<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Boletos - Feria</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
<main class="layout">
    <nav class="nav">
        <a class="active" href="/panel">Panel</a>
        <a href="/scanner">Escaner</a>
    </nav>

    <section class="card">
        <h1>Panel de boletos</h1>
        <p>Revisa el registro de escaneos y métricas del panel.</p>
        <div class="panel-stats">
            <div class="stat">
                <strong>Boletos registrados:</strong>
                <span class="stat-value">{{ $totalScannedTickets ?? 0 }}</span>
            </div>
        </div>
        <p id="selectedEventNotice" class="scan-msg" style="margin-top: 6px;"></p>

        <div class="tab-menu">
            <button id="tabRegister" type="button" class="tab-btn active">Registro</button>
        </div>

        <div id="panelRegister" class="tab-panel">
            <div class="register-actions">
                <button id="recoverBtn" type="button">Recuperar boleto</button>
                <p class="scan-msg">Clave requerida: RECUPERAR-2026</p>
            </div>
            <div class="filter-controls" style="margin-bottom: 16px; display: flex; gap: 12px; align-items: center;">
                <label for="filterDay">Filtrar por día:</label>
                <select id="filterDay">
                    <option value="">-- Todos los días --</option>
                </select>
                <label for="filterMonth" style="margin-left: 16px;">Filtrar por mes:</label>
                <select id="filterMonth">
                    <option value="">-- Todos los meses --</option>
                </select>
            </div>
            <div class="table-wrap">
                <table class="scan-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                    </tr>
                    </thead>
                    <tbody id="scanTableBody"></tbody>
                </table>
                <p id="scanEmpty" class="empty-msg">No hay registros aun.</p>
            </div>
        </div>
    </section>
</main>

<div id="ticketModal" class="modal hidden" role="dialog" aria-modal="true">
    <div class="modal-card">
        <div class="modal-head">
            <h2>Detalle del boleto</h2>
            <button id="closeModalBtn" type="button" class="close-btn">Cerrar</button>
        </div>

        <div class="ticket-rect" id="ticketRect">
            <div class="ticket-left">
                <p class="ticket-kicker">BOLETO DIGITAL FERIA</p>
                <p class="ticket-row"><strong>ID:</strong> <span id="detailId">-</span></p>
                <p class="ticket-row"><strong>Nombre:</strong> <span id="detailNombre">-</span></p>
                <p class="ticket-row"><strong>Fecha:</strong> <span id="detailFecha">-</span></p>
                <p class="ticket-row"><strong>Estado:</strong> <span id="detailEstado">-</span></p>
                <p class="ticket-row"><strong>Vigencia:</strong> <span id="detailCountdown">-</span></p>

                <div class="actions">
                    <button id="generateQrBtn" type="button">Generar QR</button>
                    <button id="checkScanBtn" type="button">Verificar escaneo</button>
                </div>

                <p id="scanCheckMessage" class="scan-msg"></p>

                <p class="mono" id="detailQrText"></p>
            </div>

            <div class="ticket-right">
                <div class="qr-box" id="qrContainer">Presiona Generar QR para mostrarlo</div>
            </div>
        </div>
    </div>
