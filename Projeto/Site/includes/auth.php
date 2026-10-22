<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', '2592000'); // 30 dias (para "manter sessão")
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

/** Escapa texto para HTML. */
function h(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/**
 * Verifica se há um utilizador autenticado.
 * $tipo: 'cliente' | 'empresa' | null (qualquer)
 */
function utilizadorAutenticado(?string $tipo = null): bool {
    if (empty($_SESSION['user_id']) || empty($_SESSION['user_tipo'])) {
        return false;
    }
    if ($tipo !== null && $_SESSION['user_tipo'] !== $tipo) {
        return false;
    }
    return true;
}

/** Redireciona para login se não estiver autenticado. */
function exigirLogin(?string $tipo = null): void {
    if (!utilizadorAutenticado($tipo)) {
        header("Location: login.php");
        exit;
    }
}

/** Faz login: guarda dados na sessão. */
function fazerLogin(int $id, string $tipo, string $nome, string $email, bool $lembrar = false): void {
    session_regenerate_id(true); // previne session fixation
    $_SESSION['user_id']    = $id;
    $_SESSION['user_tipo']  = $tipo; // 'cliente' ou 'empresa'
    $_SESSION['user_nome']  = $nome;
    $_SESSION['user_email'] = $email;

    if ($lembrar) {
        $p = session_get_cookie_params();
        setcookie(session_name(), session_id(), [
            'expires'  => time() + 60 * 60 * 24 * 30,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

/** Termina sessão. */
function fazerLogout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }
    session_destroy();
}

/* ---------- CSRF ---------- */

function csrfToken(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Campo hidden para pôr dentro de cada <form method="post">. */
function csrfCampo(): string {
    return '<input type="hidden" name="csrf" value="' . h(csrfToken()) . '">';
}

/** Valida o token enviado em POST. */
function csrfValido(): bool {
    return !empty($_SESSION['csrf'])
        && isset($_POST['csrf'])
        && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']);
}