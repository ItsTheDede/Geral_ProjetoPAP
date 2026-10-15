<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | LAM</title>

    <!-- Tema: começa em escuro se o utilizador nunca escolheu -->
    <script>
      try {
        var t = localStorage.getItem('lam-theme');
        document.documentElement.dataset.theme = t || 'dark';
      } catch(e) {
        document.documentElement.dataset.theme = 'dark';
      }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/backgroud.css">
</head>
<body>
    <header class="top">
    <div class="top-inner">
        <a href="index.php" class="brand">
            <span class="mark">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3.2"/>
                    <path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M5.3 18.7l2.1-2.1M16.6 7.4l2.1-2.1"/>
                </svg>
            </span>
            <span>L<b>AM</b></span>
        </a>
        <span class="plate hide-s"><span>PEÇAS AUTO</span></span>
        <button class="burger" aria-label="Menu">☰</button>
        <nav>
            <a href="index.php">Início</a>
            <a href="products.php">Produtos</a>
            <button class="theme" id="tema" type="button" aria-label="Alternar tema claro/escuro">🌙</button>
            <a href="login.php" class="cta">Entrar</a>
        </nav>
    </div>
    </header>

    <main class="login-page">
    <div class="login-side">
        <svg class="disc" viewBox="0 0 200 200" aria-hidden="true">
            <defs><circle id="fh" cx="100" cy="30" r="7"/></defs>
            <circle class="a" cx="100" cy="100" r="96"/>
            <circle class="b" cx="100" cy="100" r="82"/>
            <circle class="a" cx="100" cy="100" r="64"/>
            <g class="b">
                <use href="#fh"/><use href="#fh" transform="rotate(45 100 100)"/>
                <use href="#fh" transform="rotate(90 100 100)"/><use href="#fh" transform="rotate(135 100 100)"/>
                <use href="#fh" transform="rotate(180 100 100)"/><use href="#fh" transform="rotate(225 100 100)"/>
                <use href="#fh" transform="rotate(270 100 100)"/><use href="#fh" transform="rotate(315 100 100)"/>
            </g>
            <circle class="b" cx="100" cy="100" r="30"/>
            <circle class="a" cx="100" cy="100" r="22"/>
            <g class="b">
                <circle cx="100" cy="86" r="4"/><circle cx="114" cy="100" r="4"/>
                <circle cx="100" cy="114" r="4"/><circle cx="86" cy="100" r="4"/>
            </g>
        </svg>
        <div>
        <span class="eyebrow" id="sideEyebrow">Área de parceiros</span>
        <h1 id="sideTitulo">Bem-vindo de volta à <span class="gtext">oficina</span>.</h1>
        <p id="sideTexto">Entre na sua conta para reservar peças auto, acompanhar encomendas e levantar na loja ou receber no próprio dia.</p>
        <div class="vant" id="vantEmpresa">
            <div><span>⚡</span>Reserve peças em segundos</div>
            <div><span>📍</span>Levante na loja mais próxima</div>
            <div><span>🚚</span>Entrega no próprio dia num raio de 30 km</div>
        </div>
        <div class="vant" id="vantCliente" hidden>
            <div><span>🔎</span>Pesquise peças pela referência ou pelo carro</div>
            <div><span>🛒</span>Veja as suas reservas num só sítio</div>
            <div><span>📍</span>Levante junto de quem tem em stock, perto de si</div>
        </div>
        <div class="lista">
            <div class="c-c1"><i></i><b>Samiparts</b><span>Flandes</span></div>
            <div class="c-c2"><i></i><b>Guipeças</b><span>Marinha Grande</span></div>
            <div class="c-c3"><i></i><b>Gamipeças</b><span>Pombal</span></div>
        </div>
        </div>
    </div>

    <div class="form-wrap">
        <form class="card" id="loginForm" novalidate>
        <h2>Entrar</h2>
        <p class="sub" id="subTxt">Acesso para oficinas e empresas parceiras.</p>

        <div class="tipo-conta" role="radiogroup" aria-label="Tipo de conta">
            <label class="opt" id="optCliente">
                <input type="radio" name="conta" value="cliente">
                <span class="ico">👤</span>
                <span><strong>Cliente</strong><small>Particular</small></span>
            </label>
            <label class="opt on" id="optEmpresa">
                <input type="radio" name="conta" value="empresa" checked>
                <span class="ico">🔧</span>
                <span><strong>Empresa</strong><small>Oficina ou outra</small></span>
            </label>
        </div>

        <div class="campo">
            <label for="email">Email</label>
            <input type="email" id="email" placeholder="oficina@exemplo.pt" autocomplete="email" required>
        </div>

        <div class="campo">
            <label for="pass">Palavra-passe</label>
            <div class="pw">
                <input type="password" id="pass" placeholder="••••••••" autocomplete="current-password" required>
                <button type="button" class="ver" id="ver">Mostrar</button>
            </div>
        </div>

        <div class="linha">
            <label><input type="checkbox" id="lembrar"> Manter sessão iniciada</label>
            <a href="#" id="esqueci">Esqueci-me da palavra-passe</a>
        </div>

        <p class="erro" id="erro" style="display:none" role="alert"></p>
        <button type="submit" class="entrar" id="btnEntrar">Entrar</button>

        <p class="novo">Ainda não tem conta? <a href="registro.php" id="linkCriar">Criar conta</a></p>
        </form>
    </div>
    </main>

    <div class="toast" id="toast"></div>
    <script src="src/animations.js"></script>
</body>
</html>