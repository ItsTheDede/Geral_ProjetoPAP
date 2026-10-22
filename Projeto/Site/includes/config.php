<?php
// includes/config.php

// Em produção, muda para false (esconde erros técnicos do público)
const APP_DEBUG = true;

ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('display_startup_errors', APP_DEBUG ? '1' : '0');
error_reporting(E_ALL);

// Faz o mysqli lançar exceções de forma consistente (PHP 7 e 8)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "127.0.0.1";
$port = 3306;
$user = "root";
$pass = ""; // XAMPP normalmente não tem password
$db   = "projeto_pap";

try {
    $conn = new mysqli($host, $user, $pass, $db, $port);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Erro de ligação à BD: " . $e->getMessage());
    die(APP_DEBUG ? "Erro de ligação: " . $e->getMessage() : "Serviço temporariamente indisponível.");
}