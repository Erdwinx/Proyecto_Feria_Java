<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escaner de Boletos - Feria</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body class="scanner-page">
<main class="layout">
    <nav class="nav">
        <a href="/boletos">Boletos</a>
        <a class="active" href="/scanner">Escaner</a>
    </nav>

    <div class="row">
        <section class="card">
            <h1>Escaneo en puerta</h1>
            <p>Escanea el QR del boleto. Solo se puede usar una vez.</p>
            <div id="reader"></div>
        </section>

        <section class="card">
            <h1>Resultado</h1>
            <div id="resultBox" class="status">Esperando escaneo...</div>
            <p><strong>ID:</strong> <span id="resultId">-</span></p>
            <p><strong>Nombre:</strong> <span id="resultName">-</span></p>
            <p><strong>Ultimo QR:</strong></p>
            <p id="resultQr" class="mono">-</p>
        </section>
    </div>
</main>

<script>
    const resultBox = document.getElementById("resultBox");
    const resultId = document.getElementById("resultId");
    const resultName = document.getElementById("resultName");
    const resultQr = document.getElementById("resultQr");

    let lastDecoded = "";
    let lastDecodedAt = 0;
    let lastSuccessfulDecoded = "";
    let lastSuccessfulAt = 0;
    let glowTimer = null;
    let html5QrCodeScanner = null;
    let isProcessing = false;
    let connectionRetryCount = 0;
    const maxRetries = 3;
    const repeatIgnoreMs = 8000;

    function setPageGlow(ok) {
        if (glowTimer) {
            clearTimeout(glowTimer);
            glowTimer = null;
        }

        document.body.classList.toggle("scan-ok", ok);

        if (ok) {
            glowTimer = setTimeout(() => {
                document.body.classList.remove("scan-ok");
                glowTimer = null;
            }, 1800);
        }
    }

    function setStatus(ok, text, processing = false) {
        const dotClass = ok === true ? "ok" : ok === false ? "error" : "";
        resultBox.innerHTML = `<span class="status-line"><span class="status-dot ${dotClass} ${processing ? 'processing' : ''}"></span>${text}</span>`;
        resultBox.className = processing ? "status" : ok === true ? "status ok" : ok === false ? "status error" : "status";
        if (!processing && ok !== null) {
            setPageGlow(ok);
        }
    }

    function showProcessingStatus() {
        setStatus(null, "Procesando escaneo...", true);
        isProcessing = true;
        document.getElementById("reader").classList.add("processing");
    }

    function hideProcessingStatus() {
        isProcessing = false;
        document.getElementById("reader").classList.remove("processing");
    }

    function createTimeoutFetch(url, options = {}, timeout = 5000) {
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), timeout);
        const mergedOptions = Object.assign({}, options, { signal: controller.signal });
        return fetch(url, mergedOptions).finally(() => clearTimeout(timer));
    }

    function decodeBase64Url(value) {
        try {
            const base64 = value.replace(/-/g, "+").replace(/_/g, "/");
            const padded = base64.padEnd(base64.length + (4 - base64.length % 4) % 4, "=");
            const binary = atob(padded);
            const bytes = Uint8Array.from(binary, (char) => char.charCodeAt(0));
            return new TextDecoder("utf-8").decode(bytes);
        } catch (error) {
            return "";
        }
    }

    function parseTicketQr(qrText) {
        const parts = qrText.split("|");
        if (parts.length !== 6 || parts[0] !== "FERIAQR") {
            return null;
        }

        return {
            ticketId: parts[1] || "-",
            nombre: decodeBase64Url(parts[2]) || "-"
        };
    }

    async function checkConnection() {
        try {
            const response = await createTimeoutFetch(`${window.location.origin}/api/health`, { method: "GET" }, 3000);
            return response.ok;
        } catch (_error) {
            return false;
        }
    }

    async function validateScan(qrText) {
        const isConnected = await checkConnection();
        if (!isConnected) {
            setStatus(false, "Sin conexión al servidor");
            playErrorSound();
            hideProcessingStatus();
            return;
        }

        try {
            const response = await createTimeoutFetch(`${window.location.origin}/api/scan/validate`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ qrText })
            }, 5000);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            connectionRetryCount = 0;
            lastSuccessfulDecoded = qrText;
            lastSuccessfulAt = Date.now();

            setStatus(data.valid, data.message);
            resultId.textContent = data.ticketId ?? "-";
            resultName.textContent = data.nombre ?? "-";
            resultQr.textContent = qrText;

            if (data.valid) {
                playSuccessSound();
            } else {
                playErrorSound();
            }

        } catch (error) {
            console.error("Scan validation error:", error);
            connectionRetryCount++;
            lastSuccessfulDecoded = qrText;
            lastSuccessfulAt = Date.now();

            if (error.name === 'AbortError') {
                setStatus(false, "Tiempo de espera agotado - Revisa tu conexión");
            } else if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                setStatus(false, "Sin conexión a internet");
            } else if (error.message.includes('HTTP 500')) {
                setStatus(false, "Error del servidor - Intenta de nuevo");
            } else {
                setStatus(false, "Error de validación");
            }

            playErrorSound();

            if (connectionRetryCount >= maxRetries) {
                setTimeout(() => {
                    setStatus(false, "Múltiples errores - Verifica tu conexión e intenta de nuevo");
                    connectionRetryCount = 0;
                }, 3000);
            }
        } finally {
            hideProcessingStatus();
        }
    }

    function onScanSuccess(decodedText) {
        if (isProcessing) {
            return;
        }

        const now = Date.now();

        if (decodedText === lastDecoded && now - lastDecodedAt < 2000) {
            return;
        }

        if (decodedText === lastSuccessfulDecoded && now - lastSuccessfulAt < repeatIgnoreMs) {
            return;
        }

        lastDecoded = decodedText;
        lastDecodedAt = now;

        showProcessingStatus();

        validateScan(decodedText).catch(() => {
            const parsed = parseTicketQr(decodedText);
            if (parsed) {
                resultId.textContent = parsed.ticketId;
                resultName.textContent = parsed.nombre;
                resultQr.textContent = decodedText;
            } else {
                resultId.textContent = "-";
                resultName.textContent = "-";
                resultQr.textContent = decodedText;
            }
            hideProcessingStatus();
        });
    }

    function playSuccessSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = "square";

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (e) {
            // Fallback: no sound
        }
    }

    function playErrorSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 400;
            oscillator.type = "sawtooth";

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
        } catch (e) {
            // Fallback: no sound
        }
    }

    // Mobile optimizations
    function handleOrientationChange() {
        if (window.orientation === 90 || window.orientation === -90) {
            document.body.classList.add("landscape");
        } else {
            document.body.classList.remove("landscape");
        }
        // Re-init scanner on orientation change
        setTimeout(initScanner, 500);
    }

    function initScanner() {
        if (html5QrCodeScanner) {
            html5QrCodeScanner.clear();
        }

        html5QrCodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: { width: 260, height: 260 } },
            false
        );
        html5QrCodeScanner.render(onScanSuccess, () => {});
    }

    window.addEventListener("orientationchange", handleOrientationChange);
    window.addEventListener("load", handleOrientationChange);

    // Prevent zoom on double tap
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function(event) {
        const now = (new Date()).getTime();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    }, false);

    initScanner();
</script>
<script src="/js/site.js"></script>
</body>
</html>