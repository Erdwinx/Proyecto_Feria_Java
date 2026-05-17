<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feria - Inicio</title>
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
        <div class="hero">
            <div class="hero-card">
                <span class="hero-tag">Acceso Digital</span>
                <h1>Bienvenido a la feria</h1>
                <p>Compra tu boleto, guarda tu QR y entra sin filas. Todo en un solo lugar, rapido y seguro.</p>
                <div class="hero-actions">
                    <a class="ghost-btn" href="/promociones">Ver promociones</a>
                    <a class="ghost-btn" href="/comprar">Ir a comprar</a>
                </div>
            </div>
            <div class="hero-art">
                <div class="stat-card">
                    <h3>QR seguro</h3>
                    <p>Tu boleto es unico y solo se usa una vez.</p>
                </div>
                <div class="stat-card">
                    <h3>Notificaciones</h3>
                    <p>Recibe novedades de la feria y proximos eventos.</p>
                </div>
                <div class="stat-card">
                    <h3>Soporte en sitio</h3>
                    <p>Recupera tu boleto en el registro si lo necesitas.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-band alt">
        <h2 class="section-title">Accesos rapidos</h2>
        <div class="event-grid">
            <article class="event-card">
                <div class="event-meta">
                    <span>Agenda</span>
                    <span class="event-badge">Explora</span>
                </div>
                <h3>Eventos principales</h3>
                <p>Consulta las activaciones del dia y organiza tu visita.</p>
                <a class="ghost-btn" href="/eventos">Ir a eventos</a>
            </article>
            <article class="event-card">
                <div class="event-meta">
                    <span>Noticias</span>
                    <span class="event-badge">Hoy</span>
                </div>
                <h3>Ultimas novedades</h3>
                <p>Actualizaciones de accesos, mapas y anuncios oficiales.</p>
                <a class="ghost-btn" href="/noticias">Ver noticias</a>
            </article>
            <article class="event-card">
                <div class="event-meta">
                    <span>Promociones</span>
                    <span class="event-badge">Descuentos</span>
                </div>
                <h3>Boletos disponibles</h3>
                <p>Selecciona tu categoria y agregala al carrito.</p>
                <a class="ghost-btn" href="/promociones">Ver promociones</a>
            </article>
        </div>
    </section>
</main>

<script>
    const tokenKey = "feriaAuthToken";
    const profileName = document.getElementById("profileName");
    const profileEmail = document.getElementById("profileEmail");
    const profileWrap = document.getElementById("profileWrap");
    const profileButton = document.getElementById("profileButton");
    const logoutBtn = document.getElementById("logoutBtn");

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
    }

    init();
</script>
<script src="/js/site.js"></script>
</body>
</html>
