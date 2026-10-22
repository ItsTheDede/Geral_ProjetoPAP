<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAM · Peças auto para oficinas · Distrito de Leiria</title>
    <script>try{var t=localStorage.getItem('lisam-theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="src/backgroud.css">
</head>
<body>
    <header class="top">
    <div class="top-inner">
        <a href="index.php" class="brand"><span class="mark"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M5.3 18.7l2.1-2.1M16.6 7.4l2.1-2.1"/></svg></span><span>L<b>AM</b></span></a>
        <span class="plate hide-s"><span>PEÇAS AUTO</span></span>
        <button class="burger" aria-label="Menu">☰</button>
        <nav>
        <a href="index.php" class="active">Início</a>
        <a href="products.php">Produtos</a>
        <button class="theme" id="tema" type="button" aria-label="Alternar tema claro/escuro">🌙</button>
        <?php if (utilizadorAutenticado()): ?>
            <a href="dashboard.php" class="cta">Painel</a>
        <?php else: ?>
            <a href="login.php" class="cta">Entrar</a>
        <?php endif; ?>
        </nav>
    </div>
    </header>

    <div class="hero">
    <svg class="deco d1" viewBox="0 0 200 200" aria-hidden="true"><defs><circle id="fh" cx="100" cy="30" r="7"/></defs><circle cx="100" cy="100" r="96" fill="#000"/><circle cx="100" cy="100" r="82" fill="#ffd500"/><circle cx="100" cy="100" r="64" fill="#000"/><g fill="#ffd500"><use href="#fh"/><use href="#fh" transform="rotate(45 100 100)"/><use href="#fh" transform="rotate(90 100 100)"/><use href="#fh" transform="rotate(135 100 100)"/><use href="#fh" transform="rotate(180 100 100)"/><use href="#fh" transform="rotate(225 100 100)"/><use href="#fh" transform="rotate(270 100 100)"/><use href="#fh" transform="rotate(315 100 100)"/></g><circle cx="100" cy="100" r="30" fill="#ffd500"/><circle cx="100" cy="100" r="22" fill="#000"/><g fill="#ffd500"><circle cx="100" cy="86" r="4"/><circle cx="114" cy="100" r="4"/><circle cx="100" cy="114" r="4"/><circle cx="86" cy="100" r="4"/></g></svg>
        <svg class="deco d2" viewBox="0 0 200 200" aria-hidden="true"><defs><rect id="tr" x="90" y="2" width="20" height="16" rx="3"/></defs><circle cx="100" cy="100" r="98" fill="#000"/><g fill="#ffd500"><use href="#tr"/><use href="#tr" transform="rotate(30 100 100)"/><use href="#tr" transform="rotate(60 100 100)"/><use href="#tr" transform="rotate(90 100 100)"/><use href="#tr" transform="rotate(120 100 100)"/><use href="#tr" transform="rotate(150 100 100)"/><use href="#tr" transform="rotate(180 100 100)"/><use href="#tr" transform="rotate(210 100 100)"/><use href="#tr" transform="rotate(240 100 100)"/><use href="#tr" transform="rotate(270 100 100)"/><use href="#tr" transform="rotate(300 100 100)"/><use href="#tr" transform="rotate(330 100 100)"/></g><circle cx="100" cy="100" r="68" fill="#fff" stroke="#ffd500" stroke-width="6"/><g stroke="#000" stroke-width="12" stroke-linecap="round"><path d="M100 100V44M100 100l53 -17M100 100l33 46M100 100l-33 46M100 100l-53 -17"/></g><circle cx="100" cy="100" r="14" fill="#000"/></svg>
    <div class="hero-inner">
        <div class="pill"><i></i> Loja de peças auto · Entrega no próprio dia · Distrito de Leiria</div>
        <h1>Peças auto para a sua oficina, <em>entregues hoje</em>.</h1>
        <p class="lede">Travões, motor, suspensão, pneus, elétrica e mais. Peças auto novas, usadas e recondicionadas de três empresas do distrito de Leiria. Levante na loja ou receba na oficina no próprio dia.</p>
        <form class="procura" id="formProcura" onsubmit="return false">
        <input type="text" id="q" aria-label="Pesquisar" placeholder="Peça ou referência OEM (ex.: pastilhas, BRK-1024)">
        <button class="btn btn-primary" type="submit">Procurar</button>
        </form>
        <div class="quick" id="quick"></div>
    </div>
    <div class="stats rv" id="stats"></div>
    </div>

    <div class="ticker"><div class="track" id="track"></div></div>
    <div class="hazard"></div>


    <main class="wrap">
    <section id="pecas" style="margin-top:84px">
        <div class="sec-head rv">
        <span class="eyebrow">Catálogo</span>
        <h2>Peças auto disponíveis</h2>
        <p id="pecasSub">Stock atualizado pelas próprias lojas.</p>
        </div>
        <div class="cats" id="cats"></div>
        <div class="toolbar">
        <div class="chips" id="chipsEstado"></div>
        <select id="ordem" aria-label="Ordenar">
            <option value="">Ordenar: relevância</option>
            <option value="asc">Preço: mais baixo</option>
            <option value="desc">Preço: mais alto</option>
        </select>
        </div>
        <div class="pecas" id="pecasGrid"></div>
    </section>

    <section id="como">
        <div class="sec-head rv">
        <span class="eyebrow">Como funciona</span>
        <h2>Da pesquisa à oficina em 3 passos</h2>
        </div>
        <div class="passos">
        <div class="passo rv"><span class="n">1</span><div class="pi">🔎</div><h3>Pesquise</h3><p>Procure por referência OEM, peça ou categoria em todo o stock dos parceiros.</p></div>
        <div class="passo rv"><span class="n">2</span><div class="pi">🛒</div><h3>Reserve</h3><p>Reserve a peça com um clique. A loja confirma a disponibilidade.</p></div>
        <div class="passo rv"><span class="n">3</span><div class="pi">🚚</div><h3>Levante ou receba</h3><p>Levante na loja mais próxima ou receba na oficina no próprio dia, num raio de 30 km.</p></div>
        </div>
    </section>
    <section id="lojas">
        <div class="mapa-top">
        <div class="sec-head rv" style="margin:0">
            <span class="eyebrow">Localização</span>
            <h2>Onde levantar</h2>
            <p>Lojas físicas dos parceiros.</p>
        </div>
        <div class="leg" id="leg"></div>
        </div>
        <div id="mapa"></div>
    </section>
    <section id="parceiros">
        <div class="sec-head rv">
        <span class="eyebrow">Rede</span>
        <h2>Empresas parceiras</h2>
        <p>Cada empresa mantém a sua marca, os seus preços e o seu stock.</p>
        </div>
        <div class="parceiros" id="parceirosList"></div>
    </section>
    <section id="projeto">
        <div class="sobre rv">
        <div>
            <span class="eyebrow">Sobre o projeto</span>
            <h2>Digitalizar o setor automóvel da região.</h2>
            <p class="lead">O LAM reúne PMEs locais, como centros de abate, retificadoras e recondicionadores, para que vendam online com a sua própria marca. Junta o comércio eletrónico à vantagem de estar perto: levantamento no local e entrega no próprio dia.</p>
        </div>
        <div class="pontos">
            <div><span class="pe">🎯</span><h3>Missão</h3><p>Dar às PMEs locais as ferramentas para competir com os grandes marketplaces nacionais.</p></div>
            <div><span class="pe">🤝</span><h3>Modelo B2B</h3><p>As empresas decidem preços e stock. O LAM é o canal de venda, não um concorrente.</p></div>
            <div><span class="pe">📍</span><h3>Proximidade</h3><p>Entrega no próprio dia para oficinas num raio de 30 km.</p></div>
            <div><span class="pe">♻️</span><h3>Economia circular</h3><p>Cada peça reutilizada evita uma peça nova: menos CO₂ e mais anos de vida para os veículos.</p></div>
        </div>
        </div>
    </section>
    <section class="cta-band rv" style="margin-bottom:0">
        <div>
        <h2>Pronto para encontrar a peça certa?</h2>
        <p>Entre com a sua conta de oficina e reserve em segundos.</p>
        </div>
        <?php if (utilizadorAutenticado()): ?>
            <a class="btn btn-light" href="dashboard.php">Ir para o painel →</a>
        <?php else: ?>
            <a class="btn btn-light" href="login.php">Entrar na plataforma →</a>
        <?php endif; ?>
    </section>
    </main>
    <div class="hazard"></div>
    <footer class="foot">
    <div class="foot-grid">
        <div>
        <a href="index.php" class="brand"><span class="mark"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M5.3 18.7l2.1-2.1M16.6 7.4l2.1-2.1"/></svg></span><span>L<b>AM</b></span></a>
        <p>Loja de peças auto B2B que liga oficinas às empresas de peças do distrito de Leiria.</p>
        </div>
        <div><h4>Plataforma</h4><a href="products.php">Peças</a><a href="products.php">Stock</a><a href="index.php#lojas">Lojas</a></div>
        <div><h4>Conta</h4><a href="login.php">Entrar</a><a href="registo.php">Pedir acesso</a><a href="index.php#projeto">Sobre o projeto</a></div>
    </div>
    <div class="copy">© LAM · Peças auto B2B · Leiria</div>
    </footer>

    <script src="src/animations.js"></script>
</body>
</html>