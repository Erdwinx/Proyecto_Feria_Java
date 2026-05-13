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
        <a href="/comprar">Comprar</a>
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

        <div class="checkout-card seat-card">
            <h3>Seleccion de espacio</h3>
            <div class="seat-options" id="seatOptions">
                <button class="seat-choice active" type="button" data-seat-category="general">General</button>
                <button class="seat-choice" type="button" data-seat-category="grada">Grada</button>
                <button class="seat-choice" type="button" data-seat-category="vip">VIP</button>
            </div>
            <div class="seat-map" id="seatMapGeneral">
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
            <p id="seatSelectionInfo" class="seat-selection-info">Categoria activa: General</p>
            <div id="seatActionsContainer" class="seat-actions hidden">
                <button id="addSeatsToCart" type="button" class="add-cart-btn">Agregar al carrito</button>
            </div>
        </div>

        <div class="checkout-grid">
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
                        <label for="eventDate">Fecha del evento</label>
                        <input id="eventDate" type="date" required>
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
    const confirmBtn = document.getElementById("confirmBtn");
    const checkoutMessage = document.getElementById("checkoutMessage");
    const cartItems = document.getElementById("cartItems");
    const cartEmpty = document.getElementById("cartEmpty");
    const cartTotal = document.getElementById("cartTotal");

    const seatOptions = document.getElementById("seatOptions");
    const seatMapGeneral = document.getElementById("seatMapGeneral");
    const seatMapGrada = document.getElementById("seatMapGrada");
    const seatMapVip = document.getElementById("seatMapVip");
    const seatSelectionInfo = document.getElementById("seatSelectionInfo");

    const buyerName = document.getElementById("buyerName");
    const buyerEmail = document.getElementById("buyerEmail");
    const eventDate = document.getElementById("eventDate");

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
            return raw ? JSON.parse(raw) : [];
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

    function updateCartDisplay() {
        const items = readCart();
        const total = items.reduce((sum, item) => sum + (item.price * item.qty), 0);
        cartEmpty.style.display = items.length === 0 ? "block" : "none";
        cartItems.innerHTML = "";

        items.forEach(item => {
            const row = document.createElement("div");
            row.className = "cart-item";
            row.innerHTML = `
                <div>
                    <span>${item.name}</span>
                    <span class="cart-qty">x${item.qty}</span>
                </div>
                <span>${formatMoney(item.price * item.qty)}</span>
            `;
            cartItems.appendChild(row);
        });

        cartTotal.textContent = formatMoney(total);
    }

    let selectedSeatCategory = "general";
    let selectedSeats = [];
    const seatActionsContainer = document.getElementById("seatActionsContainer");
    const addSeatsToCart = document.getElementById("addSeatsToCart");

    function setSeatCategory(category) {
        selectedSeatCategory = category;
        selectedSeats = [];
        seatSelectionInfo.textContent = `Categoria activa: ${category.charAt(0).toUpperCase() + category.slice(1)}`;
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
        if (selectedSeatCategory === "general") {
            seatSelectionInfo.textContent = "Categoria activa: General (sin asiento asignado)";
            seatActionsContainer.classList.add("hidden");
        } else if (selectedSeats.length > 0) {
            seatSelectionInfo.textContent = `Categoria activa: ${selectedSeatCategory.toUpperCase()} - ${selectedSeats.length} asiento${selectedSeats.length > 1 ? 's' : ''} seleccionado${selectedSeats.length > 1 ? 's' : ''}: ${selectedSeats.join(", ")}`;
            seatActionsContainer.classList.remove("hidden");
        } else {
            seatSelectionInfo.textContent = `Categoria activa: ${selectedSeatCategory.toUpperCase()} - Elige tu asiento`;
            seatActionsContainer.classList.add("hidden");
        }
    }

    function initSeatSelection() {
        console.log("Inicializando selección de asientos...");
        console.log("seatOptions:", seatOptions);
        console.log("seatMapGrada:", seatMapGrada);
        console.log("seatMapVip:", seatMapVip);
        
        seatOptions.querySelectorAll(".seat-choice").forEach(button => {
            console.log("Agregando listener a botón:", button.dataset.seatCategory);
            button.addEventListener("click", () => {
                console.log("Clic en categoría:", button.dataset.seatCategory);
                setSeatCategory(button.dataset.seatCategory);
            });
        });

        // Event delegation para los asientos
        document.querySelectorAll(".seat-map").forEach(seatMap => {
            console.log("Agregando event delegation a seat-map");
            seatMap.addEventListener("click", (event) => {
                const button = event.target.closest(".seat-btn");
                console.log("Clic en:", event.target, "button:", button);
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
            if (selectedSeats.length === 0) return;
            
            selectedSeats.forEach(seatNumber => {
                const item = {
                    id: `${selectedSeatCategory}-${seatNumber}`,
                    name: `Boleto ${selectedSeatCategory.toUpperCase()} - Asiento ${seatNumber}`,
                    price: selectedSeatCategory === "vip" ? 250 : (selectedSeatCategory === "grada" ? 180 : 100)
                };
                addToCart(item, 1);
            });
            
            showMessage(`${selectedSeats.length} asiento${selectedSeats.length > 1 ? 's' : ''} agregado${selectedSeats.length > 1 ? 's' : ''} al carrito`, true);
            selectedSeats = [];
            document.querySelectorAll(".seat-map .seat-btn").forEach(btn => btn.classList.remove("selected"));
            updateSeatSelectionInfo();
        });

        setSeatCategory("general");
        updateSeatSelectionInfo();
    }

    async function loadProfile() {
        const response = await authFetch("/api/customers/me");
        if (!response.ok) {
            return;
        }
        const data = await response.json();
        buyerName.value = data.nombre || "";
        buyerEmail.value = data.email || "";
        profileName.textContent = data.nombre || "-";
        profileEmail.textContent = data.email || "-";
    }

    function getSeatSuffix() {
        if (selectedSeatCategory === "general") {
            return "[General]";
        }
        return selectedSeats.length > 0 ? `[${selectedSeatCategory.toUpperCase()} ${selectedSeats.join(", ")}]` : `[${selectedSeatCategory.toUpperCase()}]`;
    }

    async function createTicket(nombre, fechaEvento, items) {
        console.log("Creando ticket(s) con:", { nombre, fechaEvento, items });
        const response = await authFetch("/api/customers/tickets", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ nombre, fechaEvento, items })
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

    confirmBtn.addEventListener("click", async () => {
        const items = readCart();
        if (items.length === 0) {
            setMessage(checkoutMessage, false, "El carrito esta vacio");
            return;
        }

        const nombre = buyerName.value.trim();
        const fecha = eventDate.value;

        if (!nombre || !fecha) {
            setMessage(checkoutMessage, false, "Completa nombre y fecha");
            return;
        }

        try {
            const cartItemsList = readCart();
            if (cartItemsList.length === 0) {
                setMessage(checkoutMessage, false, "No hay items en el carrito");
                return;
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

    function init() {
        requireToken();
        loadProfile();
        initSeatSelection();
        updateCartDisplay();
        initEventFromUrl();
    }

    init();
</script>
<script src="/js/site.js"></script>
</body>
</html>
