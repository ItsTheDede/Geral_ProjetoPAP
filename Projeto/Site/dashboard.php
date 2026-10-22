<?php
require_once __DIR__ . '/includes/auth.php';
exigirLogin(); // se não estiver logado → vai para login.php

$tipo = $_SESSION['user_tipo'];
$nome = $_SESSION['user_nome'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel | LAM</title>
  <script>try{var t=localStorage.getItem('lisam-theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
  <link rel="stylesheet" href="src/backgroud.css">
</head>
<body>
  <header class="top">
    <div class="top-inner">
      <a href="index.php" class="brand"><span>L<b>AM</b></span></a>
      <nav>
        <span>Olá, <?= h($nome) ?> (<?= h($tipo) ?>)</span>
        <a href="logout.php" class="cta">Sair</a>
      </nav>
    </div>
  </header>
  <main class="wrap" style="padding:40px">
    <h1>Painel de <?= $tipo === 'cliente' ? 'Cliente' : 'Empresa' ?></h1>
    <p>Bem-vindo, <?= h($nome) ?>. Esta área só é acessível a utilizadores autenticados.</p>
  </main>
</body>
</html>