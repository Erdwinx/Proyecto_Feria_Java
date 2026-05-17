<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feria - Mis boletos</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
<header class="topbar">
    <div class="brand">FeriaPass</div>
    <nav class="topnav">
        <a href="/inicio">Inicio</a>
        <a href="/eventos">Eventos</a>
        <a href="/noticias">Noticias</a>
        <a href="/promociones">Promociones</a>
    </nav>
    <div class="profile-wrap" id="profileWrap">
        <button id="profileButton" class="profile-button" type="button" aria-expanded="false" aria-controls="profileDrawer">
            <span class="profile-avatar">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.4 0-8 2.2-8 5v1h16v-1c0-2.8-3.6-5-8-5Z"></path>
                </svg>
            </span>
        </button>
        <aside class="profile-drawer" id="profileDrawer">
            <div class="profile-header">
                <div class="profile-avatar">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.4 0-8 2.2-8 5v1h16v-1c0-2.8-3.6-5-8-5Z"></path>
                    </svg>
                </div>
                <div>
                    <p class="profile-name" id="profileName">-</p>
                    <p class="profile-email" id="profileEmail">-</p>
                </div>
            </div>
            <a class="profile-link" href="/mis-boletos">Mis boletos</a>
            <button class="profile-logout" id="logoutBtn" type="button">Cerrar sesion</button>
        </aside>
    </div>
</header>

<main class="layout wide">
    <section class="section-band">
        <h2 class="section-title">Mis boletos</h2>
        <p class="section-lead">Selecciona un boleto activo para ver el QR. Los boletos escaneados o expirados aparecen aparte.</p>

        <div class="tickets-summary">
            <div class="tickets-summary-item">
                <span class="tickets-summary-label">Activos</span>
                <strong id="activeTicketsCount">0</strong>
            </div>
            <div class="tickets-summary-item">
                <span class="tickets-summary-label">Usados / expirados</span>
                <strong id="usedTicketsCount">0</strong>
            </div>
            <div class="tickets-summary-item">
                <span class="tickets-summary-label">Total</span>
                <strong id="totalTicketsCount">0</strong>
            </div>
        </div>

        <div class="tickets-group tickets-section-card">
            <div class="tickets-group-head tickets-section-head">
                <h3>Activos</h3>
                <span class="tickets-badge" id="activeTicketsBadge">0</span>
            </div>
            <p class="section-lead section-lead-tight">Boletos que todavía puedes usar.</p>
            <div id="myTicketsGrid" class="tickets-grid"></div>
            <p id="ticketEmpty" class="empty-msg"></p>
        </div>

        <div class="tickets-group tickets-section-card tickets-section-card-muted">
            <div class="tickets-group-head tickets-section-head">
                <h3>Usados o expirados</h3>
                <span class="tickets-badge tickets-badge-muted" id="usedTicketsBadge">0</span>
            </div>
            <p class="section-lead section-lead-tight">Boletos ya escaneados. Si se recuperan en el panel, volverán a activos.</p>
            <div id="usedTicketsGrid" class="tickets-grid"></div>
            <p id="usedTicketEmpty" class="empty-msg"></p>
        </div>
    </section>
</main>

