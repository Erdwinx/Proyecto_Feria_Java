<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feria - Comprar</title>
    <link rel="stylesheet" href="/css/app.css">
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
        <h2 class="section-title">Finalizar compra</h2>
        <p class="section-lead">Completa tus datos, selecciona tu categoria y escoge asiento si eliges VIP o grada.</p>

        <div class="checkout-card" style="margin-bottom: 1rem;">
            <div id="eventHeader" style="margin-bottom: 1.5rem;">
                <h2 id="eventTitle" style="font-size: 2rem; margin: 0 0 0.5rem 0; color: var(--accent);">-</h2>
                <p id="eventSubtitle" style="margin: 0; color: var(--muted); font-size: 0.95rem;">Elige una fecha para comprar</p>
            </div>
            <select id="eventDate" required style="display: none;">
                <option value="">Selecciona una fecha</option>
            </select>
            <div class="date-chip-section">
                <p class="seat-selection-info" id="availableDatesInfo">Fechas disponibles para este boleto.</p>
                <div id="eventDatesList" class="date-chip-list"></div>
            </div>
            <p class="seat-selection-info" id="dateSelectionInfo">Elige una fecha para ver las categorías y asientos disponibles.</p>
        </div>

        <div id="fairCard" class="checkout-card hidden" style="margin-bottom: 1rem;">
            <h3>Boleto de feria</h3>
            <p id="fairTicketTitle" class="section-lead" style="margin-bottom: 0.5rem;"></p>
            <div class="form-grid">
                <div>
                    <label for="fairQty">Cantidad</label>
                    <input id="fairQty" type="number" min="1" value="1">
                </div>
                <div>
                    <label>Vigencia</label>
                    <div class="seat-selection-info">Este boleto expira en 7 días si no finalizas la compra.</div>
                </div>
            </div>
            <button id="fairAddToCart" type="button" class="add-cart-btn">Agregar boleto al carrito</button>
        </div>

        <div id="seatCard" class="checkout-card seat-card hidden">
            <h3>Seleccion de espacio</h3>
            <div class="seat-options hidden" id="seatOptions">
                <button class="seat-choice active" type="button" data-seat-category="general">General</button>
                <button class="seat-choice" type="button" data-seat-category="grada">Grada</button>
                <button class="seat-choice" type="button" data-seat-category="vip">VIP</button>
            </div>
            <div class="seat-map hidden" id="seatMapGeneral">
                <p>General no tiene asiento asignado. Compra tu boleto y accede al area general.</p>
            </div>
            <div class="seat-map hidden" id="seatMapGrada">
                <div class="screen-label">ESCENARIO</div>
                <div class="seat-grid">
                    <button class="seat-btn" type="button" data-seat-number="A1">A1</button>
                    <button class="seat-btn" type="button" data-seat-number="A2">A2</button>
                    <button class="seat-btn" type="button" data-seat-number="A3">A3</button>
                    <button class="seat-btn" type="button" data-seat-number="A4">A4</button>
                    <button class="seat-btn" type="button" data-seat-number="A5">A5</button>
                    <button class="seat-btn" type="button" data-seat-number="A6">A6</button>
                    <button class="seat-btn" type="button" data-seat-number="A7">A7</button>
                    <button class="seat-btn" type="button" data-seat-number="A8">A8</button>
                    <button class="seat-btn" type="button" data-seat-number="A9">A9</button>
                    <button class="seat-btn" type="button" data-seat-number="A10">A10</button>
                    <button class="seat-btn" type="button" data-seat-number="A11">A11</button>
                    <button class="seat-btn" type="button" data-seat-number="A12">A12</button>
                    <button class="seat-btn" type="button" data-seat-number="B1">B1</button>
                    <button class="seat-btn" type="button" data-seat-number="B2">B2</button>
                    <button class="seat-btn" type="button" data-seat-number="B3">B3</button>
                    <button class="seat-btn" type="button" data-seat-number="B4">B4</button>
                    <button class="seat-btn" type="button" data-seat-number="B5">B5</button>
                    <button class="seat-btn" type="button" data-seat-number="B6">B6</button>
                    <button class="seat-btn" type="button" data-seat-number="B7">B7</button>
                    <button class="seat-btn" type="button" data-seat-number="B8">B8</button>
                    <button class="seat-btn" type="button" data-seat-number="B9">B9</button>
                    <button class="seat-btn" type="button" data-seat-number="B10">B10</button>
                    <button class="seat-btn" type="button" data-seat-number="B11">B11</button>
                    <button class="seat-btn" type="button" data-seat-number="B12">B12</button>
                    <button class="seat-btn" type="button" data-seat-number="C1">C1</button>
                    <button class="seat-btn" type="button" data-seat-number="C2">C2</button>
                    <button class="seat-btn" type="button" data-seat-number="C3">C3</button>
                    <button class="seat-btn" type="button" data-seat-number="C4">C4</button>
                    <button class="seat-btn" type="button" data-seat-number="C5">C5</button>
                    <button class="seat-btn" type="button" data-seat-number="C6">C6</button>
                    <button class="seat-btn" type="button" data-seat-number="C7">C7</button>
                    <button class="seat-btn" type="button" data-seat-number="C8">C8</button>
                    <button class="seat-btn" type="button" data-seat-number="C9">C9</button>
                    <button class="seat-btn" type="button" data-seat-number="C10">C10</button>
                    <button class="seat-btn" type="button" data-seat-number="C11">C11</button>
                    <button class="seat-btn" type="button" data-seat-number="C12">C12</button>
                </div>
            </div>
            <div class="seat-map hidden" id="seatMapVip">
                <div class="screen-label">ESCENARIO</div>
                <div class="seat-grid">
                    <button class="seat-btn" type="button" data-seat-number="V1">V1</button>
                    <button class="seat-btn" type="button" data-seat-number="V2">V2</button>
                    <button class="seat-btn" type="button" data-seat-number="V3">V3</button>
                    <button class="seat-btn" type="button" data-seat-number="V4">V4</button>
                    <button class="seat-btn" type="button" data-seat-number="V5">V5</button>
                    <button class="seat-btn" type="button" data-seat-number="V6">V6</button>
                    <button class="seat-btn disabled" type="button" disabled data-seat-number="V7">V7</button>
                    <button class="seat-btn disabled" type="button" disabled data-seat-number="V8">V8</button>
                    <button class="seat-btn" type="button" data-seat-number="V9">V9</button>
                    <button class="seat-btn" type="button" data-seat-number="V10">V10</button>
                    <button class="seat-btn" type="button" data-seat-number="V11">V11</button>
                    <button class="seat-btn" type="button" data-seat-number="V12">V12</button>
                </div>
                <p class="seat-note">Los asientos desactivados ya no estan disponibles.</p>
            </div>
            <p id="seatSelectionInfo" class="seat-selection-info">Selecciona una fecha para continuar.</p>
            <div id="seatActionsContainer" class="seat-actions hidden">
                <button id="addSeatsToCart" type="button" class="add-cart-btn">Agregar al carrito</button>
            </div>
        </div>

        <div id="checkoutPanel" class="hidden checkout-drawer">
        <div class="checkout-grid checkout-drawer-inner">
            <form id="checkoutForm" class="checkout-card">
                <div class="form-grid">
                    <div>
                        <label for="buyerName">Nombre completo</label>
                        <input id="buyerName" type="text" maxlength="80" required>
                    </div>
                    <div>
                        <label for="buyerEmail">Correo</label>
                        <input id="buyerEmail" type="email" maxlength="120" required>
                    </div>
                    <div>
                        <label for="buyerPhone">Telefono</label>
                        <input id="buyerPhone" type="tel" maxlength="20" required>
                    </div>
                    <div>
                        <label for="paymentMethod">Metodo de pago</label>
                        <select id="paymentMethod" required>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="efectivo">Efectivo en taquilla</option>
                        </select>
                    </div>
                    <div>
                        <label for="cardNumber">Numero de tarjeta</label>
                        <input id="cardNumber" type="text" inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000" required>
                    </div>
                    <div>
                        <label for="cardExpiry">Vencimiento</label>
                        <input id="cardExpiry" type="text" maxlength="5" placeholder="MM/AA" required>
                    </div>
                    <div>
                        <label for="cardCvv">CVV</label>
                        <input id="cardCvv" type="password" inputmode="numeric" maxlength="4" required>
                    </div>
                    <div>
                        <label for="billingAddress">Direccion de facturacion</label>
                        <input id="billingAddress" type="text" maxlength="120" placeholder="Calle y numero" required>
                    </div>
                    <div>
                        <label for="billingCity">Ciudad</label>
                        <input id="billingCity" type="text" maxlength="80" required>
                    </div>
                </div>
            </form>

            <div class="checkout-card">
                <button id="closeCheckoutBtn" type="button" class="close-checkout-btn">Cerrar carrito</button>
                <h3>Tu carrito</h3>
                <div id="cartItems" class="cart-items"></div>
                <p id="cartEmpty" class="empty-msg">Tu carrito esta vacio.</p>
                <div class="cart-summary">
                    <span>Total</span>
                    <span id="cartTotal">$0 MXN</span>
                </div>
                <button id="confirmBtn" type="button">Confirmar compra</button>
                <p id="checkoutMessage" class="create-msg"></p>
            </div>
        </div>
        </div>
    </section>
