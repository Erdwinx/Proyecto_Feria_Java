<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion - Feria</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="auth-body">
<main class="auth-shell">
    <section class="auth-card">
        <div class="auth-left">
            <p class="auth-kicker">FERIA DIGITAL</p>
            <h1>Bienvenido</h1>
            <p>Compra tu boleto en linea y entra mas rapido. Recibe tu QR, guardalo en tu celular y listo.</p>
            <div class="auth-highlights">
                <span>Acceso rapido</span>
                <span>QR seguro</span>
                <span>Sin filas</span>
            </div>
        </div>
        <div class="auth-right">
            <section id="loginPanel">
                <h2>Iniciar sesion</h2>
                <form id="loginForm" class="auth-form">
                    <label for="loginEmail">Correo</label>
                    <input id="loginEmail" type="email" maxlength="120" required>

                    <label for="loginPassword">Contrasena</label>
                    <input id="loginPassword" type="password" minlength="6" maxlength="80" required>

                    <button type="submit">Entrar</button>
                </form>
                <p id="loginMessage" class="create-msg"></p>
                <p class="auth-switch">Aun no tienes cuenta? <button id="goRegister" type="button" class="auth-link">Registrar</button></p>
            </section>

            <section id="registerPanel" class="hidden">
                <h2>Crear cuenta</h2>
                <form id="registerForm" class="auth-form">
                    <label for="registerName">Nombre completo</label>
                    <input id="registerName" type="text" maxlength="80" required>

                    <label for="registerEmail">Correo</label>
                    <input id="registerEmail" type="email" maxlength="120" required>

                    <label for="registerPassword">Contrasena</label>
                    <input id="registerPassword" type="password" minlength="6" maxlength="80" required>

                    <label for="registerPasswordConfirm">Confirmar contrasena</label>
                    <input id="registerPasswordConfirm" type="password" minlength="6" maxlength="80" required>

                    <button type="submit">Crear cuenta</button>
                </form>
                <p id="registerMessage" class="create-msg"></p>
                <p class="auth-switch">Ya tienes cuenta? <button id="goLogin" type="button" class="auth-link">Iniciar sesion</button></p>
            </section>
        </div>
    </section>
</main>

<script>
    const tokenKey = "feriaAuthToken";
    const loginPanel = document.getElementById("loginPanel");
    const registerPanel = document.getElementById("registerPanel");
    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");
    const loginMessage = document.getElementById("loginMessage");
    const registerMessage = document.getElementById("registerMessage");
    const goRegister = document.getElementById("goRegister");
    const goLogin = document.getElementById("goLogin");

    function setMessage(target, ok, text) {
        target.textContent = text;
        target.className = ok ? "create-msg ok" : "create-msg error";
    }

    function switchPanel(panel) {
        const showLogin = panel === "login";
        loginPanel.classList.toggle("hidden", !showLogin);
        registerPanel.classList.toggle("hidden", showLogin);
        loginMessage.textContent = "";
        registerMessage.textContent = "";
    }

    async function readErrorMessage(response) {
        try {
            const data = await response.json();
            if (data.message) {
                return data.message;
            }

            if (data.errors) {
                const firstKey = Object.keys(data.errors)[0];
                const firstMessage = firstKey && Array.isArray(data.errors[firstKey]) ? data.errors[firstKey][0] : null;
                if (firstMessage) {
                    return firstMessage;
                }
            }

            return "No se pudo completar la solicitud";
        } catch (error) {
            const text = await response.text();
            return text || "No se pudo completar la solicitud";
        }
    }

    function handleAuthSuccess(data) {
        localStorage.setItem(tokenKey, data.token);
        localStorage.setItem("feriaCustomer", JSON.stringify(data.customer));
        window.location.href = "/inicio";
    }

    loginForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        const body = {
            email: document.getElementById("loginEmail").value.trim(),
            password: document.getElementById("loginPassword").value
        };

        const response = await fetch("/api/customers/login", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body)
        });

        if (!response.ok) {
            setMessage(loginMessage, false, await readErrorMessage(response));
            return;
        }

        const data = await response.json();
        handleAuthSuccess(data);
    });

    registerForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        const password = document.getElementById("registerPassword").value;
        const confirmPassword = document.getElementById("registerPasswordConfirm").value;
        if (password !== confirmPassword) {
            setMessage(registerMessage, false, "Las contrasenas no coinciden");
            return;
        }
        const body = {
            nombre: document.getElementById("registerName").value.trim(),
            email: document.getElementById("registerEmail").value.trim(),
            password
        };

        const response = await fetch("/api/customers/register", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body)
        });

        if (!response.ok) {
            const message = await readErrorMessage(response);
            setMessage(registerMessage, false, message);
            if (message.toLowerCase().includes("correo ya esta registrado")) {
                document.getElementById("registerEmail").focus();
            }
            return;
        }

        const data = await response.json();
        handleAuthSuccess(data);
    });

    goRegister.addEventListener("click", () => switchPanel("register"));
    goLogin.addEventListener("click", () => switchPanel("login"));

    if (localStorage.getItem(tokenKey)) {
        window.location.href = "/inicio";
    }
</script>
</body>
</html>
