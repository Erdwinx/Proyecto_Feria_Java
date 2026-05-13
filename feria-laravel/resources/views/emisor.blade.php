<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emisor de Boletos - Feria</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
<main class="layout">
    <nav class="nav">
        <a class="active" href="/emisor">Emisor</a>
        <a href="/scanner">Escaner</a>
    </nav>

    <section class="card">
        <h1>Boletos con QR dinamico</h1>
        <p>El QR cambia automaticamente cada 15 segundos y esta ligado al nombre + ID del boleto.</p>

        <label for="ticketSelect">Selecciona un boleto</label>
        <select id="ticketSelect">
            <option value="">Cargando boletos...</option>
        </select>

        <div class="actions">
            <button id="generateTicketBtn" type="button">Generar boleto</button>
        </div>
    </section>

    <section class="card ticket-rect hidden" id="ticketBox">
        <div class="ticket-left">
            <h2 class="ticket-title">Boleto</h2>
            <p class="ticket-row"><strong>ID:</strong> <span id="ticketId">-</span></p>
            <p class="ticket-row"><strong>Nombre:</strong> <span id="ticketName">-</span></p>
            <p class="ticket-row"><strong>Estado:</strong> <span id="ticketStatus">-</span></p>
            <p class="ticket-row"><strong>Vence en:</strong> <span id="countdown">-</span></p>
            <div class="actions">
                <button id="generateQrBtn" type="button">Generar QR</button>
            </div>
            <p class="mono" id="qrTextPreview"></p>
        </div>

        <div class="ticket-right">
            <div class="qr-box" id="qrContainer">Presiona Generar QR para mostrarlo</div>
        </div>
    </section>
</main>

<script>
    const select = document.getElementById("ticketSelect");
    const generateTicketBtn = document.getElementById("generateTicketBtn");
    const generateQrBtn = document.getElementById("generateQrBtn");
    const ticketBox = document.getElementById("ticketBox");
    const ticketId = document.getElementById("ticketId");
    const ticketName = document.getElementById("ticketName");
    const ticketStatus = document.getElementById("ticketStatus");
    const qrContainer = document.getElementById("qrContainer");
    const countdown = document.getElementById("countdown");
    const qrTextPreview = document.getElementById("qrTextPreview");

    let tickets = [];
    let currentTicketId = "";
    let expiresAtMs = 0;
    let qrEnabled = false;
    let refreshTimer = null;

    async function loadTickets(selectedIdToKeep = "") {
        const response = await fetch("/api/tickets");
        tickets = await response.json();

        select.innerHTML = "<option value=''>Selecciona...</option>";
        tickets.forEach(ticket => {
            const option = document.createElement("option");
            option.value = ticket.id;
            const status = ticket.escaneado ? " (Escaneado)" : "";
            option.textContent = `${ticket.id} - ${ticket.nombre}${status}`;
            select.appendChild(option);
        });

        if (selectedIdToKeep) {
            select.value = selectedIdToKeep;
        }
    }

    function renderQr(text) {
        qrContainer.innerHTML = "";
        new QRCode(qrContainer, {
            text,
            width: 280,
            height: 280
        });
        qrTextPreview.textContent = text;
    }

    function updateCountdown() {
        if (!expiresAtMs) {
            countdown.textContent = "-";
            return;
        }

        const remainingSec = Math.max(0, Math.floor((expiresAtMs - Date.now()) / 1000));
        const min = String(Math.floor(remainingSec / 60)).padStart(2, "0");
        const sec = String(remainingSec % 60).padStart(2, "0");
        countdown.textContent = `${min}:${sec}`;
    }

    function scheduleRefresh() {
        if (refreshTimer) {
            clearTimeout(refreshTimer);
        }
        if (!qrEnabled) {
            return;
        }
        const delay = Math.max(1000, expiresAtMs - Date.now() + 100);
        refreshTimer = setTimeout(fetchCurrentQr, delay);
    }

    async function fetchCurrentQr() {
        if (!currentTicketId || !qrEnabled) {
            return;
        }

        const response = await fetch(`/api/tickets/${encodeURIComponent(currentTicketId)}/current-qr`);
        if (!response.ok) {
            if (response.status === 409) {
                await loadTickets(currentTicketId);
                const updatedTicket = tickets.find(ticket => ticket.id === currentTicketId);
                if (updatedTicket && updatedTicket.escaneado) {
                    applyTicketState(updatedTicket);
                    return;
                }
            }

            qrContainer.textContent = "No se pudo generar el QR";
            return;
        }

        const data = await response.json();
        expiresAtMs = data.expiresAtEpochSeconds * 1000;
        renderQr(data.qrText);
        updateCountdown();
        scheduleRefresh();
    }

    function resetQrDisplay() {
        qrEnabled = false;
        if (refreshTimer) {
            clearTimeout(refreshTimer);
        }
        expiresAtMs = 0;
        qrContainer.textContent = "Presiona Generar QR para mostrarlo";
        qrTextPreview.textContent = "";
        countdown.textContent = "-";
    }

    function applyTicketState(selectedTicket) {
        const scanned = Boolean(selectedTicket.escaneado);
        if (scanned) {
            ticketStatus.innerHTML = "<span class='status-inline'><span class='status-dot ok'></span>Escaneado</span>";
            generateQrBtn.disabled = true;
            qrContainer.textContent = "Este boleto ya fue escaneado y no genera QR";
            qrTextPreview.textContent = "";
            countdown.textContent = "-";
            expiresAtMs = 0;
            qrEnabled = false;
            if (refreshTimer) {
                clearTimeout(refreshTimer);
            }
            return;
        }

        ticketStatus.innerHTML = "Disponible";
        generateQrBtn.disabled = false;
        resetQrDisplay();
    }

    generateTicketBtn.addEventListener("click", async () => {
        const selectedId = select.value;
        if (!selectedId) {
            alert("Selecciona un boleto para continuar");
            return;
        }

        await loadTickets(selectedId);

        const selectedTicket = tickets.find(ticket => ticket.id === selectedId);
        if (!selectedTicket) {
            alert("No se encontro el boleto seleccionado");
            return;
        }

        currentTicketId = selectedTicket.id;
        ticketId.textContent = selectedTicket.id;
        ticketName.textContent = selectedTicket.nombre;
        ticketBox.classList.remove("hidden");
        applyTicketState(selectedTicket);
    });

    generateQrBtn.addEventListener("click", async () => {
        if (!currentTicketId) {
            alert("Primero genera el boleto");
            return;
        }

        qrEnabled = true;
        await fetchCurrentQr();
    });

    async function init() {
        await loadTickets();
        setInterval(updateCountdown, 1000);
    }

    init();
</script>
<script src="/js/site.js"></script>
</body>
</html>