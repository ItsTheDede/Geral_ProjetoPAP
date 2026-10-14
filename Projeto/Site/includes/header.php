<?php
if (!isset($pageTitle)) $pageTitle = 'LAM, marketplace automóvel B2B';
if (!isset($activeNav)) $activeNav = '';
if (!isset($pageCss))   $pageCss   = [];   // caminhos relativos a assets/css/
if (!isset($bodyPage))  $bodyPage  = '';   // home | stock | login
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>

<!-- Anti-flash: tem de ser inline, corre antes do CSS -->
<script>try{var t=localStorage.getItem('lisam-theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="scr/backgroud.css">

<?php foreach ($pageCss as $css): ?>
  <link rel="stylesheet" href="<?= e($css) ?>">
<?php endforeach; ?>
</head>
<body data-page="<?= e($bodyPage) ?>">

<header class="top">
  <div class="top-inner">
    <a href="index.php" class="brand">
      <span class="mark">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M5.3 18.7l2.1-2.1M16.6 7.4l2.1-2.1"/></svg>
      </span>
      <span>L<b>AM</b></span>
    </a>
    <button class="burger" aria-label="Menu">☰</button>
    <nav>
      <a href="index.php" class="<?= $activeNav === 'inicio' ? 'active' : '' ?>">Início</a>
      <a href="products.php" class="<?= $activeNav === 'stock'  ? 'active' : '' ?>">Produtos</a>
      <button class="theme" id="tema" type="button" aria-label="Alternar tema claro/escuro">🌙</button>
      <?php if (!empty($_SESSION['user'])): ?>
        <a href="logout.php" class="cta">Sair (<?= e($_SESSION['user']['nome']) ?>)</a>
      <?php else: ?>
        <a href="login.php" class="cta">Entrar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>