<div id="ticketModal" class="modal hidden" role="dialog" aria-modal="true">
    <div class="modal-card">
        <div class="modal-head">
            <h2>Mi boleto</h2>
            <button id="closeModalBtn" type="button" class="close-btn">Cerrar</button>
        </div>

        <div class="ticket-rect" id="ticketRect">
            <div class="ticket-left">
                <p class="ticket-kicker" id="ticketKicker">BOLETO DIGITAL FERIA</p>
                <p class="ticket-row"><strong>ID:</strong> <span id="detailId">-</span></p>
                <p class="ticket-row"><strong>Nombre:</strong> <span id="detailNombre">-</span></p>
                <p class="ticket-row"><strong>Fecha:</strong> <span id="detailFecha">-</span></p>
                <p class="ticket-row"><strong>Estado:</strong> <span id="detailEstado">-</span></p>
                <p id="packageInfoRow" class="ticket-row hidden"><strong>Paquete:</strong> <span id="detailPackageName">-</span></p>
                <p id="seatInfoRow" class="ticket-row hidden"><strong>Asientos:</strong> <span id="detailSeatNumbers">-</span></p>
                <div id="packageTicketsRow" class="ticket-row hidden">
                    <strong>Boletos del paquete:</strong>
                    <ul id="packageTicketsList" style="margin-top: 5px; padding-left: 20px; font-size: 0.9em;"></ul>
                </div>

                <div class="actions">
                    <button id="generateQrBtn" type="button">Generar QR</button>
                    <button id="recoverBtn" type="button" style="display:none; background-color: #ff9800; margin-left: 10px;">Recuperar boleto</button>
                </div>

                <p class="mono" id="detailQrText"></p>
            </div>

            <div class="ticket-right">
                <div class="qr-box" id="qrContainer">Presiona Generar QR para mostrarlo</div>
            </div>
        </div>
    </div>
</div>

<div id="recoverModal" class="modal hidden" role="dialog" aria-modal="true">
    <div class="modal-card">
        <div class="modal-head">
            <h2>Recuperar boleto</h2>
            <button id="closeRecoverModalBtn" type="button" class="close-btn">Cerrar</button>
        </div>
        <div style="padding: 20px;">
            <p>Ingresa la clave de recuperación para recuperar este boleto.</p>
            <div style="margin-top: 15px;">
                <label for="recoverKey" style="display: block; margin-bottom: 8px;">Clave de recuperación</label>
                <input id="recoverKey" type="password" maxlength="50" placeholder="Ingresa la clave" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <button id="confirmRecoverBtn" type="button" style="flex: 1; padding: 10px; background-color: #ff9800; color: white; border: none; border-radius: 4px; cursor: pointer;">Recuperar</button>
                <button id="cancelRecoverBtn" type="button" style="flex: 1; padding: 10px; background-color: #ccc; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
            </div>
            <p id="recoverMessage" style="margin-top: 10px; text-align: center; color: red;"></p>
        </div>
    </div>
</div>

