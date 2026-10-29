<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/perfil.php';

if (!utilizadorAutenticado()) {
    header('Location: login.php');
    exit;
}

/* ---------- Ligação, deteção e identificação do utilizador ---------- */
try {
    $c      = def_cfg();
    $userId = def_id_utilizador();
    if ($userId === 0) {
        throw new DefErro(
            'Não consegui identificar o utilizador',
            '<p>A sessão tem estas chaves: <code>' . h(implode(', ', array_keys($_SESSION)) ?: '(vazia)') . '</code></p>'
            . '<p>Diz qual delas guarda o ID (ou o email) do utilizador.</p>'
        );
    }
} catch (DefErro $e) {
    $mostrar = def_manual()['debug'];
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="pt"><meta charset="UTF-8"><title>Erro</title>'
       . '<body style="font-family:sans-serif;max-width:640px;margin:60px auto;padding:0 20px">'
       . '<h1>' . ($mostrar ? h($e->getMessage()) : 'Não foi possível abrir as definições') . '</h1>'
       . ($mostrar ? $e->detalhe : '<p>Tenta novamente mais tarde.</p>')
       . '<p><a href="index.php">Voltar ao início</a></p></body></html>';
    exit;
}

/* ---------- Processar formulários ---------- */
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$ok  = ['perfil' => null, 'password' => null, 'foto' => null];
$err = ['perfil' => null, 'password' => null, 'foto' => null];
$formNome  = null;
$formEmail = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit('Pedido inválido. Atualiza a página e tenta novamente.');
    }

    $acao = $_POST['acao'] ?? '';
    $user = def_buscar_utilizador($userId);

    // ---- Nome e email ----
    if ($acao === 'perfil') {
        $formNome  = trim($_POST['nome'] ?? '');
        $formEmail = trim($_POST['email'] ?? '');

        if ($formNome === '' || mb_strlen($formNome) > 100) {
            $err['perfil'] = 'Indica um nome com até 100 caracteres.';
        } elseif (!filter_var($formEmail, FILTER_VALIDATE_EMAIL)) {
            $err['perfil'] = 'Indica um email válido.';
        } else {
            $dup = def_select(
                "SELECT {$c['c_id']} AS id FROM {$c['tabela']}
                 WHERE {$c['c_email']} = ? AND {$c['c_id']} <> ?",
                [$formEmail, $userId]
            );
            if ($dup) {
                $err['perfil'] = 'Esse email já está a ser usado por outra conta.';
            } else {
                def_exec(
                    "UPDATE {$c['tabela']} SET {$c['c_nome']} = ?, {$c['c_email']} = ?
                     WHERE {$c['c_id']} = ?",
                    [$formNome, $formEmail, $userId]
                );
                // Mantém a sessão alinhada com a base de dados
                foreach (['nome', 'nome_empresa'] as $k) {
                    if (isset($_SESSION[$k])) {
                        $_SESSION[$k] = $formNome;
                    }
                }
                if (isset($_SESSION['email'])) {
                    $_SESSION['email'] = $formEmail;
                }
                $ok['perfil'] = 'Informações guardadas.';
                $formNome = $formEmail = null;
            }
        }
    }

    // ---- Palavra-passe ----
    if ($acao === 'password') {
        $atual     = $_POST['password_atual'] ?? '';
        $nova      = $_POST['password_nova'] ?? '';
        $confirmar = $_POST['password_confirmar'] ?? '';

        $r = def_select(
            "SELECT {$c['c_pass']} AS pass FROM {$c['tabela']} WHERE {$c['c_id']} = ?",
            [$userId]
        );
        $hash = $r[0]['pass'] ?? '';

        if (!$hash || !password_verify($atual, $hash)) {
            $err['password'] = 'A palavra-passe atual está incorreta.';
        } elseif (strlen($nova) < 8) {
            $err['password'] = 'A nova palavra-passe deve ter pelo menos 8 caracteres.';
        } elseif ($nova !== $confirmar) {
            $err['password'] = 'A confirmação não coincide com a nova palavra-passe.';
        } elseif (password_verify($nova, $hash)) {
            $err['password'] = 'A nova palavra-passe tem de ser diferente da atual.';
        } else {
            def_exec(
                "UPDATE {$c['tabela']} SET {$c['c_pass']} = ? WHERE {$c['c_id']} = ?",
                [password_hash($nova, PASSWORD_DEFAULT), $userId]
            );
            session_regenerate_id(true);
            $ok['password'] = 'Palavra-passe alterada.';
        }
    }

    // ---- Carregar foto ----
    if ($acao === 'foto') {
        $f = $_FILES['foto'] ?? null;

        if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) {
            $err['foto'] = 'Escolhe uma imagem primeiro.';
        } elseif ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > AVATAR_MAX) {
            $err['foto'] = 'A imagem não pode ter mais de 2 MB.';
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
            if (!isset(AVATAR_TIPOS[$mime]) || @getimagesize($f['tmp_name']) === false) {
                $err['foto'] = 'Usa uma imagem JPG, PNG ou WebP.';
            } else {
                if (!is_dir(AVATAR_DIR)) {
                    mkdir(AVATAR_DIR, 0755, true);
                }
                $novoNome = $userId . '_' . bin2hex(random_bytes(8)) . '.' . AVATAR_TIPOS[$mime];

                if (move_uploaded_file($f['tmp_name'], AVATAR_DIR . $novoNome)) {
                    def_apagar_foto($user['foto'] ?? null);
                    def_exec(
                        "UPDATE {$c['tabela']} SET {$c['c_foto']} = ? WHERE {$c['c_id']} = ?",
                        [$novoNome, $userId]
                    );
                    $ok['foto'] = 'Foto de perfil atualizada.';
                } else {
                    $err['foto'] = 'Não foi possível guardar a imagem. Tenta novamente.';
                }
            }
        }
    }

    // ---- Remover foto ----
    if ($acao === 'remover_foto') {
        def_apagar_foto($user['foto'] ?? null);
        def_exec(
            "UPDATE {$c['tabela']} SET {$c['c_foto']} = NULL WHERE {$c['c_id']} = ?",
            [$userId]
        );
        $ok['foto'] = 'Foto removida.';
    }
}

