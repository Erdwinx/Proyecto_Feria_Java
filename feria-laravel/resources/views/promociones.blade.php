<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feria - Promociones</title>
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
        <h2 class="section-title">Promociones</h2>
        <p class="section-lead">Elige tu boleto general de feria, define la cantidad y agregalo al carrito.</p>
        <div class="promo-grid">
            <article class="promo-card" data-id="feria-general" data-name="Boleto general de feria" data-price="100">
                <div class="promo-top">
                    <span class="promo-tag">Entrada general</span>
                    <span class="promo-price">$100 MXN</span>
                </div>
                <h3>Boleto general de feria</h3>
                <p>Acceso a todas las zonas generales de la feria. Este boleto expira en 7 dias si no finalizas la compra.</p>
                <div class="promo-actions">
                    <div class="qty-field">
                        <label class="qty-label" for="qty-feria-general">Cantidad</label>
                        <input id="qty-feria-general" class="qty-input" type="number" min="1" value="1">
                    </div>
                    <button class="add-cart-btn" type="button" data-add>Agregar al carrito</button>
                </div>
            </article>
        </div>

        <section class="section-band alt">
            <h2 class="section-title">Boletos de concierto</h2>
            <p class="section-lead">Selecciona tu concierto y verás el boleto destacado en promociones.</p>
            <div class="promo-carousel" data-carousel>
                <button class="carousel-arrow carousel-prev" type="button" data-carousel-prev aria-label="Concierto anterior">
                    <span aria-hidden="true">‹</span>
                </button>
                <div class="promo-carousel-viewport">
                    <div class="promo-event-grid promo-carousel-track" id="eventTicketGrid">
                        <article class="promo-event-card" data-event-key="concierto-central" data-event-name="Concierto central" data-event-price="220">
                            <div class="promo-event-image" style="background-image: linear-gradient(135deg, rgba(27, 146, 224, 0.85), rgba(74, 58, 204, 0.85)), url('/images/concierto1.jpg');"></div>
                            <div class="promo-event-body">
                                <span class="promo-event-tag">Concierto</span>
                                <h3 class="promo-event-title">Concierto central</h3>
                                <p class="promo-event-description">Ana Vega y banda en vivo. Duracion 90 min. Escenario central.</p>
                            </div>
                            <div class="promo-event-footer">
                                <button class="event-select-btn" type="button" data-select-event="concierto-central">Seleccionar boleto</button>
                            </div>
                        </article>
                        <article class="promo-event-card" data-event-key="ritmo-urbano" data-event-name="Ritmo Urbano" data-event-price="180">
                            <div class="promo-event-image" style="background-image: linear-gradient(135deg, rgba(145, 52, 199, 0.85), rgba(59, 130, 246, 0.85)), url('/images/concierto2.jpg');"></div>
                            <div class="promo-event-body">
                                <span class="promo-event-tag">Concierto</span>
                                <h3 class="promo-event-title">Ritmo Urbano</h3>
                                <p class="promo-event-description">Kira Flow y DJ Boldo en un set urbano con acceso VIP en primera fila.</p>
                            </div>
                            <div class="promo-event-footer">
                                <button class="event-select-btn" type="button" data-select-event="ritmo-urbano">Seleccionar boleto</button>
                            </div>
                        </article>
                        <article class="promo-event-card" data-event-key="electro-fest" data-event-name="Electro Fest" data-event-price="200">
                            <div class="promo-event-image" style="background-image: linear-gradient(135deg, rgba(22, 163, 74, 0.85), rgba(16, 185, 129, 0.85)), url('/images/concierto3.jpg');"></div>
                            <div class="promo-event-body">
                                <span class="promo-event-tag">Concierto</span>
                                <h3 class="promo-event-title">Electro Fest</h3>
                                <p class="promo-event-description">Luma Beats en un show nocturno con DJ set y efectos visuales.</p>
                            </div>
                            <div class="promo-event-footer">
                                <button class="event-select-btn" type="button" data-select-event="electro-fest">Seleccionar boleto</button>
                            </div>
                        </article>
                    </div>
                </div>
                <button class="carousel-arrow carousel-next" type="button" data-carousel-next aria-label="Siguiente concierto">
                    <span aria-hidden="true">›</span>
                </button>
            </div>
        </section>

        <section class="section-band alt">
            <h2 class="section-title">Conciertos destacados</h2>
            <p class="section-lead">Eventos musicales con artista, duracion y acceso directo al boleto.</p>
            <div class="promo-carousel" data-carousel>
                <button class="carousel-arrow carousel-prev" type="button" data-carousel-prev aria-label="Concierto anterior">
                    <span aria-hidden="true">‹</span>
                </button>
                <div class="promo-carousel-viewport">
                    <div class="promo-event-grid promo-carousel-track">
                        <article class="promo-event-card">
                            <div class="promo-event-image" style="background-image: linear-gradient(135deg, rgba(27, 146, 224, 0.85), rgba(74, 58, 204, 0.85)), url('/images/concierto1.jpg');"></div>
                            <div class="promo-event-body">
                                <span class="promo-event-tag">Concierto</span>
                                <h3 class="promo-event-title">Noche de Pop</h3>
                                <p class="promo-event-description">Cantante: Ana Vega · Fechas: 17/06, 20/06 y 24/06 · Lugar: Escenario central.</p>
                                <div class="promo-event-list">
                                    <span>Artista invitado: DJ Boldo</span>
                                    <span>Horario estimado: 20:00 - 21:30</span>
                                    <span>Ambiente: luces, video y animacion</span>
                                </div>
                            </div>
                            <div class="promo-event-footer">
                                <a href="/comprar?ticketType=concierto&event=noche-de-pop">Ver boleto</a>
                            </div>
                        </article>
                        <article class="promo-event-card">
                            <div class="promo-event-image" style="background-image: linear-gradient(135deg, rgba(145, 52, 199, 0.85), rgba(59, 130, 246, 0.85)), url('/images/concierto2.jpg');"></div>
                            <div class="promo-event-body">
                                <span class="promo-event-tag">Concierto</span>
                                <h3 class="promo-event-title">Ritmo Urbano</h3>
                                <p class="promo-event-description">Cantante: Kira Flow · Fechas: 15/06, 21/06 y 28/06 · Lugar: Escenario oeste.</p>
                                <div class="promo-event-list">
                                    <span>Estilo: urbano / rap</span>
                                    <span>Horario estimado: 18:30 - 19:45</span>
                                    <span>Acceso VIP: zona frontal</span>
                                </div>
                            </div>
                            <div class="promo-event-footer">
                                <a href="/comprar?ticketType=concierto&event=ritmo-urbano">Ver boleto</a>
                            </div>
                        </article>
                        <article class="promo-event-card">
                            <div class="promo-event-image" style="background-image: linear-gradient(135deg, rgba(22, 163, 74, 0.85), rgba(16, 185, 129, 0.85)), url('/images/concierto3.jpg');"></div>
                            <div class="promo-event-body">
                                <span class="promo-event-tag">Concierto</span>
                                <h3 class="promo-event-title">Electro Fest</h3>
                                <p class="promo-event-description">Cantante: Luma Beats · Fechas: 19/06, 23/06 y 27/06 · Lugar: Escenario norte.</p>
                                <div class="promo-event-list">
                                    <span>Formato: DJ y banda en vivo</span>
                                    <span>Horario estimado: 22:00 - 23:20</span>
                                    <span>Entradas con acceso rapido</span>
                                </div>
                            </div>
                            <div class="promo-event-footer">
                                <a href="/comprar?ticketType=concierto&event=electro-fest">Ver boleto</a>
                            </div>
                        </article>
                    </div>
                </div>
                <button class="carousel-arrow carousel-next" type="button" data-carousel-next aria-label="Siguiente concierto">
                    <span aria-hidden="true">›</span>
                </button>
            </div>
        </section>

        <p id="promoMessage" class="create-msg"></p>
    </section>
