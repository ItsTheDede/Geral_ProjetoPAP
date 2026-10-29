<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Garante que a sessão existe (necessária para $_SESSION e para o limite de tentativas)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se já está autenticado, vai para o index
if (utilizadorAutenticado()) {
    header("Location: index.php.php");
    exit;
}

// Imagem de fundo do painel da esquerda
const LOGIN_FUNDO = 'assets/images/social/imagess.svg';

// Mensagem vinda do registo (lida uma só vez)
$sucesso = $_SESSION['registo_sucesso'] ?? '';
unset($_SESSION['registo_sucesso']);

// Limite de tentativas falhadas (por sessão)
const LOGIN_MAX_TENTATIVAS = 5;
const LOGIN_BLOQUEIO_SEG   = 300; // 5 minutos

$erro  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email   = trim($_POST['email'] ?? '');
    $pass    = $_POST['pass'] ?? '';
    $lembrar = !empty($_POST['lembrar']);

    $bloqueadoAte = $_SESSION['login_bloqueado_ate'] ?? 0;

    if (!csrfValido()) {
        $erro = "Sessão expirada. Recarrega a página e tenta novamente.";
    } elseif ($bloqueadoAte > time()) {
        $minutos = (int)ceil(($bloqueadoAte - time()) / 60);
        $erro = "Demasiadas tentativas. Tenta novamente dentro de $minutos minuto(s).";
    } elseif ($email === '' || $pass === '') {
        $erro = "Preenche o email e a palavra-passe.";
    } else {
        try {
            // Tabelas e colunas vêm desta lista fixa, nunca do utilizador.
            // Como já não há escolha de tipo de conta, procura nas duas.
            $contas = [
                'empresa' => ['tabela' => 'empresas', 'col' => 'nome_empresa'],
                'cliente' => ['tabela' => 'clientes', 'col' => 'nome'],
            ];

            $u = null;
            $tipo = null;
            $emailExiste = false;

            foreach ($contas as $t => $c) {
                $stmt = $conn->prepare("SELECT id, email, password_hash, {$c['col']} AS nome FROM {$c['tabela']} WHERE email = ? LIMIT 1");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($row) {
                    $emailExiste = true;
                    if (password_verify($pass, $row['password_hash'])) {
                        $u = $row;
                        $tipo = $t;
                        break;
                    }
                }
            }

            if (!$emailExiste) {
                // Trabalho equivalente quando o email não existe, para o tempo
                // de resposta não revelar que contas existem
                password_hash($pass, PASSWORD_DEFAULT);
            }

            if ($u) {
                unset($_SESSION['login_falhas'], $_SESSION['login_bloqueado_ate']);
                fazerLogin((int)$u['id'], $tipo, $u['nome'], $u['email'], $lembrar);
                header("Location: index.php");
                exit;
            }

            // Falhou: conta a tentativa e bloqueia se exceder o limite
            $_SESSION['login_falhas'] = ($_SESSION['login_falhas'] ?? 0) + 1;
            if ($_SESSION['login_falhas'] >= LOGIN_MAX_TENTATIVAS) {
                $_SESSION['login_bloqueado_ate'] = time() + LOGIN_BLOQUEIO_SEG;
                $_SESSION['login_falhas'] = 0;
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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/backgroud.css">
    <style>
        /* Imagem de fundo no painel da esquerda, com escurecimento para o texto ler-se bem */
        .login-side {
            background-image:
                linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.7)),
                url('<?= h(LOGIN_FUNDO) ?>');
            background-size: cover;
            background-position: center bottom;
            background-repeat: no-repeat;
        }
    </style>
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
          <h1>Peças certas, <span class="gtext">sem espera</span>.</h1>
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

          <div class="campo">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="oficina@exemplo.pt"
                   autocomplete="email" required
                   value="<?= h($email) ?>">
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
            <a href="recuperar.php">Esqueci-me da palavra-passe</a>
          </div>

          <?php if ($erro): ?>
            <p class="erro" style="display:block" role="alert"><?= h($erro) ?></p>
          <?php endif; ?>

          <button type="submit" class="entrar">Entrar</button>

          <p class="novo">Ainda não tem conta?<a href="registro.php">Criar conta</a></p>
        </form>
      </div>
    </main>

    <script src="src/animations.js"></script>
</body>
</html>