/* ---------- Dados para mostrar ---------- */
$user = def_buscar_utilizador($userId);
if (!$user) {
    header('Location: login.php');
    exit;
}

$temFoto = !empty($user['foto']) && is_file(AVATAR_DIR . basename($user['foto']));
$inicial = mb_strtoupper(mb_substr($user['nome'] ?? '?', 0, 1));
$desde   = !empty($user['criado_em']) ? date('d/m/Y', strtotime($user['criado_em'])) : null;

// Opções do cabeçalho partilhado
$paginaAtiva = '';
$cabecalhoJs = true; // esta página não carrega animations.js
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Definições · LAM</title>
    <script>try{var t=localStorage.getItem('lisam-theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/backgroud.css">
    <style>
        /* Usa as cores herdadas do tema (claro/escuro): nada de fundos fixos. */
        .def-wrap { max-width: 720px; margin: 0 auto; padding: 40px 20px 60px; text-align: left; }
        .def-wrap h1 { margin: 0 0 4px; }
        .def-sub { opacity: .7; margin: 0 0 32px; }
        .def-card { background: color-mix(in srgb, currentColor 5%, transparent);
                    border: 1px solid color-mix(in srgb, currentColor 22%, transparent);
                    border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .def-card h2 { margin: 0 0 16px; font-size: 1.15rem; }
        .def-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
        .def-field label { font-weight: 600; font-size: .9rem; }
        .def-field input { padding: 10px 12px; border-radius: 8px; font: inherit; color: inherit;
                           background: color-mix(in srgb, currentColor 6%, transparent);
                           border: 1px solid color-mix(in srgb, currentColor 40%, transparent); }
        .def-field input:focus-visible, .def-btn:focus-visible { outline: 3px solid #4c8dff; outline-offset: 2px; }
        .def-field small { opacity: .7; }
        .def-row { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
        .def-btn { padding: 10px 18px; border-radius: 8px; border: 0; cursor: pointer; font: inherit;
                   font-weight: 700; background: #ffd500; color: #000; }
        .def-btn:hover { filter: brightness(.95); }
        .def-btn.sec { background: transparent; color: inherit;
                       border: 1px solid color-mix(in srgb, currentColor 50%, transparent); }
        .def-ok, .def-err { padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid; }
        .def-ok  { background: color-mix(in srgb, #1f9d55 18%, transparent); border-color: #1f9d55; }
        .def-err { background: color-mix(in srgb, #d93025 18%, transparent); border-color: #d93025; }
        .def-avatar { width: 112px; height: 112px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
        .def-avatar.ph { display: flex; align-items: center; justify-content: center; font-size: 2.6rem;
                         font-weight: 700; background: #ffd500; color: #000; }
        .def-foto { display: flex; gap: 24px; align-items: center; flex-wrap: wrap; }
        .def-foto form + form { margin-top: 8px; }
        .def-meta { margin: 0 0 16px; opacity: .75; font-size: .9rem; }
    </style>
</head>
<body>
<?php require __DIR__ . '/includes/cabecalho.php'; ?>

<main class="def-wrap">
    <h1>Definições da conta</h1>
    <p class="def-sub">Gere as tuas informações, a palavra-passe e a foto de perfil.</p>

    <!-- Foto de perfil -->
    <section class="def-card" aria-labelledby="t-foto">
        <h2 id="t-foto">Foto de perfil</h2>
        <?php if ($ok['foto']): ?><div class="def-ok" role="status"><?= h($ok['foto']) ?></div><?php endif; ?>
        <?php if ($err['foto']): ?><div class="def-err" role="alert"><?= h($err['foto']) ?></div><?php endif; ?>

        <div class="def-foto">
            <?php if ($temFoto): ?>
                <img class="def-avatar" src="<?= h(AVATAR_URL . basename($user['foto'])) ?>" alt="A tua foto de perfil">
            <?php else: ?>
                <div class="def-avatar ph" aria-hidden="true"><?= h($inicial) ?></div>
            <?php endif; ?>

            <div>
                <form method="post" enctype="multipart/form-data" class="def-field">
                    <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
                    <input type="hidden" name="acao" value="foto">
                    <label for="foto">Escolher nova imagem</label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" required>
                    <small>JPG, PNG ou WebP, até 2 MB.</small>
                    <div class="def-row"><button class="def-btn" type="submit">Guardar foto</button></div>
                </form>

                <?php if ($temFoto): ?>
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
                        <input type="hidden" name="acao" value="remover_foto">
                        <button class="def-btn sec" type="submit">Remover foto</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Informações da conta -->
    <section class="def-card" aria-labelledby="t-perfil">
        <h2 id="t-perfil">Informações da conta</h2>
        <?php if ($ok['perfil']): ?><div class="def-ok" role="status"><?= h($ok['perfil']) ?></div><?php endif; ?>
        <?php if ($err['perfil']): ?><div class="def-err" role="alert"><?= h($err['perfil']) ?></div><?php endif; ?>

        <?php if ($desde): ?><p class="def-meta">Conta criada em <?= h($desde) ?></p><?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
            <input type="hidden" name="acao" value="perfil">

            <div class="def-field">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" maxlength="100" required
                       value="<?= h($formNome ?? $user['nome']) ?>">
            </div>
            <div class="def-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required
                       value="<?= h($formEmail ?? $user['email']) ?>">
            </div>
            <button class="def-btn" type="submit">Guardar informações</button>
        </form>
    </section>

    <!-- Palavra-passe -->
    <section class="def-card" aria-labelledby="t-pass">
        <h2 id="t-pass">Palavra-passe</h2>
        <?php if ($ok['password']): ?><div class="def-ok" role="status"><?= h($ok['password']) ?></div><?php endif; ?>
        <?php if ($err['password']): ?><div class="def-err" role="alert"><?= h($err['password']) ?></div><?php endif; ?>

        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
            <input type="hidden" name="acao" value="password">

            <div class="def-field">
                <label for="password_atual">Palavra-passe atual</label>
                <input type="password" id="password_atual" name="password_atual" autocomplete="current-password" required>
            </div>
            <div class="def-field">
                <label for="password_nova">Nova palavra-passe</label>
                <input type="password" id="password_nova" name="password_nova" minlength="8" autocomplete="new-password" required>
                <small>Mínimo de 8 caracteres.</small>
            </div>
            <div class="def-field">
                <label for="password_confirmar">Confirmar nova palavra-passe</label>
                <input type="password" id="password_confirmar" name="password_confirmar" minlength="8" autocomplete="new-password" required>
            </div>
            <button class="def-btn" type="submit">Alterar palavra-passe</button>
        </form>
    </section>

    <a href="index.php" class="btn btn-primary">Voltar ao início</a>
</main>
</body>
</html>