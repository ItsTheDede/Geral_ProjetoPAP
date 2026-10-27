<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

/* Caminhos das imagens. Altere aqui se os ficheiros tiverem outro nome ou extensão. */
$fotoHero  = 'assets/images/social/peças-sobresselentes-idosas-do-carro-do-lote-na-garagem-do-serviço-94477246.svg';
$logoMarca = 'assets/images/parcerias/GAMIPECAS_Logotipo_2019.svg';
// Codifica os acentos do nome do ficheiro para o URL funcionar em qualquer browser
$fotoHeroUrl = implode('/', array_map('rawurlencode', explode('/', $fotoHero)));

$produtos = [];
$erroBD   = '';

try {
    // Confirma o nome da tabela no phpMyAdmin (aqui: "produtos")
    $res      = $conn->query("SELECT * FROM produtos ORDER BY nome");
    $produtos = $res->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    error_log("Produtos: " . $e->getMessage());
    $erroBD = APP_DEBUG ? "Erro de base de dados: " . $e->getMessage() : "Não foi possível carregar os produtos neste momento.";
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peças auto disponíveis | LAM</title>
    <script>try{var t=localStorage.getItem('lisam-theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/backgroud.css">
    <style>
        /* Logotipo da Gamipeças no lugar do ícone da marca */
        .brand .mark img{display:block;height:100%;width:auto;max-width:100%;object-fit:contain}
    </style>
</head>
<body>
    <header class="top">
    <div class="top-inner">
        <a href="index.php" class="brand"><span class="mark"><img src="<?= $logoMarca ?>" alt="Gamipeças" onerror="this.style.display='none';this.nextElementSibling.style.display='block'"><svg style="display:none" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M5.3 18.7l2.1-2.1M16.6 7.4l2.1-2.1"/></svg></span><span>L<b>AM</b></span></a>
        <span class="plate hide-s"><span>PEÇAS AUTO</span></span>
        <button class="burger" aria-label="Menu">☰</button>
        <nav>
        <a href="index.php">Início</a>
        <a href="products.php" class="active">Produtos</a>
        <a href="parceiros.php">Parcerias</a>
        <a href="about.php">Sobre o projeto</a>
        <button class="theme" id="tema" type="button" aria-label="Alternar tema claro/escuro">🌙</button>
        <?php if (utilizadorAutenticado()): ?>
            <a href="dashboard.php" class="cta">Painel</a>
        <?php else: ?>
            <a href="login.php" class="cta">Entrar</a>
        <?php endif; ?>
        </nav>
    </div>
    </header>

    <div class="page-hero" style="background-image:linear-gradient(rgba(255,213,0,.72),rgba(255,213,0,.72)),url('<?= $fotoHeroUrl ?>') !important;background-size:cover !important;background-position:center !important">
    <svg class="disc" viewBox="0 0 200 200" aria-hidden="true"><defs><circle id="fh" cx="100" cy="30" r="7"/></defs><circle class="a" cx="100" cy="100" r="96"/><circle class="b" cx="100" cy="100" r="82"/><circle class="a" cx="100" cy="100" r="64"/><g class="b"><use href="#fh"/><use href="#fh" transform="rotate(45 100 100)"/><use href="#fh" transform="rotate(90 100 100)"/><use href="#fh" transform="rotate(135 100 100)"/><use href="#fh" transform="rotate(180 100 100)"/><use href="#fh" transform="rotate(225 100 100)"/><use href="#fh" transform="rotate(270 100 100)"/><use href="#fh" transform="rotate(315 100 100)"/></g><circle class="b" cx="100" cy="100" r="30"/><circle class="a" cx="100" cy="100" r="22"/><g class="b"><circle cx="100" cy="86" r="4"/><circle cx="114" cy="100" r="4"/><circle cx="100" cy="114" r="4"/><circle cx="86" cy="100" r="4"/></g></svg>
    <div class="wrap">
        <h1>Peças auto disponíveis</h1>
        <p id="pecasSub">Stock atualizado pelas próprias lojas.</p>
    </div>
    </div>

    <main class="wrap">
    <section id="pecas" style="margin:40px 0 84px">
        <div class="cats" id="cats"></div>
        <div class="toolbar">
        <div class="chips" id="chipsEstado"></div>
        <input type="text" id="q" placeholder="Peça ou referência OEM (ex.: pastilhas, BRK-1024)" aria-label="Pesquisar produtos">
        <select id="ordem" aria-label="Ordenar">
            <option value="">Ordenar: relevância</option>
            <option value="asc">Preço: mais baixo</option>
            <option value="desc">Preço: mais alto</option>
        </select>
        </div>
        <div class="pecas" id="pecasGrid"></div>

        <?php if ($erroBD): ?>
            <p class="erro" style="display:block" role="alert"><?= h($erroBD) ?></p>
        <?php endif; ?>

        <div class="pecas">
        <?php foreach ($produtos as $row): ?>
            <div class="cartao-produto">
                <h3><?= h($row['nome'] ?? '') ?></h3>
                <p>Preço: <?= isset($row['preco']) ? h(number_format((float)$row['preco'], 2, ',', ' ')) : '—' ?> €</p>
                <!-- Adiciona aqui a imagem e o resto do design -->
            </div>
        <?php endforeach; ?>
        <?php if (!$erroBD && !$produtos): ?>
            <p>Ainda não há produtos disponíveis.</p>
        <?php endif; ?>
        </div>
    </section>
    </main>

    <div class="hazard"></div>
    <footer class="foot">
    <div class="foot-grid">
        <div>
        <a href="index.php" class="brand"><span class="mark"><img src="<?= $logoMarca ?>" alt="Gamipeças" onerror="this.style.display='none';this.nextElementSibling.style.display='block'"><svg style="display:none" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M5.3 18.7l2.1-2.1M16.6 7.4l2.1-2.1"/></svg></span><span>L<b>AM</b></span></a>
        <p>Loja de peças auto B2B que liga oficinas às empresas de peças do distrito de Leiria.</p>
        </div>
        <div><h4>Plataforma</h4><a href="products.php">Peças</a><a href="parceiros.php">Parcerias</a><a href="index.php#lojas">Lojas</a></div>
        <div><h4>Conta</h4><a href="login.php">Entrar</a><a href="registo.php">Pedir acesso</a><a href="about.php">Sobre o projeto</a></div>
    </div>
    <div class="copy">© LAM · Peças auto B2B · Leiria</div>
    </footer>
    <div class="toast" id="toast"></div>
    <script src="src/animations.js"></script>
</body>
</html>