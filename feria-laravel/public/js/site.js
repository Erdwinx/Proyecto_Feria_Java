(function(){
    // site-wide helpers: inject cart FAB when missing, manage cart count, improve profile hover
    function createCartFab(){
        if (document.getElementById('cartButton')) return;
        const btn = document.createElement('button');
        btn.id = 'cartButton';
        btn.className = 'cart-fab';
        btn.type = 'button';
        btn.setAttribute('aria-label','Ir al carrito');
        btn.innerHTML = `
            <svg class="cart-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M7 18a2 2 0 1 0 2 2 2 2 0 0 0-2-2Zm10 0a2 2 0 1 0 2 2 2 2 0 0 0-2-2ZM7.2 6h12.4a1 1 0 0 1 1 .8l1.3 6.6a1 1 0 0 1-1 1.2H8.1a1 1 0 0 1-1-.8L5.5 5.4H3a1 1 0 1 1 0-2h3.3a1 1 0 0 1 1 .8Z"></path>
            </svg>
            <span id="cartCount" class="cart-count hidden">0</span>
        `;
        document.body.appendChild(btn);
        btn.addEventListener('click', () => {
            if (window.location.pathname.replace(/\/$/, '') === '/comprar') {
                const checkoutPanel = document.getElementById('checkoutPanel');
                if (checkoutPanel) {
                    const isHidden = checkoutPanel.classList.contains('hidden');
                    checkoutPanel.classList.toggle('hidden', !isHidden);
                    if (isHidden) {
                        checkoutPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
                return;
            }

            window.location.href = '/comprar';
        });
    }

    function updateCartCount(){
        const raw = localStorage.getItem('feriaCart');
        let total = 0;
        try{ const items = raw ? JSON.parse(raw) : []; total = items.reduce((s,i)=>s+(i.qty||0),0); }catch(e){}
        const el = document.getElementById('cartCount') || null;
        if (el) {
            el.textContent = String(total);
            el.classList.toggle('hidden', total === 0);
        }
    }

    function enhanceProfile(){
        const profileWrap = document.getElementById('profileWrap');
        const profileButton = document.getElementById('profileButton');
        if (!profileWrap || !profileButton) return;
        let leaveTimer = null;
        profileWrap.addEventListener('mouseenter', () => {
            clearTimeout(leaveTimer);
            profileWrap.classList.add('is-open');
            profileButton.setAttribute('aria-expanded','true');
        });
        profileWrap.addEventListener('mouseleave', () => {
            leaveTimer = setTimeout(()=>{
                profileWrap.classList.remove('is-open');
                profileButton.setAttribute('aria-expanded','false');
            }, 180);
        });
    }

    function highlightActiveNav(){
        const path = window.location.pathname.replace(/\/$/, '') || '/';
        document.querySelectorAll('.topnav a').forEach((link) => {
            const href = new URL(link.getAttribute('href'), window.location.origin).pathname.replace(/\/$/, '') || '/';
            const isActive = href === path;
            link.classList.toggle('active', isActive);
            if (isActive) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }

    // run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { createCartFab(); updateCartCount(); enhanceProfile(); highlightActiveNav(); });
    } else {
        createCartFab(); updateCartCount(); enhanceProfile(); highlightActiveNav();
    }

    // watch localStorage changes by other tabs
    window.addEventListener('storage', (e) => { if (e.key === 'feriaCart') updateCartCount(); });
})();