</div>

    <script>
    const tabRegister = document.getElementById("tabRegister");
    const panelRegister = document.getElementById("panelRegister");
    const filterDay = document.getElementById("filterDay");
    const filterMonth = document.getElementById("filterMonth");

    let allScanEntries = [];

    const ticketModal = document.getElementById("ticketModal");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const detailId = document.getElementById("detailId");
    const detailNombre = document.getElementById("detailNombre");
    const detailFecha = document.getElementById("detailFecha");
    const detailEstado = document.getElementById("detailEstado");
    const detailCountdown = document.getElementById("detailCountdown");
    const detailQrText = document.getElementById("detailQrText");
    const generateQrBtn = document.getElementById("generateQrBtn");
    const checkScanBtn = document.getElementById("checkScanBtn");
    const scanCheckMessage = document.getElementById("scanCheckMessage");
    const qrContainer = document.getElementById("qrContainer");
    const scanTableBody = document.getElementById("scanTableBody");
    const scanEmpty = document.getElementById("scanEmpty");
    const recoverBtn = document.getElementById("recoverBtn");
    const selectedEventNotice = document.getElementById("selectedEventNotice");

    let currentTicketId = "";
    let expiresAtMs = 0;
    let refreshTimer = null;
    let qrEnabled = false;
    let scanPollTimer = null;

    function switchTab(tab) {
        const showRegister = tab === "register";
        panelRegister.classList.toggle("hidden", !showRegister);
        tabRegister.classList.toggle("active", showRegister);
        if (showRegister) {
            loadScanLog();
        }
    }

    function resetScanCheck() {
        scanCheckMessage.textContent = "";
        scanCheckMessage.className = "scan-msg";
        checkScanBtn.classList.remove("btn-ok");
        checkScanBtn.textContent = "Verificar escaneo";
    }

    function setScanCheckStatus(scanned, text) {
        scanCheckMessage.textContent = text;
        scanCheckMessage.className = scanned ? "scan-msg ok" : "scan-msg";
        checkScanBtn.classList.toggle("btn-ok", scanned);
        checkScanBtn.textContent = scanned ? "Escaneado" : "Verificar escaneo";
    }

    function formatScanDate(epochSeconds) {
        if (!epochSeconds) {
            return { date: "-", time: "-" };
        }

        const scannedAt = new Date(epochSeconds * 1000);
        return {
            date: scannedAt.toLocaleDateString("es-CL"),
            time: scannedAt.toLocaleTimeString("es-CL", { hour: "2-digit", minute: "2-digit" })
        };
    }

    function updateFilterOptions() {
        const days = new Set();
        const months = new Set();

        allScanEntries.forEach(entry => {
            const formatted = formatScanDate(entry.scannedAtEpochSeconds);
            const dateParts = formatted.date.split("/");
            if (dateParts[0]) days.add(dateParts[0]);
            if (dateParts[1]) months.add(dateParts[1]);
        });

        const dayOptions = Array.from(days).sort();
        const monthOptions = Array.from(months).sort();

        filterDay.innerHTML = '<option value="">-- Todos los días --</option>';
        dayOptions.forEach(day => {
            const option = document.createElement("option");
            option.value = day;
            option.textContent = day;
            filterDay.appendChild(option);
        });

        filterMonth.innerHTML = '<option value="">-- Todos los meses --</option>';
        monthOptions.forEach(month => {
            const option = document.createElement("option");
            option.value = month;
            option.textContent = month;
            filterMonth.appendChild(option);
        });
    }

    function applyFilters() {
        const selectedDay = filterDay.value;
        const selectedMonth = filterMonth.value;

        scanTableBody.innerHTML = "";
        
        let filtered = allScanEntries;
        if (selectedDay) {
            filtered = filtered.filter(entry => {
                const formatted = formatScanDate(entry.scannedAtEpochSeconds);
                const dateParts = formatted.date.split("/");
                return dateParts[0] === selectedDay;
            });
        }
        if (selectedMonth) {
            filtered = filtered.filter(entry => {
                const formatted = formatScanDate(entry.scannedAtEpochSeconds);
                const dateParts = formatted.date.split("/");
                return dateParts[1] === selectedMonth;
            });
        }

        scanEmpty.style.display = filtered.length ? "none" : "block";
        renderScanLog(filtered);
    }

    function renderScanLog(entries) {
        scanTableBody.innerHTML = "";
        scanEmpty.style.display = entries.length ? "none" : "block";

        entries.forEach(entry => {
            const row = document.createElement("tr");
            const idCell = document.createElement("td");
            const nameCell = document.createElement("td");
            const dateCell = document.createElement("td");
            const timeCell = document.createElement("td");
            const formatted = formatScanDate(entry.scannedAtEpochSeconds);

            idCell.textContent = entry.ticketId ?? "-";
            nameCell.textContent = entry.nombre ?? "-";
            dateCell.textContent = formatted.date;
            timeCell.textContent = formatted.time;

            row.appendChild(idCell);
            row.appendChild(nameCell);
            row.appendChild(dateCell);
            row.appendChild(timeCell);
            scanTableBody.appendChild(row);
        });
    }

    async function loadScanLog() {
        const response = await fetch("/api/scans");
        if (!response.ok) {
            scanTableBody.innerHTML = "";
            scanEmpty.textContent = "No se pudo cargar el registro";
            scanEmpty.style.display = "block";
            return;
        }

        allScanEntries = await response.json();
        allScanEntries = allScanEntries.sort((a, b) => (b.scannedAtEpochSeconds || 0) - (a.scannedAtEpochSeconds || 0));
        scanEmpty.textContent = "No hay registros aun.";
        updateFilterOptions();
        applyFilters();
    }

    function stopScanPolling() {
        if (scanPollTimer) {
            clearInterval(scanPollTimer);
            scanPollTimer = null;
        }
    }

    async function pollScanStatus() {
        if (!currentTicketId) {
            return;
        }

        const response = await fetch(`/api/tickets/${encodeURIComponent(currentTicketId)}`);
        if (!response.ok) {
            return;
        }

        const ticket = await response.json();
        if (ticket.escaneado) {
            applyTicketState(ticket);
            if (!panelRegister.classList.contains("hidden")) {
                loadScanLog();
            }
            stopScanPolling();
        }
    }

    function startScanPolling() {
        stopScanPolling();
        scanPollTimer = setInterval(pollScanStatus, 2000);
    }

    async function fetchTicketById(ticketId) {
        const response = await fetch(`/api/tickets/${encodeURIComponent(ticketId)}`);
        if (!response.ok) {
            return null;
        }

        return await response.json();
    }

    async function recoverTicket() {
        const ticketId = prompt("ID del boleto a recuperar:");
        if (!ticketId) {
            return;
        }

        const key = prompt("Clave de recuperacion:");
        if (!key) {
            return;
        }

        const response = await fetch("/api/scan/recover", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ ticketId: ticketId.trim(), key: key.trim() })
        });

        if (!response.ok) {
            alert("No se pudo recuperar el boleto");
            return;
        }

        await response.json();
        alert("Boleto recuperado");
        await loadScanLog();
    }

    function resetQrBox() {
        if (refreshTimer) {
            clearTimeout(refreshTimer);
        }
        qrEnabled = false;
        expiresAtMs = 0;
        detailCountdown.textContent = "-";
        qrContainer.textContent = "Presiona Generar QR para mostrarlo";
        detailQrText.textContent = "";
    }

    function applyTicketState(ticket) {
        detailId.textContent = ticket.id;
        detailNombre.textContent = ticket.nombre;
        detailFecha.textContent = ticket.fechaEvento;

        if (ticket.escaneado) {
            detailEstado.innerHTML = "<span class='status-inline'><span class='status-dot ok'></span>Escaneado</span>";
            generateQrBtn.disabled = true;
            qrContainer.textContent = "Este boleto ya fue escaneado y no genera QR";
            detailQrText.textContent = "";
            detailCountdown.textContent = "-";
            qrEnabled = false;
            if (refreshTimer) {
                clearTimeout(refreshTimer);
            }
            setScanCheckStatus(true, "Escaneado con exito");
            stopScanPolling();
            return;
        }

        detailEstado.textContent = "Disponible";
        generateQrBtn.disabled = false;
        resetQrBox();
        resetScanCheck();
    }

    function renderQr(text) {
        qrContainer.innerHTML = "";
        new QRCode(qrContainer, {
            text,
            width: 240,
            height: 240
        });
        detailQrText.textContent = text;
    }

    function updateCountdown() {
        if (!qrEnabled) {
            detailCountdown.textContent = "-";
            return;
        }

        if (!expiresAtMs) {
            detailCountdown.textContent = "Sin vencimiento";
            return;
        }

        const remainingSec = Math.max(0, Math.floor((expiresAtMs - Date.now()) / 1000));
        const min = String(Math.floor(remainingSec / 60)).padStart(2, "0");
        const sec = String(remainingSec % 60).padStart(2, "0");
        detailCountdown.textContent = `${min}:${sec}`;
    }

    function scheduleRefresh() {
        if (refreshTimer) {
            clearTimeout(refreshTimer);
        }
        if (!qrEnabled || !expiresAtMs) {
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
                const updated = await fetchTicketById(currentTicketId);
                if (updated) {
                    applyTicketState(updated);
                }
            } else {
                qrContainer.textContent = "No se pudo generar el QR";
            }
            return;
        }

        const data = await response.json();
        expiresAtMs = data.expiresAtEpochSeconds ? data.expiresAtEpochSeconds * 1000 : 0;
        renderQr(data.qrText);
        updateCountdown();
        scheduleRefresh();
    }

    function openTicket(ticket) {
        if (!ticket) {
            return;
        }

        currentTicketId = ticket.id;
        applyTicketState(ticket);
        ticketModal.classList.remove("hidden");
        startScanPolling();
    }

    generateQrBtn.addEventListener("click", async () => {
        if (!currentTicketId) {
            return;
        }

        qrEnabled = true;
        await fetchCurrentQr();
    });

    checkScanBtn.addEventListener("click", async () => {
        if (!currentTicketId) {
            return;
        }

        const response = await fetch(`/api/tickets/${encodeURIComponent(currentTicketId)}`);
        if (!response.ok) {
            setScanCheckStatus(false, "No se pudo verificar el escaneo");
            return;
        }

        const ticket = await response.json();
        if (ticket.escaneado) {
            applyTicketState(ticket);
            return;
        }

        setScanCheckStatus(false, "Aun no se ha escaneado");
    });

    closeModalBtn.addEventListener("click", () => {
        ticketModal.classList.add("hidden");
        resetQrBox();
        resetScanCheck();
        stopScanPolling();
    });

    ticketModal.addEventListener("click", (event) => {
        if (event.target === ticketModal) {
            ticketModal.classList.add("hidden");
            resetQrBox();
            resetScanCheck();
            stopScanPolling();
        }
    });

    tabRegister.addEventListener("click", () => switchTab("register"));
    recoverBtn.addEventListener("click", recoverTicket);
    filterDay.addEventListener("change", applyFilters);
    filterMonth.addEventListener("change", applyFilters);

    function init() {
        const params = new URLSearchParams(window.location.search);
        const selectedEvent = params.get("selectedEvent") || params.get("event");
        if (selectedEvent) {
            selectedEventNotice.textContent = `Evento seleccionado: ${selectedEvent.replace(/-/g, " ")}`;
        }
    }

    init();
</script>
<script src="/js/site.js"></script>
</body>
</html>