</main>

<button id="cartButton" class="cart-fab" type="button" aria-label="Ir al carrito">
    <svg class="cart-icon" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M7 18a2 2 0 1 0 2 2 2 2 0 0 0-2-2Zm10 0a2 2 0 1 0 2 2 2 2 0 0 0-2-2ZM7.2 6h12.4a1 1 0 0 1 1 .8l1.3 6.6a1 1 0 0 1-1 1.2H8.1a1 1 0 0 1-1-.8L5.5 5.4H3a1 1 0 1 1 0-2h3.3a1 1 0 0 1 1 .8Z"></path>
    </svg>
    <span id="cartCount" class="cart-count hidden">0</span>
</button>

<script>
    const tokenKey = "feriaAuthToken";
    const cartKey = "feriaCart";
    const profileName = document.getElementById("profileName");
    const profileEmail = document.getElementById("profileEmail");
    const profileWrap = document.getElementById("profileWrap");
    const profileButton = document.getElementById("profileButton");
    const logoutBtn = document.getElementById("logoutBtn");
    const cartButton = document.getElementById("cartButton");
    const cartCount = document.getElementById("cartCount");
    const promoMessage = document.getElementById("promoMessage");

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
        updateCartCount(items);
    }

    function updateCartCount(items = readCart()) {
        const total = items.reduce((sum, item) => sum + (item.qty || 0), 0);
        cartCount.textContent = total;
        cartCount.classList.toggle("hidden", total === 0);
    }

    function showPromoMessage(text, ok = true) {
        setMessage(promoMessage, ok, text);
        if (!text) {
            return;
        }
        setTimeout(() => {
            promoMessage.textContent = "";
            promoMessage.className = "create-msg";
        }, 1800);
    }

    function initCarousels() {
        document.querySelectorAll("[data-carousel]").forEach((carousel) => {
            const viewport = carousel.querySelector(".promo-carousel-viewport");
            const track = carousel.querySelector(".promo-carousel-track");
            const cards = Array.from(carousel.querySelectorAll(".promo-event-card"));
            const prevButton = carousel.querySelector("[data-carousel-prev]");
            const nextButton = carousel.querySelector("[data-carousel-next]");
            if (cards.length <= 1) {
                prevButton.classList.add("hidden");
                nextButton.classList.add("hidden");
                return;
            }

            const originals = cards.map((card) => card.cloneNode(true));
            const firstClone = originals[0].cloneNode(true);
            const lastClone = originals[originals.length - 1].cloneNode(true);

            track.replaceChildren(lastClone, ...originals, firstClone);

            const slides = Array.from(track.children);
            const totalSlides = slides.length;
            const realCount = originals.length;
            let stepPx = 0;
            let currentIndex = 1;
            let isAnimating = false;

            // If the page requested a specific event, try to start centered on it
            try {
                const params = new URLSearchParams(window.location.search);
                const selectedEventKey = params.get("selectedEvent");
                if (selectedEventKey) {
                    const found = originals.findIndex(o => (o.dataset && o.dataset.eventKey) === selectedEventKey || o.getAttribute("data-event-key") === selectedEventKey);
                    if (found >= 0) {
                        currentIndex = found + 1; // account for the leading cloned slide
                    }
                }
            } catch (e) {
                // ignore
            }

            function setActiveCard() {
                slides.forEach((slide, index) => {
                    slide.classList.toggle("is-active", index === currentIndex);
                });
            }

            function updateCarousel(withAnimation = true) {
                // Robust centering: temporarily remove transform to measure absolute positions,
                // compute desired delta to center the target slide in the viewport, then apply it.
                const target = slides[currentIndex];
                if (!target) return;

                const prevTransition = track.style.transition;
                const prevTransform = track.style.transform;

                // Turn off transition for measurement
                track.style.transition = 'none';
                track.style.transform = 'translateX(0px)';

                // Measure
                const viewportRect = viewport.getBoundingClientRect();
                const slideRect = target.getBoundingClientRect();
                const centerOffset = (viewportRect.width - slideRect.width) / 2;

                // desired left position for the slide
                const desiredLeft = viewportRect.left + centerOffset;
                const delta = desiredLeft - slideRect.left;

                // Apply transform. If caller wants animation, restore transition class instead of inline transition
                if (withAnimation) {
                    // allow CSS transition to animate
                    track.style.transition = prevTransition || '';
                } else {
                    track.style.transition = 'none';
                }

                track.style.transform = `translateX(${delta}px)`;

                // Small delay to let layout settle if needed
                requestAnimationFrame(() => setActiveCard());
            }

            function goToSlide(nextIndex, withAnimation = true) {
                if (isAnimating) {
                    return;
                }

                isAnimating = true;
                track.classList.toggle("is-animating", withAnimation);
                currentIndex = nextIndex;
                updateCarousel();
            }

            track.addEventListener("transitionend", () => {
                if (currentIndex === 0) {
                    track.classList.remove("is-animating");
                    currentIndex = realCount;
                    stepPx = 0;
                    updateCarousel();
                } else if (currentIndex === totalSlides - 1) {
                    track.classList.remove("is-animating");
                    currentIndex = 1;
                    stepPx = 0;
                    updateCarousel();
                }

                isAnimating = false;
            });

            window.addEventListener("resize", () => {
                stepPx = 0;
                updateCarousel();
            });

            prevButton.addEventListener("click", () => {
                goToSlide(currentIndex - 1, true);
            });

            nextButton.addEventListener("click", () => {
                goToSlide(currentIndex + 1, true);
            });

            slides.forEach((slide) => {
                    slide.querySelectorAll(".event-select-btn").forEach((button) => {
                    button.addEventListener("click", () => {
                        const eventKey = button.dataset.selectEvent;
                        window.location.href = `/comprar?ticketType=concierto&event=${eventKey}&selectedEvent=${eventKey}`;
                    });
                });
            });

            // initial positioning: perform update twice and once in rAF to avoid measurement glitches
            track.classList.remove('is-animating');
            updateCarousel();
            setTimeout(() => {
                stepPx = 0;
                updateCarousel();
            }, 40);
            requestAnimationFrame(() => {
                stepPx = 0;
                updateCarousel();
            });
        });
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
        showPromoMessage("Agregado al carrito");
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

    document.querySelectorAll(".promo-card").forEach(card => {
        const addButton = card.querySelector("[data-add]");
        const qtyInput = card.querySelector(".qty-input");
        addButton.addEventListener("click", () => {
            const qty = Math.max(1, parseInt(qtyInput.value, 10) || 1);
            const item = {
                id: card.dataset.id,
                name: card.dataset.name,
                price: Number(card.dataset.price)
            };
            addToCart(item, qty);
            qtyInput.value = "1";
        });
    });

    function getQueryParam(name) {
        const params = new URLSearchParams(window.location.search);
        return params.get(name);
    }

    function highlightSelectedEvent() {
        const selected = getQueryParam("selectedEvent");
        if (!selected) {
            return;
        }
        const card = document.querySelector(`.promo-event-card[data-event-key="${selected}"]`);
        if (!card) {
            return;
        }
        card.classList.add("selected");
    }

    initCarousels();
    highlightSelectedEvent();

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

    cartButton.addEventListener("click", () => {
        window.location.href = "/comprar?ticketType=feria&ticket=general";
    });

    function init() {
        requireToken();
        updateCartCount();
        loadProfile();
    }

    init();
</script>
<script src="/js/site.js"></script>
</body>
</html>
