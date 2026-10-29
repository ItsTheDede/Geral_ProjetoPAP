<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$nomeUtilizador = '';
if (utilizadorAutenticado()) {
    $nomeUtilizador = $_SESSION['nome'] ?? $_SESSION['nome_empresa'] ?? 'Utilizador';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre o projeto · LAM · Peças auto para oficinas · Distrito de Leiria</title>
    <meta name="description" content="O LAM reúne PMEs do setor automóvel do distrito de Leiria numa só loja de peças B2B, com levantamento no local e entrega no próprio dia.">
    <script>try{var t=localStorage.getItem('lisam-theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/backgroud.css">
    <style>
        /* Apenas o que a página "Sobre" precisa além do CSS global */
        .about-hero{padding:72px 0 8px}
        .about-hero h1{font-size:clamp(2.2rem,5vw,3.6rem);line-height:1.05;margin:12px 0 18px;max-width:20ch}
        .about-hero .lede{max-width:62ch;font-size:1.1rem;line-height:1.6;opacity:.85}
        .about-nums{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-top:40px}
        .about-nums div{border-top:4px solid #ffd500;padding:14px 0 0}
        .about-nums strong{display:block;font-size:2.2rem;font-weight:900;line-height:1}
        .about-nums span{display:block;margin-top:6px;font-size:.92rem;opacity:.8}
        .about-split{display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:start}
        .about-split p{line-height:1.7;max-width:62ch;margin:0 0 14px}
        .about-split h3{margin:0 0 10px}
        .tipos{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px}
        .tipos article{border:1px solid rgba(128,128,128,.3);border-radius:10px;padding:22px}
        .tipos h3{margin:0 0 8px}
        .tipos p{margin:0;line-height:1.6;opacity:.85}
        .faq{max-width:780px}
        .faq details{border-bottom:1px solid rgba(128,128,128,.3);padding:16px 0}
        .faq summary{cursor:pointer;font-weight:700;font-size:1.05rem;list-style-position:outside}
        .faq summary:focus-visible{outline:3px solid #ffd500;outline-offset:4px}
        .faq details p{margin:12px 0 0;line-height:1.65;max-width:66ch;opacity:.85}
        @media (max-width:760px){.about-split{grid-template-columns:1fr;gap:20px}.about-hero{padding-top:48px}}
    </style>
</head>
<body>
    <header class="top">
    <div class="top-inner">
        <a href="index.php" class="brand"><span class="mark"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M5.3 18.7l2.1-2.1M16.6 7.4l2.1-2.1"/></svg></span><span>L<b>AM</b></span></a>
        <span class="plate hide-s"><span>PEÇAS AUTO</span></span>
        <button class="burger" aria-label="Menu">☰</button>
        <nav>
        <a href="index.php">Início</a>
        <a href="products.php">Produtos</a>
        <a href="parceiros.php">Parcerias</a>
        <a href="about.php" class="active" aria-current="page">Sobre o projeto</a>
        <button class="theme" id="tema" type="button" aria-label="Alternar tema claro/escuro">🌙</button>
        <?php if (utilizadorAutenticado()): ?>
            <div class="user-menu">
                <button class="user-btn" id="userBtn" type="button" aria-haspopup="true" aria-expanded="false">
                    <span class="avatar">👤</span>
                    <span class="nome"><?= h($nomeUtilizador) ?></span>
                    <span class="seta">▼</span>
                </button>
                <div class="dropdown" id="userDropdown" hidden>
                    <a href="perfil.php"><span>⚙️</span> Definições</a>
                    <a href="logout.php" class="sair"><span>🚪</span> Terminar sessão</a>
                </div>
            </div><!-- fecha .user-menu -->
        <?php else: ?>
            <!-- Botões para quem não está logado -->
            <a href="login.php" class="cta">Entrar</a>
            <a href="registro.php" class="cta btn-ghost" style="margin-left: 6px;">Criar conta</a>
        <?php endif; ?>
        </nav>
    </div>
    </header>

    <div class="hazard"></div>

    <main class="wrap">

    <!-- Apresentação -->
    <section class="about-hero">
        <span class="eyebrow">Sobre o projeto</span>
        <h1>Qual o objetivo deste projeto?</h1>
        <p class="lede">O projeto LAM (também associado ao conceito de Leiria AutoMarket), trata-se de uma plataforma digital inovadora em formato de marketplace multi-vendor, concebida para responder diretamente às necessidades do ecossistema automóvel da região de Leiria. O propósito é colmatar uma lacuna tecnológica significativa num setor tradicionalmente analógico, promovendo a transição digital de pequenas e médias empresas (PMEs) locais, tais como Centros de Abate de Veículos (CAMV), oficinas, retificadoras e recondicionadores especializados.</p>
    </section>

    <!-- Problema e solução -->
    <section id="problema" style="margin-top:84px">
        <div class="sec-head rv">
        <span class="eyebrow">Porquê o LAM</span>
        <h2>Boas peças, difíceis de encontrar</h2>
        </div>
        <div class="about-split rv">
            <div>
                <h3>O problema</h3>
                <p>Muitos centros de abate, retificadoras e recondicionadores da região têm stock valioso, mas pouca presença online. Quem precisa de uma peça tem de telefonar a cada empresa, uma a uma, e as oficinas perdem tempo que não têm.</p>
                <p>Em paralelo, os grandes marketplaces nacionais ganham pela escala, e as PMEs locais ficam sem forma de competir.</p>
            </div>
            <div>
                <h3>A solução</h3>
                <p>O LAM dá a estas empresas um canal de venda partilhado, mas sem lhes tirar a identidade. Cada uma mantém a sua marca, os seus preços e o seu stock. A oficina pesquisa uma vez e vê tudo o que está disponível perto de si.</p>
                <p>Junta a conveniência do comércio eletrónico à vantagem de estar na mesma região: levantamento no local e entrega no próprio dia.</p>
            </div>
        </div>
    </section>

    <!-- Missão, modelo, proximidade, economia circular -->
    <section id="valores">
        <div class="sobre rv">
        <div>
            <span class="eyebrow">Princípios</span>
            <h2>O que nos guia</h2>
            <p class="lead">O projeto assenta em quatro ideias simples. Servem de critério para cada decisão sobre a plataforma.</p>
        </div>
        <div class="pontos">
            <div><span class="pe">🎯</span><h3>Missão</h3><p>Dar às PMEs locais as ferramentas para competir com os grandes marketplaces nacionais.</p></div>
            <div><span class="pe">🤝</span><h3>Modelo B2B</h3><p>As empresas decidem preços e stock. O LAM é o canal de venda, não um concorrente.</p></div>
            <div><span class="pe">📍</span><h3>Proximidade</h3><p>Entrega no próprio dia para oficinas num raio de 30 km.</p></div>
            <div><span class="pe">♻️</span><h3>Economia circular</h3><p>Cada peça reutilizada evita uma peça nova: menos CO₂ e mais anos de vida para os veículos.</p></div>
        </div>
        </div>
    </section>

    <!-- Quem vende -->
    <section id="quem">
        <div class="sec-head rv">
        <span class="eyebrow">Quem vende</span>
        <h2>Empresas que conhecem as peças</h2>
        <p>A rede reúne negócios locais com experiência direta no setor.</p>
        </div>
        <div class="tipos rv">
            <article>
                <h3>Centros de abate</h3>
                <p>Peças usadas retiradas de veículos em fim de vida, testadas e catalogadas. Costumam ser a opção mais económica.</p>
            </article>
            <article>
                <h3>Retificadoras</h3>
                <p>Especialistas em componentes de motor. Trabalham peças que exigem rigor e conhecimento técnico.</p>
            </article>
            <article>
                <h3>Recondicionadores</h3>
                <p>Peças reparadas e revistas para voltarem a funcionar como novas, com custo inferior ao de uma peça nova.</p>
            </article>
        </div>
        <p style="margin-top:20px"><a href="index.php#parceiros">Ver as empresas parceiras</a></p>
    </section>

    <!-- Como funciona -->
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

    

    <!-- Perguntas frequentes -->
    <section id="faq">
        <div class="sec-head rv">
        <span class="eyebrow">Perguntas frequentes</span>
        <h2>Antes de começar</h2>
        </div>
        <div class="faq rv">
            <details>
                <summary>Quem pode comprar no LAM?</summary>
                <p>A plataforma é B2B, por isso destina-se a oficinas e profissionais do setor automóvel. Pode pedir acesso na página de registo.</p>
            </details>
            <details>
                <summary>Quem define os preços e o stock?</summary>
                <p>Cada empresa parceira. O LAM não altera preços nem stock: apresenta o que as lojas publicam.</p>
            </details>
            <details>
                <summary>Como é feita a entrega?</summary>
                <p>Pode levantar a peça na loja do parceiro ou receber na oficina no próprio dia, desde que esteja num raio de 30 km.</p>
            </details>
            <details>
                <summary>As peças usadas e recondicionadas têm garantia?</summary>
                <p>As condições de garantia são definidas por cada empresa e devem ser confirmadas com a loja ao reservar a peça.</p>
            </details>
            <details>
                <summary>Sou uma empresa de peças. Como posso aderir?</summary>
                <p>Entre em contacto através da página de acesso. Cada parceiro mantém a sua marca e gere o seu próprio catálogo.</p>
            </details>
        </div>
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
        <div><h4>Conta</h4><a href="login.php">Entrar</a><a href="registo.php">Pedir acesso</a><a href="about.php">Sobre o projeto</a></div>
    </div>
    <div class="copy">© LAM · Peças auto B2B · Leiria</div>
    </footer>

    <script src="src/animations.js"></script>
</body>
</html>