</main>

<script>
    const tokenKey = "feriaAuthToken";
    const cartKey = "feriaCart";
    const profileName = document.getElementById("profileName");
    const profileEmail = document.getElementById("profileEmail");
    const profileWrap = document.getElementById("profileWrap");
    const profileButton = document.getElementById("profileButton");
    const logoutBtn = document.getElementById("logoutBtn");
    const checkoutForm = document.getElementById("checkoutForm");
    const closeCheckoutBtn = document.getElementById("closeCheckoutBtn");
    const confirmBtn = document.getElementById("confirmBtn");
    const checkoutMessage = document.getElementById("checkoutMessage");
    const cartItems = document.getElementById("cartItems");
    const cartEmpty = document.getElementById("cartEmpty");
    const cartTotal = document.getElementById("cartTotal");
    const dateSelectionInfo = document.getElementById("dateSelectionInfo");
    const availableDatesInfo = document.getElementById("availableDatesInfo");
    const eventDatesList = document.getElementById("eventDatesList");
    const eventTitle = document.getElementById("eventTitle");
    const eventSubtitle = document.getElementById("eventSubtitle");

    const seatOptions = document.getElementById("seatOptions");
    const seatMapGeneral = document.getElementById("seatMapGeneral");
    const seatMapGrada = document.getElementById("seatMapGrada");
    const seatMapVip = document.getElementById("seatMapVip");
    const seatSelectionInfo = document.getElementById("seatSelectionInfo");
    const checkoutPanel = document.getElementById("checkoutPanel");
    const seatCard = document.getElementById("seatCard");
    const fairCard = document.getElementById("fairCard");
    const fairTicketTitle = document.getElementById("fairTicketTitle");
    const fairQty = document.getElementById("fairQty");
    const fairAddToCart = document.getElementById("fairAddToCart");

    const buyerName = document.getElementById("buyerName");
    const buyerEmail = document.getElementById("buyerEmail");
    const eventDate = document.getElementById("eventDate");

    const seatCatalog = {
        grada: [
            "A1", "A2", "A3", "A4", "A5", "A6", "A7", "A8", "A9", "A10", "A11", "A12",
            "B1", "B2", "B3", "B4", "B5", "B6", "B7", "B8", "B9", "B10", "B11", "B12",
            "C1", "C2", "C3", "C4", "C5", "C6", "C7", "C8", "C9", "C10", "C11", "C12"
        ],
        vip: ["V1", "V2", "V3", "V4", "V5", "V6", "V7", "V8", "V9", "V10", "V11", "V12"],
        general: []
    };

    let eventsList = [];
    let eventsLoaded = false;
    let eventsLoading = false;
    let selectedTicketType = 'concierto';
    let selectedEventKey = null;
    let currentAvailability = { soldSeats: [], availableSeats: [] };
    const eventDatePresets = {
        'concierto-central': ['2026-05-10', '2026-05-14', '2026-05-18'],
        'ritmo-urbano': ['2026-06-15', '2026-06-21', '2026-06-28'],
        'electro-fest': ['2026-06-19', '2026-06-23', '2026-06-27'],
        'noche-de-pop': ['2026-06-17', '2026-06-20', '2026-06-24'],
        'feria': ['2026-05-11', '2026-05-12', '2026-05-13', '2026-05-17'],
    };

    function slugify(value) {
        return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function getPurchaseParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            event: params.get('event'),
            selectedEvent: params.get('selectedEvent'),
            ticketType: params.get('ticketType'),
            ticket: params.get('ticket'),
        };
    }

    function applyPurchaseMode() {
        const params = getPurchaseParams();
        selectedTicketType = params.ticketType === 'feria' ? 'feria' : 'concierto';
        selectedEventKey = params.selectedEvent || params.event;

        if (selectedTicketType === 'feria') {
            seatCard.classList.add('hidden');
            fairCard.classList.remove('hidden');
            fairTicketTitle.textContent = params.ticket ? `Boleto ${params.ticket}` : 'Boleto de feria';
            seatOptions.classList.add('hidden');
            seatActionsContainer.classList.add('hidden');
            dateSelectionInfo.textContent = 'Boleto de feria: solo selecciona cantidad y agrégalo al carrito.';
            return;
        }

        fairCard.classList.add('hidden');
        seatCard.classList.remove('hidden');
    }

    async function loadEvents() {
        if (eventsLoaded || eventsLoading) {
            return;
        }

        eventsLoading = true;

        try {
            const res = await fetch('/api/events');
            if (!res.ok) return;
            const rawEvents = await res.json();

            const params = getPurchaseParams();
            const selectedKey = params.event || selectedEventKey;
            const scopedEvents = selectedKey
                ? rawEvents.filter((ev) => {
                    const eventKey = ev.event_key || slugify(ev.nombre);
                    return eventKey === selectedKey || slugify(ev.nombre) === slugify(selectedKey);
                })
                : rawEvents;

            const presetDates = selectedKey && eventDatePresets[selectedKey] ? eventDatePresets[selectedKey] : [];
            const fallbackEvents = presetDates.map((dateValue) => ({
                id: `${selectedKey || 'event'}-${dateValue}`,
                event_key: selectedKey || 'event',
                nombre: selectedKey ? selectedKey.replace(/-/g, ' ') : 'Evento',
                tipo_evento: selectedTicketType,
                fecha_evento: dateValue,
            }));

            const seenDates = new Set();
            const sourceEvents = scopedEvents.length ? scopedEvents : fallbackEvents;
            eventsList = sourceEvents.filter((ev) => {
                if (!ev || !ev.fecha_evento || seenDates.has(ev.fecha_evento)) {
                    return false;
                }
                seenDates.add(ev.fecha_evento);
                return true;
            });

            const sel = document.getElementById('eventDate');
            sel.replaceChildren();

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Selecciona una fecha';
            sel.appendChild(placeholder);

            eventsList.forEach(ev => {
                const opt = document.createElement('option');
                opt.value = ev.fecha_evento;
                opt.text = `${ev.fecha_evento} - ${ev.nombre}`;
                sel.appendChild(opt);
            });

            renderAvailableDates();

            // Update the event header
            if (eventTitle && eventsList.length > 0) {
                const eventName = eventsList[0].nombre || 'Evento';
                eventTitle.textContent = eventName;
                eventSubtitle.textContent = 'Elige una fecha para comprar';
            }

            if (selectedTicketType === 'concierto' && eventsList.length > 0) {
                sel.value = eventsList[0].fecha_evento;
                dateSelectionInfo.textContent = `Evento seleccionado: ${eventsList[0].nombre}`;
            }

            applyPurchaseMode();

            // If we arrived with a selectedEvent, open the seat selection and refresh availability
            const arrivalKey = selectedEventKey || params.event;
            if (arrivalKey && eventsList.length > 0) {
                // ensure UI shows seat selection and loads availability
                try {
                    await refreshAvailabilityForCurrentSelection();
                } catch (e) {
                    console.warn('Error refreshing availability for selected event', e);
                }
                // scroll the seat card into view so user sees the boleto section
                setTimeout(() => {
                    seatCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 120);
            }

            eventsLoaded = true;
        } catch (e) {
            console.error('Could not load events', e);
        } finally {
            eventsLoading = false;
        }
    }

    function setCheckoutPanelVisible(visible) {
        checkoutPanel.classList.toggle("hidden", !visible);
    }

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
            localStorage.removeItem(cartKey);
            window.location.href = "/login";
            throw new Error("No autenticado");
        }
        return response;
    }

    function setMessage(target, ok, text) {
        target.textContent = text;
        target.className = ok ? "create-msg ok" : "create-msg error";
    }

    function readCart() {
        try {
            const raw = localStorage.getItem(cartKey);
            const items = raw ? JSON.parse(raw) : [];
            if (!Array.isArray(items)) {
                return [];
            }

            return items.map((item, index) => ({
                id: item.id ?? `cart-${index}`,
                name: item.name || 'Boleto',
                category: item.category || 'general',
                seatNumbers: Array.isArray(item.seatNumbers) ? item.seatNumbers : [],
                dateEvento: item.dateEvento || '',
                eventName: item.eventName || '',
                price: Number.isFinite(Number(item.price)) ? Number(item.price) : 0,
                qty: Math.max(1, parseInt(item.qty, 10) || 1),
                tipoEvento: item.tipoEvento || '',
            }));
        } catch (error) {
            return [];
        }
    }

    function writeCart(items) {
        localStorage.setItem(cartKey, JSON.stringify(items));
        updateCartDisplay();
    }

    function addToCart(item, qty) {
        const items = readCart();
        const existing = items.find(entry => entry.id === item.id);
        if (existing) {
            existing.qty += qty;
        } else {
            items.push({ ...item, qty });
        }
        writeCart(items);
    }

    function showMessage(text, ok = true) {
        const msg = document.createElement("div");
        msg.className = ok ? "create-msg ok" : "create-msg error";
        msg.textContent = text;
        msg.style.position = "fixed";
        msg.style.bottom = "20px";
        msg.style.right = "20px";
        msg.style.zIndex = "9999";
        document.body.appendChild(msg);
        
        setTimeout(() => {
            msg.remove();
        }, 2000);
    }

    function formatMoney(amount) {
        const formatter = new Intl.NumberFormat("es-MX", {
            style: "currency",
            currency: "MXN"
        });
        return formatter.format(amount);
    }

    function removeFromCart(itemId) {
        let items = readCart();
        items = items.filter(item => item.id !== itemId);
        writeCart(items);
        showMessage('Item eliminado del carrito', true);
    }

    function updateCartDisplay() {
        const items = readCart();
        const total = items.reduce((sum, item) => sum + (item.price * item.qty), 0);
        cartEmpty.style.display = items.length === 0 ? "block" : "none";
        cartItems.innerHTML = "";

        items.forEach(item => {
            const row = document.createElement("div");
            row.className = "cart-item";
            row.style.display = "grid";
            row.style.gridTemplateColumns = "minmax(0, 1fr) auto auto";
            row.style.columnGap = "10px";
            row.style.alignItems = "center";
            row.style.padding = "0.7rem 0.5rem";
            
            const infoDiv = document.createElement("div");
            infoDiv.style.flex = "1";
            infoDiv.style.minWidth = "0";
            infoDiv.style.display = "flex";
            infoDiv.style.flexDirection = "column";
            infoDiv.style.gap = "0.15rem";
            infoDiv.innerHTML = `
                <div style="margin-bottom: 0.25rem;">
                    <strong>${item.name}</strong>
                </div>
                ${item.dateEvento ? `<div style="font-size: 0.85rem; color: var(--muted);">📅 ${item.dateEvento}</div>` : ''}
                ${item.eventName ? `<div style="font-size: 0.85rem; color: var(--muted);">${item.eventName}</div>` : ''}
                <span class="cart-qty">x${item.qty} · ${item.category}</span>
            `;
            
            const priceDiv = document.createElement("div");
            priceDiv.style.whiteSpace = "nowrap";
            priceDiv.style.fontWeight = "700";
            priceDiv.textContent = formatMoney(item.price * item.qty);
            
            const removeBtn = document.createElement("button");
            removeBtn.type = "button";
            removeBtn.textContent = "🗑️";
            removeBtn.title = "Eliminar del carrito";
            removeBtn.style.background = "none";
            removeBtn.style.border = "none";
            removeBtn.style.cursor = "pointer";
            removeBtn.style.fontSize = "1.2em";
            removeBtn.style.padding = "0 0 0 5px";
            removeBtn.addEventListener("click", () => removeFromCart(item.id));
            
            row.appendChild(infoDiv);
            row.appendChild(priceDiv);
            row.appendChild(removeBtn);
            cartItems.appendChild(row);
        });

        cartTotal.textContent = formatMoney(total);
    }

    function renderAvailableDates() {
        if (!eventDatesList || !availableDatesInfo) {
            return;
        }

        const selectedKey = selectedEventKey || getPurchaseParams().event || getPurchaseParams().selectedEvent;
        const dates = eventsList.map((ev) => ev.fecha_evento).filter(Boolean);

        eventDatesList.innerHTML = '';

        if (!dates.length) {
            availableDatesInfo.textContent = selectedTicketType === 'feria'
                ? 'Boleto de feria sin fecha.'
                : 'No hay fechas disponibles para este evento.';
            eventDatesList.classList.add('hidden');
            return;
        }

        availableDatesInfo.textContent = selectedKey
            ? `Fechas disponibles para ${selectedKey.replace(/-/g, ' ')}`
            : 'Fechas disponibles';
        eventDatesList.classList.remove('hidden');

        dates.forEach((dateValue, index) => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'date-chip';
            chip.textContent = dateValue;
            chip.dataset.date = dateValue;
            if (index === 0) {
                chip.classList.add('active');
            }

            chip.addEventListener('click', async () => {
                eventDate.value = dateValue;
                document.querySelectorAll('.date-chip').forEach((node) => node.classList.toggle('active', node.dataset.date === dateValue));
                updateEventTypeUI();
            });

            eventDatesList.appendChild(chip);
        });
    }

    function isConcertDate(dateValue) {
        return selectedTicketType === 'concierto' && eventsList.some(ev => ev.fecha_evento === dateValue && ev.tipo_evento === 'concierto');
    }

    function getAllowedCategories(dateValue) {
        return ["general", "grada", "vip"];
    }

    async function loadSeatAvailability(dateValue, category) {
        if (!dateValue || category === "general") {
            currentAvailability = { soldSeats: [], availableSeats: [] };
            return;
        }

        const token = localStorage.getItem(tokenKey);
        if (!token) {
            currentAvailability = { soldSeats: [], availableSeats: [] };
            return;
        }

        try {
            const response = await authFetch(`/api/tickets/available-seats?fechaEvento=${encodeURIComponent(dateValue)}&category=${encodeURIComponent(category)}`);
            if (!response.ok) {
                throw new Error("No se pudo cargar la disponibilidad de asientos");
            }

            currentAvailability = await response.json();
        } catch (error) {
            currentAvailability = { soldSeats: [], availableSeats: [] };
        }
    }

    function refreshSeatButtons() {
        const soldSeats = new Set(currentAvailability.soldSeats || []);
        document.querySelectorAll(".seat-map .seat-btn").forEach(button => {
            const seatNumber = button.dataset.seatNumber;
            if (!seatNumber) {
                return;
            }

            const isSold = soldSeats.has(seatNumber);
            button.disabled = isSold;
            button.classList.toggle("disabled", isSold);
            button.classList.toggle("sold", isSold);
            if (isSold) {
                button.title = "Asiento no disponible";
                button.classList.remove("selected");
            } else {
                button.title = "";
            }
        });
    }

    let selectedSeatCategory = "general";
    let selectedSeats = [];
    const seatActionsContainer = document.getElementById("seatActionsContainer");
    const addSeatsToCart = document.getElementById("addSeatsToCart");

    function setSeatCategory(category) {
        selectedSeatCategory = category;
        selectedSeats = [];
        const selectedDate = eventDate.value;
        const isConcert = isConcertDate(selectedDate);
        seatSelectionInfo.textContent = isConcert && category !== "general"
            ? `Concierto: selecciona tus asientos ${category.toUpperCase()} disponibles.`
            : `Categoria activa: ${category.charAt(0).toUpperCase() + category.slice(1)}`;
        seatMapGeneral.classList.toggle("hidden", category !== "general");
        seatMapGrada.classList.toggle("hidden", category !== "grada");
        seatMapVip.classList.toggle("hidden", category !== "vip");
        seatOptions.querySelectorAll(".seat-choice").forEach(btn => {
            btn.classList.toggle("active", btn.dataset.seatCategory === category);
        });
        document.querySelectorAll(".seat-map .seat-btn").forEach(btn => {
            btn.classList.remove("selected");
        });
        updateSeatSelectionInfo();
    }

    function updateSeatSelectionInfo() {
        const selectedDate = eventDate.value;
        if (!selectedDate) {
            seatSelectionInfo.textContent = "Selecciona una fecha para continuar.";
            seatActionsContainer.classList.add("hidden");
            addSeatsToCart.disabled = true;
            return;
        }

        if (selectedSeatCategory === "general") {
            seatSelectionInfo.textContent = "Categoria activa: General (sin asiento asignado)";
            seatActionsContainer.classList.remove("hidden");
            addSeatsToCart.disabled = false;
        } else if (selectedSeats.length > 0) {
            seatSelectionInfo.textContent = `Categoria activa: ${selectedSeatCategory.toUpperCase()} - ${selectedSeats.length} asiento${selectedSeats.length > 1 ? 's' : ''} seleccionado${selectedSeats.length > 1 ? 's' : ''}: ${selectedSeats.join(", ")}`;
            seatActionsContainer.classList.remove("hidden");
            addSeatsToCart.disabled = false;
        } else {
            seatSelectionInfo.textContent = `Categoria activa: ${selectedSeatCategory.toUpperCase()} - Elige tu asiento`;
            seatActionsContainer.classList.add("hidden");
            addSeatsToCart.disabled = true;
        }
    }

    function initSeatSelection() {
        seatOptions.querySelectorAll(".seat-choice").forEach(button => {
            button.addEventListener("click", async () => {
                if (button.disabled) {
                    return;
                }

                await setCategoryAndReload(button.dataset.seatCategory);
            });
        });

        document.querySelectorAll(".seat-map").forEach(seatMap => {
            seatMap.addEventListener("click", (event) => {
                const button = event.target.closest(".seat-btn");
                if (!button || button.disabled || button.classList.contains("disabled")) return;

                const seatNumber = button.dataset.seatNumber;
                const index = selectedSeats.indexOf(seatNumber);

                if (index > -1) {
                    selectedSeats.splice(index, 1);
                    button.classList.remove("selected");
                } else {
                    selectedSeats.push(seatNumber);
                    button.classList.add("selected");
                }

                updateSeatSelectionInfo();
            });
        });

        addSeatsToCart.addEventListener("click", () => {
            if (selectedSeatCategory !== "general" && selectedSeats.length === 0) return;

            const eventValue = eventDate.value;
            const eventName = eventsList.find(ev => ev.fecha_evento === eventValue)?.nombre || 'Evento';
            const tipoEvento = isConcertDate(eventValue) ? 'concierto' : 'feria';
            const item = {
                id: `${eventValue}-${selectedSeatCategory}-${selectedSeats.join(',')}-${Math.random()}`,
                name: selectedSeatCategory === "general"
                    ? `Boleto GENERAL` 
                    : `Boleto ${selectedSeatCategory.toUpperCase()} - Asientos ${selectedSeats.join(", ")}`,
                category: selectedSeatCategory,
                seatNumbers: selectedSeatCategory === "general" ? [] : selectedSeats.slice(),
                dateEvento: eventValue,
                eventName: eventName,
                tipoEvento: tipoEvento,
                price: selectedSeatCategory === "vip" ? 250 * selectedSeats.length : (selectedSeatCategory === "grada" ? 180 * selectedSeats.length : 100),
                qty: 1,
            };
            addToCart(item, 1);

            showMessage(`${selectedSeatCategory === "general" ? 1 : selectedSeats.length} boleto${selectedSeats.length > 1 ? 's' : ''} agregado${selectedSeats.length > 1 ? 's' : ''} al carrito`, true);
            selectedSeats = [];
            document.querySelectorAll(".seat-map .seat-btn").forEach(btn => btn.classList.remove("selected"));
            updateSeatSelectionInfo();
        });

        seatOptions.classList.add("hidden");
        seatMapGeneral.classList.add("hidden");
        seatMapGrada.classList.add("hidden");
        seatMapVip.classList.add("hidden");
        seatActionsContainer.classList.add("hidden");
        addSeatsToCart.disabled = true;
        seatSelectionInfo.textContent = "Selecciona una fecha para continuar.";
    }

    async function setCategoryAndReload(category) {
        selectedSeats = [];
        selectedSeatCategory = category;
        seatOptions.querySelectorAll(".seat-choice").forEach(btn => {
            btn.classList.toggle("active", btn.dataset.seatCategory === category);
        });

        seatMapGeneral.classList.toggle("hidden", category !== "general");
        seatMapGrada.classList.toggle("hidden", category !== "grada");
        seatMapVip.classList.toggle("hidden", category !== "vip");

        await refreshAvailabilityForCurrentSelection();
        updateSeatSelectionInfo();
    }

    async function refreshAvailabilityForCurrentSelection() {
        const selectedDate = eventDate.value;
        if (!selectedDate) {
            seatOptions.classList.add("hidden");
            seatSelectionInfo.textContent = "Selecciona una fecha para continuar.";
            return;
        }

        document.querySelectorAll('.date-chip').forEach((node) => {
            node.classList.toggle('active', node.dataset.date === selectedDate);
        });

        const isConcert = isConcertDate(selectedDate);
        seatOptions.classList.remove("hidden");

        const allowedCategories = getAllowedCategories(selectedDate);
        seatOptions.querySelectorAll(".seat-choice").forEach(button => {
            const isAllowed = allowedCategories.includes(button.dataset.seatCategory);
            button.disabled = !isAllowed;
            button.classList.toggle("hidden", !isAllowed);
        });

        const nextCategory = allowedCategories.includes(selectedSeatCategory)
            ? selectedSeatCategory
            : (allowedCategories[0] ?? "general");
        if (nextCategory !== selectedSeatCategory) {
            selectedSeatCategory = nextCategory;
        }

        if (selectedSeatCategory === "general") {
            seatMapGeneral.classList.remove("hidden");
            seatMapGrada.classList.add("hidden");
            seatMapVip.classList.add("hidden");
            currentAvailability = { soldSeats: [], availableSeats: [] };
            refreshSeatButtons();
            return;
        }

        await loadSeatAvailability(selectedDate, selectedSeatCategory);
        refreshSeatButtons();

        seatMapGeneral.classList.toggle("hidden", selectedSeatCategory !== "general");
        seatMapGrada.classList.toggle("hidden", selectedSeatCategory !== "grada");
        seatMapVip.classList.toggle("hidden", selectedSeatCategory !== "vip");
    }

    async function loadProfile() {
        const token = localStorage.getItem(tokenKey);
        if (!token) {
            return;
        }

        try {
            const response = await authFetch("/api/customers/me");
            if (!response.ok) {
                return;
            }
            const data = await response.json();
            buyerName.value = data.nombre || "";
            buyerEmail.value = data.email || "";
            profileName.textContent = data.nombre || "-";
            profileEmail.textContent = data.email || "-";
        } catch (error) {
            // Leave the purchase UI usable even if profile data is unavailable.
            return;
        }
    }

    function getSeatSuffix() {
        if (selectedSeatCategory === "general") {
            return "[General]";
        }
        return selectedSeats.length > 0 ? `[${selectedSeatCategory.toUpperCase()} ${selectedSeats.join(", ")}]` : `[${selectedSeatCategory.toUpperCase()}]`;
    }

    async function createTicket(nombre, fechaEvento, items) {
        // Sanitizar items para asegurar que tienen todos los campos requeridos
        const sanitizedItems = items.map(item => ({
            name: item.name || 'Boleto',
            category: item.category || 'general',
            seatNumbers: item.seatNumbers || [],
            price: parseFloat(item.price) || 0,
            qty: parseInt(item.qty) || 1
        }));
        
        console.log("Creando ticket(s) con:", { nombre, fechaEvento, items: sanitizedItems });
        const response = await authFetch("/api/customers/tickets", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ nombre, fechaEvento, items: sanitizedItems })
        });
        console.log("Respuesta del servidor:", response.status);
        if (!response.ok) {
            const errorData = await response.text();
            console.error("Error response:", response.status, errorData);
            throw new Error(errorData || "Error en el servidor");
        }
        const result = await response.json();
        console.log("Ticket creado:", result);
        return result;
    }

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
        localStorage.removeItem(cartKey);
        window.location.href = "/login";
    });

    if (closeCheckoutBtn) {
        closeCheckoutBtn.addEventListener('click', () => {
            checkoutPanel.classList.add('hidden');
        });
    }

    confirmBtn.addEventListener("click", async () => {
        const items = readCart();
        if (items.length === 0) {
            setMessage(checkoutMessage, false, "El carrito esta vacio");
            return;
        }

        const nombre = buyerName.value.trim();
        const fecha = selectedTicketType === 'feria'
            ? new Date().toISOString().slice(0, 10)
            : eventDate.value;

        if (!nombre) {
            setMessage(checkoutMessage, false, "Completa tu nombre");
            return;
        }

        if (selectedTicketType !== 'feria' && !fecha) {
            setMessage(checkoutMessage, false, "Selecciona una fecha");
            return;
        }

        // Validar campos de pago
        const cardNumber = document.getElementById("cardNumber").value.trim();
        const cardExpiry = document.getElementById("cardExpiry").value.trim();
        const cardCvv = document.getElementById("cardCvv").value.trim();
        const billingAddress = document.getElementById("billingAddress").value.trim();
        const billingCity = document.getElementById("billingCity").value.trim();

        if (!cardNumber || !cardExpiry || !cardCvv || !billingAddress || !billingCity) {
            setMessage(checkoutMessage, false, "Completa todos los datos de pago y facturacion");
            return;
        }

        try {
            const cartItemsList = readCart();
            if (cartItemsList.length === 0) {
                setMessage(checkoutMessage, false, "No hay items en el carrito");
                return;
            }
            
            // Validar que todos los items tengan los campos requeridos
            for (const item of cartItemsList) {
                if (!item.name) {
                    setMessage(checkoutMessage, false, "Item sin nombre");
                    return;
                }
                if (!item.category) {
                    setMessage(checkoutMessage, false, "Item sin categoría");
                    return;
                }
                if (item.price === undefined || item.price === null) {
                    setMessage(checkoutMessage, false, "Item sin precio");
                    return;
                }
                if (!item.qty || item.qty < 1) {
                    setMessage(checkoutMessage, false, "Item sin cantidad");
                    return;
                }
            }
            
            const ticketResult = await createTicket(nombre, fecha, cartItemsList);
            if (ticketResult) {
                localStorage.removeItem(cartKey);
                updateCartDisplay();
                setMessage(checkoutMessage, true, "Compra confirmada!");
                setTimeout(() => window.location.href = "/mis-boletos", 1200);
            }
        } catch (error) {
            console.error("Error al procesar compra:", error);
            setMessage(checkoutMessage, false, error.message || "Error al procesar");
        }
    });

    function getQueryParam(name) {
        const params = new URLSearchParams(window.location.search);
        return params.get(name);
    }

    function initEventFromUrl() {
        const eventKey = getQueryParam('event');
        if (eventKey) {
            // Aquí puedes agregar lógica para mostrar información del evento seleccionado
            console.log('Evento seleccionado:', eventKey);
            // Por ejemplo, podrías agregar un mensaje o preseleccionar algo
        }
    }

    function updateEventTypeUI() {
        if (selectedTicketType === 'feria') {
            seatCard.classList.add("hidden");
            fairCard.classList.remove("hidden");
            dateSelectionInfo.textContent = "Boleto de feria: solo selecciona cantidad y agrégalo al carrito.";
            return;
        }

        const selectedDate = eventDate.value;
        const isConcert = isConcertDate(selectedDate);

        dateSelectionInfo.textContent = selectedDate
            ? (isConcert ? "Fecha de concierto seleccionada. Puedes elegir General, Grada o VIP." : "Fecha de feria seleccionada. Puedes elegir General, Grada o VIP.")
            : "Elige una fecha para ver las categorías y asientos disponibles.";

        if (!selectedDate) {
            seatCard.classList.add("hidden");
            seatOptions.classList.add("hidden");
            seatMapGeneral.classList.add("hidden");
            seatMapGrada.classList.add("hidden");
            seatMapVip.classList.add("hidden");
            seatActionsContainer.classList.add("hidden");
            addSeatsToCart.disabled = true;
            seatSelectionInfo.textContent = "Selecciona una fecha para continuar.";
            return;
        }

        seatCard.classList.remove("hidden");
        seatOptions.classList.remove("hidden");

        if (isConcert) {
            seatOptions.querySelectorAll(".seat-choice").forEach(btn => {
                btn.disabled = false;
                btn.classList.remove("hidden");
            });
            setCategoryAndReload(['general', 'grada', 'vip'].includes(selectedSeatCategory) ? selectedSeatCategory : 'general');
        } else {
            seatOptions.querySelectorAll(".seat-choice").forEach(btn => {
                btn.disabled = false;
                btn.classList.remove("hidden");
            });
            setCategoryAndReload('general');
        }
    }

    eventDate.addEventListener('change', updateEventTypeUI);

    if (fairAddToCart) {
        fairAddToCart.addEventListener('click', () => {
            const qty = Math.max(1, parseInt(fairQty.value, 10) || 1);
            const ticketName = fairTicketTitle.textContent || 'Boleto de feria';
            const selectedDate = eventDate.value || (eventsList[0]?.fecha_evento || '');
            const eventName = eventsList.find(ev => ev.fecha_evento === selectedDate)?.nombre || 'Feria Local';
            addToCart({
                id: `fair-${slugify(ticketName)}-${selectedDate}`,
                name: ticketName,
                price: 100,
                category: 'general',
                seatNumbers: [],
                dateEvento: selectedDate,
                eventName: eventName,
                tipoEvento: 'feria',
                qty: 1
            }, qty);
            fairQty.value = 1;
            showMessage(`${qty} boleto${qty > 1 ? 's' : ''} de feria agregado${qty > 1 ? 's' : ''} al carrito`, true);
        });
    }

    async function init() {
        setCheckoutPanelVisible(false);
        applyPurchaseMode();
        await loadEvents();
        loadProfile();
        initSeatSelection();
        updateCartDisplay();
        initEventFromUrl();
        updateEventTypeUI(); // Inicializar estado del evento
    }

    init();
</script>
<script src="/js/site.js"></script>
</body>
</html>
