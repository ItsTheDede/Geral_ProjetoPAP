<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Se já está autenticado, vai para o dashboard
if (utilizadorAutenticado()) {
    header("Location: dashboard.php");
    exit;
}

// Mensagem vinda do registo (lida uma só vez)
$sucesso = $_SESSION['registo_sucesso'] ?? '';
unset($_SESSION['registo_sucesso']);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email   = trim($_POST['email'] ?? '');
    $pass    = $_POST['pass'] ?? '';
    $tipo    = (($_POST['conta'] ?? 'empresa') === 'cliente') ? 'cliente' : 'empresa';
    $lembrar = !empty($_POST['lembrar']);

    if (!csrfValido()) {
        $erro = "Sessão expirada. Recarrega a página e tenta novamente.";
    } elseif ($email === '' || $pass === '') {
        $erro = "Preenche o email e a palavra-passe.";
    } else {
        try {
            // $tabela e $colNome vêm de uma lista fixa, nunca do utilizador
            $tabela  = $tipo === 'cliente' ? 'clientes' : 'empresas';
            $colNome = $tipo === 'cliente' ? 'nome' : 'nome_empresa';

            $stmt = $conn->prepare("SELECT id, email, password_hash, $colNome AS nome FROM $tabela WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $u = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($u && password_verify($pass, $u['password_hash'])) {
                fazerLogin((int)$u['id'], $tipo, $u['nome'], $u['email'], $lembrar);
                header("Location: dashboard.php");
                exit;
            }
            $erro = "Credenciais inválidas. Verifica o email e a palavra-passe.";
        } catch (mysqli_sql_exception $e) {
            error_log("Login: " . $e->getMessage());
            $erro = APP_DEBUG ? "Erro de base de dados: " . $e->getMessage() : "Ocorreu um erro. Tenta novamente mais tarde.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | LAM</title>
    <script>try{var t=localStorage.getItem('lisam-theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/backgroud.css">
</head>
<body>
    <header class="top">
      <div class="top-inner">
        <a href="index.php" class="brand">
          <span class="mark"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M5.3 18.7l2.1-2.1M16.6 7.4l2.1-2.1"/></svg></span>
          <span>L<b>AM</b></span>
        </a>
        <nav>
          <a href="index.php">Início</a>
          <a href="products.php">Produtos</a>
          <a href="parceiros.php">Parcerias</a>
          <a href="about.php">Sobre o projeto</a>
          <a href="login.php" class="cta">Entrar</a>
        </nav>
      </div>
    </header>

    <main class="login-page">
      <div class="login-side">
        <div>
          <span class="eyebrow">Área de parceiros</span>
          <h1>Bem-vindo de volta à <span class="gtext">oficina</span>.</h1>
          <p>Entre na sua conta para reservar peças auto, acompanhar encomendas e levantar na loja ou receber no próprio dia.</p>
        </div>
      </div>

      <div class="form-wrap">
        <form class="card" method="post" action="login.php" novalidate>
          <?= csrfCampo() ?>
          <h2>Entrar</h2>
          <p class="sub">Acesso para clientes e empresas parceiras.</p>

          <?php if ($sucesso): ?>
            <p class="ok" style="display:block" role="status"><?= h($sucesso) ?></p>
          <?php endif; ?>

          <div class="tipo-conta" role="radiogroup" aria-label="Tipo de conta">
            <label class="opt" id="optCliente">
              <input type="radio" name="conta" value="cliente" <?= (($_POST['conta'] ?? '') === 'cliente') ? 'checked' : '' ?>>
              <span class="ico">👤</span>
              <span><strong>Cliente</strong><small>Particular</small></span>
            </label>
            <label class="opt on" id="optEmpresa">
              <input type="radio" name="conta" value="empresa" <?= (($_POST['conta'] ?? 'empresa') === 'empresa') ? 'checked' : '' ?>>
              <span class="ico">🔧</span>
              <span><strong>Empresa</strong><small>Oficina ou outra</small></span>
            </label>
          </div>

          <div class="campo">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="oficina@exemplo.pt"
                   autocomplete="email" required
                   value="<?= h($_POST['email'] ?? '') ?>">
          </div>

          <div class="campo">
            <label for="pass">Palavra-passe</label>
            <div class="pw">
              <input type="password" id="pass" name="pass" placeholder="••••••••" autocomplete="current-password" required>
              <button type="button" class="ver" id="ver">Mostrar</button>
            </div>
          </div>

          <div class="linha">
            <label><input type="checkbox" name="lembrar"> Manter sessão iniciada</label>
            <a href="#">Esqueci-me da palavra-passe</a>
          </div>

          <?php if ($erro): ?>
            <p class="erro" style="display:block" role="alert"><?= h($erro) ?></p>
          <?php endif; ?>

          <button type="submit" class="entrar">Entrar</button>

          <p class="novo">Ainda não tem conta? <a href="registo.php">Criar conta</a></p>
        </form>
      </div>
    </main>

    <script src="src/animations.js"></script>
</body>
</html>