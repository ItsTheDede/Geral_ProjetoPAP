<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

/*
 * Lista de parceiros. Para adicionar ou alterar uma empresa, edite apenas este array.
 * 'logo' é o caminho do ficheiro (SVG, PNG ou WebP). Se o ficheiro não existir,
 * a página mostra as iniciais da empresa.
 */
$parceiros = [
    [
        'nome'      => 'Gamipeças',
        'tipo'      => 'Loja Local',
        'descricao' => 'Comércio de peças ligeiros, pesados e máquinas.',
        'local'     => 'Pombal',
        'logo'      => 'assets/images/parcerias/GAMIPECAS_Logotipo_2019.svg',
        'site'      => 'https://gamipecas.pt/',
    ],
];

function iniciais(string $nome): string {
    $partes = preg_split('/\s+/', trim($nome));
    $i = '';
    foreach (array_slice($partes, 0, 2) as $p) {
        $i .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $i;
}
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
    <title>Parceiros · LAM · Peças auto para oficinas · Distrito de Leiria</title>
    <meta name="description" content="Conheça as empresas parceiras do LAM: centros de abate, retificadoras e recondicionadores do distrito de Leiria.">
    <script>try{var t=localStorage.getItem('lisam-theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/backgroud.css">
    <style>
        .par-hero{padding:72px 0 8px}
        .par-hero h1{font-size:clamp(2.2rem,5vw,3.4rem);line-height:1.05;margin:12px 0 18px;max-width:22ch}
        .par-hero .lede{max-width:62ch;font-size:1.1rem;line-height:1.6;opacity:.85}

        .logos-faixa{display:flex;flex-wrap:wrap;gap:16px;margin-top:36px}
        .logo-box{display:flex;align-items:center;justify-content:center;width:160px;height:84px;padding:14px;border:1px solid rgba(128,128,128,.3);border-radius:10px;background:#fff}
        .logo-box img{max-width:100%;max-height:100%;object-fit:contain}
        .logo-box .ini{display:none;font-weight:900;font-size:1.6rem;color:#111;letter-spacing:.02em}
        .logo-box.sem-logo img{display:none}
        .logo-box.sem-logo .ini{display:block}

        .par-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px}
        .par-card{display:flex;flex-direction:column;gap:14px;border:1px solid rgba(128,128,128,.3);border-radius:12px;padding:22px}
        .par-card .logo-box{width:100%;height:110px}
        .par-card h3{margin:0}
        .par-card .tipo{display:inline-block;align-self:flex-start;padding:3px 10px;border-radius:999px;background:#ffd500;color:#111;font-weight:700;font-size:.82rem}
        .par-card p{margin:0;line-height:1.6;opacity:.85}
        .par-card .meta{font-size:.92rem;opacity:.75}
        .par-card .acoes{margin-top:auto;display:flex;gap:14px;flex-wrap:wrap}
        .par-card a:focus-visible{outline:3px solid #ffd500;outline-offset:3px}

        .juntar{display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:start}
        .juntar p{line-height:1.7;max-width:62ch;margin:0 0 14px}
        .juntar ul{margin:0;padding-left:20px;line-height:1.8}
        @media (max-width:760px){.juntar{grid-template-columns:1fr;gap:20px}.par-hero{padding-top:48px}}
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
        <a href="parceiros.php" class="active" aria-current="page">Parceiros</a>
        <a href="about.php">Sobre o projeto</a>
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

    <section class="par-hero">
        <span class="eyebrow">Rede</span>
        <h1>Empresas parceiras do LAM</h1>
        <p class="lede">Cada empresa mantém a sua marca, os seus preços e o seu stock. O LAM é o canal que as põe à distância de um clique das oficinas do distrito de Leiria.</p>

        <div class="logos-faixa" aria-label="Logotipos dos parceiros">
        <?php foreach ($parceiros as $p): ?>
            <div class="logo-box" title="<?= htmlspecialchars($p['nome']) ?>">
                <img src="<?= htmlspecialchars($p['logo']) ?>" alt="Logotipo de <?= htmlspecialchars($p['nome']) ?>" loading="lazy"
                     onerror="this.parentNode.classList.add('sem-logo')">
                <span class="ini" aria-hidden="true"><?= htmlspecialchars(iniciais($p['nome'])) ?></span>
            </div>
        <?php endforeach; ?>
        </div>
    </section>

    <section id="lista" style="margin-top:72px">
        <div class="sec-head rv">
        <span class="eyebrow">Parceiros</span>
        <h2>Quem vende no LAM</h2>
        <p><?= count($parceiros) ?> empresas locais, com stock próprio e levantamento no local.</p>
        </div>

        <div class="par-grid">
        <?php foreach ($parceiros as $p): ?>
            <article class="par-card rv">
                <div class="logo-box">
                    <img src="<?= htmlspecialchars($p['logo']) ?>" alt="Logotipo de <?= htmlspecialchars($p['nome']) ?>" loading="lazy"
                         onerror="this.parentNode.classList.add('sem-logo')">
                    <span class="ini" aria-hidden="true"><?= htmlspecialchars(iniciais($p['nome'])) ?></span>
                </div>
                <span class="tipo"><?= htmlspecialchars($p['tipo']) ?></span>
                <h3><?= htmlspecialchars($p['nome']) ?></h3>
                <p><?= htmlspecialchars($p['descricao']) ?></p>
                <p class="meta">📍 <?= htmlspecialchars($p['local']) ?></p>
                <div class="acoes">
                    <a href="products.php?parceiro=<?= urlencode($p['nome']) ?>">Ver peças</a>
                    <?php if (!empty($p['site'])): ?>
                        <a href="<?= htmlspecialchars($p['site']) ?>" target="_blank" rel="noopener">Website</a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    </section>

    <section id="juntar">
        <div class="sec-head rv">
        <span class="eyebrow">Adesão</span>
        <h2>Quer vender através do LAM?</h2>
        </div>
        <div class="juntar rv">
            <div>
                <p>O LAM é aberto a empresas de peças auto do distrito de Leiria que queiram vender online sem perder a sua identidade.</p>
                <p>Mantém o controlo total sobre o catálogo, os preços e o stock.</p>
            </div>
            <ul>
                <li>Loja com a sua própria marca</li>
                <li>Gestão do stock pela própria empresa</li>
                <li>Reservas diretas das oficinas</li>
                <li>Entrega no próprio dia num raio de 30 km</li>
            </ul>
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
        <div><h4>Plataforma</h4><a href="products.php">Peças</a><a href="parceiros.php">Parceiros</a><a href="index.php#lojas">Lojas</a></div>
        <div><h4>Conta</h4><a href="login.php">Entrar</a><a href="registo.php">Pedir acesso</a><a href="about.php">Sobre o projeto</a></div>
    </div>
    <div class="copy">© LAM · Peças auto B2B · Leiria</div>
    </footer>

    <script src="src/animations.js"></script>
</body>
</html>