<script>
    const tokenKey = "feriaAuthToken";
    const profileName = document.getElementById("profileName");
    const profileEmail = document.getElementById("profileEmail");
    const profileWrap = document.getElementById("profileWrap");
    const profileButton = document.getElementById("profileButton");
    const logoutBtn = document.getElementById("logoutBtn");

    const myTicketsGrid = document.getElementById("myTicketsGrid");
    const ticketEmpty = document.getElementById("ticketEmpty");
    const usedTicketsGrid = document.getElementById("usedTicketsGrid");
    const usedTicketEmpty = document.getElementById("usedTicketEmpty");
    const activeTicketsCount = document.getElementById("activeTicketsCount");
    const usedTicketsCount = document.getElementById("usedTicketsCount");
    const totalTicketsCount = document.getElementById("totalTicketsCount");
    const activeTicketsBadge = document.getElementById("activeTicketsBadge");
    const usedTicketsBadge = document.getElementById("usedTicketsBadge");
    const ticketModal = document.getElementById("ticketModal");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const detailId = document.getElementById("detailId");
    const detailNombre = document.getElementById("detailNombre");
    const detailFecha = document.getElementById("detailFecha");
    const detailEstado = document.getElementById("detailEstado");
    const detailQrText = document.getElementById("detailQrText");
    const generateQrBtn = document.getElementById("generateQrBtn");
    const recoverBtn = document.getElementById("recoverBtn");
    const qrContainer = document.getElementById("qrContainer");
    const ticketKicker = document.getElementById("ticketKicker");
    const packageInfoRow = document.getElementById("packageInfoRow");
    const detailPackageName = document.getElementById("detailPackageName");
    const seatInfoRow = document.getElementById("seatInfoRow");
    const detailSeatNumbers = document.getElementById("detailSeatNumbers");
    const packageTicketsRow = document.getElementById("packageTicketsRow");
    const packageTicketsList = document.getElementById("packageTicketsList");

    const recoverModal = document.getElementById("recoverModal");
    const closeRecoverModalBtn = document.getElementById("closeRecoverModalBtn");
    const recoverKey = document.getElementById("recoverKey");
    const confirmRecoverBtn = document.getElementById("confirmRecoverBtn");
    const cancelRecoverBtn = document.getElementById("cancelRecoverBtn");
    const recoverMessage = document.getElementById("recoverMessage");

    let tickets = [];
    let currentTicketId = "";
    let refreshInterval = null;

    function requireToken() {
        const token = localStorage.getItem(tokenKey);
        if (!token) {
            window.location.href = "/login";
            return null;
        }
        return token;
    }

    async function authFetch(url, options = {}) {
        const token = requireToken();
        if (!token) {
            throw new Error("Sin token");
        }

        const headers = Object.assign({}, options.headers || {}, {
            Authorization: `Bearer ${token}`
        });

        const response = await fetch(url, Object.assign({}, options, { headers }));
        if (response.status === 401) {
            localStorage.removeItem(tokenKey);
            localStorage.removeItem("feriaCustomer");
            window.location.href = "/login";
            throw new Error("No autenticado");
        }
        return response;
    }

    async function loadProfile() {
        const response = await authFetch("/api/customers/me");
        if (!response.ok) {
            return;
        }
        const data = await response.json();
        profileName.textContent = data.nombre || "-";
        profileEmail.textContent = data.email || "-";
    }

    async function loadMyTickets() {
        const response = await authFetch("/api/customers/tickets");
        if (!response.ok) {
            myTicketsGrid.innerHTML = "";
            usedTicketsGrid.innerHTML = "";
            ticketEmpty.textContent = "No se pudieron cargar los boletos.";
            usedTicketEmpty.textContent = "";
            activeTicketsCount.textContent = "0";
            usedTicketsCount.textContent = "0";
            totalTicketsCount.textContent = "0";
            activeTicketsBadge.textContent = "0";
            usedTicketsBadge.textContent = "0";
            return;
        }

        tickets = await response.json();
        if (!tickets.length) {
            myTicketsGrid.innerHTML = "";
            usedTicketsGrid.innerHTML = "";
            ticketEmpty.textContent = "Aun no tienes boletos.";
            usedTicketEmpty.textContent = "";
            activeTicketsCount.textContent = "0";
            usedTicketsCount.textContent = "0";
            totalTicketsCount.textContent = "0";
            activeTicketsBadge.textContent = "0";
            usedTicketsBadge.textContent = "0";
            return;
        }

        const activeTickets = tickets.filter(ticket => !ticket.escaneado);
        const usedTickets = tickets.filter(ticket => ticket.escaneado);

        myTicketsGrid.innerHTML = "";
        usedTicketsGrid.innerHTML = "";

        activeTicketsCount.textContent = String(activeTickets.length);
        usedTicketsCount.textContent = String(usedTickets.length);
        totalTicketsCount.textContent = String(tickets.length);
        activeTicketsBadge.textContent = String(activeTickets.length);
        usedTicketsBadge.textContent = String(usedTickets.length);

        ticketEmpty.textContent = activeTickets.length ? "" : "No tienes boletos activos.";
        usedTicketEmpty.textContent = usedTickets.length ? "" : "No tienes boletos usados o expirados.";

        activeTickets.forEach(ticket => {
            const card = document.createElement("button");
            card.type = "button";
            card.className = "ticket-card";
            card.innerHTML = `
                <p><strong>ID:</strong> ${ticket.id}</p>
                <p><strong>Nombre:</strong> ${ticket.nombre}</p>
                <p><strong>Fecha:</strong> ${ticket.fechaEvento}</p>
                <p style="margin-top: 0.5rem; margin-bottom: 0;">
                    <strong>Evento:</strong> ${ticket.nombreEvento || "Evento"} 
                </p>
                <p style="margin: 0;">
                    <strong>Fecha:</strong> ${ticket.fechaEvento}
                </p>
                <p style="margin-top: 0.5rem;">
                    <strong>Estado:</strong> <span class="status-indicator available">Disponible</span>
                </p>
            `;
            card.addEventListener("click", () => openTicket(ticket.id));
            myTicketsGrid.appendChild(card);
        });

        usedTickets.forEach(ticket => {
            const card = document.createElement("button");
            card.type = "button";
            card.className = "ticket-card ticket-card-used";
            card.innerHTML = `
                <p><strong>ID:</strong> ${ticket.id}</p>
                <p><strong>Nombre:</strong> ${ticket.nombre}</p>
                <p><strong>Fecha:</strong> ${ticket.fechaEvento}</p>
                <p style="margin-top: 0.5rem; margin-bottom: 0;">
                    <strong>Evento:</strong> ${ticket.nombreEvento || "Evento"} 
                </p>
                <p style="margin: 0;">
                    <strong>Fecha:</strong> ${ticket.fechaEvento}
                </p>
                <p style="margin-top: 0.5rem;">
                    <strong>Estado:</strong> <span class="status-indicator scanned">Usado / expirado</span>
                </p>
            `;
            card.addEventListener("click", () => openTicket(ticket.id));
            usedTicketsGrid.appendChild(card);
        });
    }

    function openTicket(ticketId) {
        const ticket = tickets.find(item => item.id === ticketId);
        if (!ticket) {
            return;
        }

        currentTicketId = ticket.id;
        detailId.textContent = ticket.id;
        detailNombre.textContent = ticket.nombre;
        detailFecha.textContent = ticket.fechaEvento;
        detailEstado.textContent = ticket.escaneado ? "Escaneado" : "Disponible";
        detailQrText.textContent = "";
        qrContainer.textContent = "Presiona Generar QR para mostrarlo";
        generateQrBtn.disabled = ticket.escaneado;
        
        // Mostrar botón de recuperación solo si está escaneado
        recoverBtn.style.display = ticket.escaneado ? "block" : "none";

        // Actualizar tipo de evento y información de paquete
        if (ticket.tipoEvento === 'concierto' && ticket.packageId) {
            ticketKicker.textContent = "BOLETO DIGITAL CONCIERTO - PAQUETE";
            packageInfoRow.classList.remove("hidden");
            detailPackageName.textContent = ticket.packageName || "-";

            packageTicketsRow.classList.add("hidden");
            if (ticket.seatNumbers && ticket.seatNumbers.length > 0) {
                seatInfoRow.classList.remove("hidden");
                detailSeatNumbers.textContent = ticket.seatNumbers.join(', ');
            } else {
                seatInfoRow.classList.add("hidden");
                detailSeatNumbers.textContent = "-";
            }
        } else {
            ticketKicker.textContent = "BOLETO DIGITAL FERIA";
            packageInfoRow.classList.add("hidden");
            seatInfoRow.classList.add("hidden");
            packageTicketsRow.classList.add("hidden");
        }

        ticketModal.classList.remove("hidden");

        // Start polling for status updates
        startStatusPolling();
    }

    async function startStatusPolling() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }

        refreshInterval = setInterval(async () => {
            if (!currentTicketId || ticketModal.classList.contains("hidden")) {
                clearInterval(refreshInterval);
                refreshInterval = null;
                return;
            }

            try {
                const response = await authFetch("/api/customers/tickets");
                if (response.ok) {
                    const updatedTickets = await response.json();
                    const currentTicket = updatedTickets.find(t => t.id === currentTicketId);
                    if (currentTicket) {
                        const wasScanned = tickets.find(t => t.id === currentTicketId)?.escaneado;
                        if (currentTicket.escaneado !== wasScanned) {
                            // Status changed - update UI immediately
                            detailEstado.textContent = currentTicket.escaneado ? "Escaneado" : "Disponible";
                            generateQrBtn.disabled = currentTicket.escaneado;
                            if (currentTicket.escaneado) {
                                qrContainer.textContent = "Boleto ya escaneado";
                                detailQrText.textContent = "";
                            }
                            // Update global tickets array
                            tickets = tickets.map(t => t.id === currentTicketId ? currentTicket : t);
                            // Update grid
                            loadMyTickets();
                        }
                    }
                }
            } catch (error) {
                // Silent fail for polling
            }
        }, 3000); // Poll every 3 seconds
    }

    async function generateQr() {
        if (!currentTicketId) {
            return;
        }

        generateQrBtn.disabled = true;
        generateQrBtn.textContent = "Generando...";

        try {
            const response = await fetch(`/api/tickets/${encodeURIComponent(currentTicketId)}/current-qr`);
            if (!response.ok) {
                qrContainer.textContent = "No se pudo generar el QR";
                return;
            }

            const data = await response.json();
            qrContainer.innerHTML = "";
            new QRCode(qrContainer, {
                text: data.qrText,
                width: 240,
                height: 240
            });
            detailQrText.textContent = data.qrText;
        } catch (error) {
            qrContainer.textContent = "Error al generar QR";
        } finally {
            generateQrBtn.disabled = false;
            generateQrBtn.textContent = "Generar QR";
        }
    }

    generateQrBtn.addEventListener("click", generateQr);

    async function recoverTicket() {
        if (!currentTicketId || !recoverKey.value.trim()) {
            recoverMessage.textContent = "Ingresa la clave de recuperación";
            recoverMessage.style.color = "red";
            return;
        }

        confirmRecoverBtn.disabled = true;
        confirmRecoverBtn.textContent = "Recuperando...";

        try {
            const response = await fetch("/api/scan/recover", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ key: recoverKey.value, ticketId: currentTicketId })
            });

            if (!response.ok) {
                const error = await response.json();
                recoverMessage.textContent = error.message || "Error al recuperar boleto";
                recoverMessage.style.color = "red";
                return;
            }

            recoverMessage.textContent = "¡Boleto recuperado!";
            recoverMessage.style.color = "green";

            // Cerrar modal y recargar boletos
            setTimeout(() => {
                recoverModal.classList.add("hidden");
                ticketModal.classList.add("hidden");
                recoverKey.value = "";
                recoverMessage.textContent = "";
                loadMyTickets();
            }, 1500);
        } catch (error) {
            recoverMessage.textContent = "Error al recuperar boleto";
            recoverMessage.style.color = "red";
        } finally {
            confirmRecoverBtn.disabled = false;
            confirmRecoverBtn.textContent = "Recuperar";
        }
    }

    recoverBtn.addEventListener("click", () => {
        recoverModal.classList.remove("hidden");
        recoverKey.value = "";
        recoverMessage.textContent = "";
    });

    closeRecoverModalBtn.addEventListener("click", () => {
        recoverModal.classList.add("hidden");
        recoverKey.value = "";
        recoverMessage.textContent = "";
    });

    cancelRecoverBtn.addEventListener("click", () => {
        recoverModal.classList.add("hidden");
        recoverKey.value = "";
        recoverMessage.textContent = "";
    });

    confirmRecoverBtn.addEventListener("click", recoverTicket);

    recoverKey.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            recoverTicket();
        }
    });


    closeModalBtn.addEventListener("click", () => {
        ticketModal.classList.add("hidden");
        qrContainer.textContent = "Presiona Generar QR para mostrarlo";
        detailQrText.textContent = "";
    });

    profileButton.addEventListener("click", (event) => {
        event.stopPropagation();
        const isOpen = profileWrap.classList.toggle("is-open");
        profileButton.setAttribute("aria-expanded", String(isOpen));
    });

    document.addEventListener("click", (event) => {
        if (!profileWrap.contains(event.target)) {
            profileWrap.classList.remove("is-open");
            profileButton.setAttribute("aria-expanded", "false");
        }
    });

    logoutBtn.addEventListener("click", () => {
        localStorage.removeItem(tokenKey);
        localStorage.removeItem("feriaCustomer");
        localStorage.removeItem("feriaCart");
        window.location.href = "/login";
    });

    function init() {
        requireToken();
        loadProfile();
        loadMyTickets();
    }

    init();
</script>
<script src="/js/site.js"></script>
</body>
</html>
