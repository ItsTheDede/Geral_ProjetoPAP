<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Se não estiver logado, manda-o para o login
if (!utilizadorAutenticado()) { 
    header("Location: login.php"); 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Definições | LAM</title>
    <link rel="stylesheet" href="src/backgroud.css">
</head>
<body style="padding: 40px; text-align: center;">
    <h1>Definições da Conta</h1>
    <p>Página em construção.</p>
    <a href="index.php" class="btn btn-primary" style="margin-top:20px;">Voltar ao Início</a>
</body>
</html>