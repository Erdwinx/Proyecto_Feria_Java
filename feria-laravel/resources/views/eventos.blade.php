<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feria - Eventos</title>
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
        <h2 class="section-title">Eventos y activaciones</h2>
        <p class="section-lead">Planifica tu visita con los eventos destacados del dia.</p>
        <div class="event-grid">
            <article id="boleto-concierto-central" class="event-card cover">
                <div class="cover-image" style="background-image: linear-gradient(180deg, rgba(6, 13, 38, 0.2), rgba(6, 13, 38, 0.75)), url('/images/concert-cover-1.jpg');"></div>
                <div class="event-top">
                    <div class="event-meta">
                        <span>Escenario central</span>
                        <span class="event-badge">Concierto</span>
                    </div>
                    <h3>Concierto central</h3>
                    <p>Vive el show audiovisual de la noche con Ana Vega y banda en vivo.</p>
                </div>
                <div class="event-details-grid">
                    <div class="event-detail-box">
                        <strong>Sobre el concierto</strong>
                        <p>Un set lleno de luces, videos y temas exclusivos para cerrar la noche.</p>
                    </div>
                    <div class="event-detail-box">
                        <strong>Fecha, horario y zona</strong>
                        <p>Fechas:<br>10/05/2026<br>14/05/2026<br>18/05/2026<br>Horario: 20:00 - 21:30<br>Zona: Escenario central</p>
                    </div>
                    <div class="event-detail-box">
                        <strong>Artistas</strong>
                        <p>Ana Vega<br>DJ Boldo</p>
                    </div>
                </div>
                <div class="event-footer full-width">
                    <a href="/promociones">Ver boleto</a>
                </div>
            </article>
            <article id="boleto-ritmo-urbano" class="event-card cover">
                <div class="cover-image" style="background-image: linear-gradient(180deg, rgba(7, 14, 31, 0.2), rgba(7, 14, 31, 0.75)), url('/images/concert-cover-2.jpg');"></div>
                <div class="event-top">
                    <div class="event-meta">
                        <span>Escenario urbano</span>
                        <span class="event-badge">Concierto</span>
                    </div>
                    <h3>Ritmo Urbano</h3>
                    <p>Kira Flow y DJ Boldo traen rap en vivo para encender la noche.</p>
                </div>
                <div class="event-details-grid">
                    <div class="event-detail-box">
                        <strong>Sobre el concierto</strong>
                        <p>Una experiencia urbana con beats, rimas y buena energia.</p>
                    </div>
                    <div class="event-detail-box">
                        <strong>Fecha, horario y zona</strong>
                        <p>Fechas:<br>15/06/2026<br>21/06/2026<br>28/06/2026<br>Horario: 18:30 - 19:45<br>Zona: Escenario urbano</p>
                    </div>
                    <div class="event-detail-box">
                        <strong>Artistas</strong>
                        <p>Kira Flow<br>DJ Boldo</p>
                    </div>
                </div>
                <div class="event-footer full-width">
                    <a href="/promociones">Ver boleto</a>
                </div>
            </article>
            <article id="boleto-electro-fest" class="event-card cover">
                <div class="cover-image" style="background-image: linear-gradient(180deg, rgba(7, 14, 31, 0.2), rgba(7, 14, 31, 0.75)), url('/images/concert-cover-3.jpg');"></div>
                <div class="event-top">
                    <div class="event-meta">
                        <span>Escenario nocturno</span>
                        <span class="event-badge">Concierto</span>
                    </div>
                    <h3>Electro Fest</h3>
                    <p>La mejor mezcla de DJ set y proyecciones para cerrar la feria.</p>
                </div>
                <div class="event-details-grid">
                    <div class="event-detail-box">
                        <strong>Sobre el concierto</strong>
                        <p>Un show con loops, visuales y energia electronica por Luma Beats.</p>
                    </div>
                    <div class="event-detail-box">
                        <strong>Fecha, horario y zona</strong>
                        <p>Fechas:<br>19/06/2026<br>23/06/2026<br>27/06/2026<br>Horario: 22:00 - 23:20<br>Zona: Escenario nocturno</p>
                    </div>
                    <div class="event-detail-box">
                        <strong>Artistas</strong>
                        <p>Luma Beats</p>
                    </div>
                </div>
                <div class="event-footer full-width">
                    <a href="/promociones">Ver boleto</a>
                </div>
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
        highlightEventFromHash();
    }

    function highlightEventFromHash() {
        const hash = window.location.hash;
        if (hash && hash.startsWith("#boleto-")) {
            const eventCard = document.querySelector(hash);
            if (eventCard) {
                eventCard.scrollIntoView({ behavior: "smooth", block: "center" });
                eventCard.style.borderColor = "#7fbbff";
                eventCard.style.borderWidth = "3px";
                eventCard.style.boxShadow = "0 0 20px rgba(127, 187, 255, 0.5)";
                setTimeout(() => {
                    eventCard.style.borderColor = "";
                    eventCard.style.borderWidth = "";
                    eventCard.style.boxShadow = "";
                }, 3000);
            }
        }
    }

    init();
</script>
<script src="/js/site.js"></script>
</body>
</html>
