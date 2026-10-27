<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Se o utilizador já estiver logado, manda-o para o painel
if (utilizadorAutenticado()) {
    header("Location: dashboard.php");
    exit;
}

/** Valida NIF português (9 dígitos + dígito de controlo). */
function nifValido(string $nif): bool {
    if (!preg_match('/^\d{9}$/', $nif)) {
        return false;
    }
    $soma = 0;
    for ($i = 0; $i < 8; $i++) {
        $soma += (int)$nif[$i] * (9 - $i);
    }
    $controlo = 11 - ($soma % 11);
    if ($controlo >= 10) {
        $controlo = 0;
    }
    return $controlo === (int)$nif[8];
}

$erro_servidor = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = (($_POST['conta'] ?? 'empresa') === 'cliente') ? 'cliente' : 'empresa';

    $email   = trim($_POST['email'] ?? '');
    $nome    = trim($_POST['nome'] ?? '');
    $tel     = trim($_POST['tel'] ?? '');
    $morada  = trim($_POST['morada'] ?? '');
    $cp      = trim($_POST['cp'] ?? '');
    $local   = trim($_POST['local'] ?? '');
    $pw      = $_POST['pw'] ?? '';
    $pw2     = $_POST['pw2'] ?? '';
    $empresa = trim($_POST['empresa'] ?? '');
    $nif     = trim($_POST['nif'] ?? '');
    $tipoEmp = trim($_POST['tipo'] ?? '');

    $tiposPermitidos = ['Oficina', 'Centro de abate', 'Retificadora', 'Recondicionador', 'Outro'];

    if (!csrfValido()) {
        $erro_servidor = "Sessão expirada. Recarrega a página e tenta novamente.";
    } elseif ($email === '' || $nome === '' || $pw === '') {
        $erro_servidor = "Preenche todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro_servidor = "O email indicado não é válido.";
    } elseif ($tel !== '' && !preg_match('/^[0-9 +]{9,16}$/', $tel)) {
        $erro_servidor = "O telefone indicado não é válido.";
    } elseif ($pw !== $pw2) {
        $erro_servidor = "As palavras-passe não coincidem.";
    } elseif (strlen($pw) < 8) {
        $erro_servidor = "A palavra-passe deve ter pelo menos 8 caracteres.";
    } elseif (empty($_POST['termos'])) {
        $erro_servidor = "Tens de aceitar os termos de utilização e a política de privacidade.";
    } elseif ($tipo === 'empresa' && ($empresa === '' || $nif === '')) {
        $erro_servidor = "O nome da empresa e o NIF são obrigatórios.";
    } elseif ($tipo === 'empresa' && !nifValido($nif)) {
        $erro_servidor = "O NIF indicado não é válido.";
    } elseif ($tipo === 'empresa' && !in_array($tipoEmp, $tiposPermitidos, true)) {
        $erro_servidor = "Seleciona o tipo de empresa.";
    } elseif ($cp !== '' && !preg_match('/^\d{4}-\d{3}$/', $cp)) {
        $erro_servidor = "O código postal deve ter o formato 0000-000.";
    } else {
        $hash = password_hash($pw, PASSWORD_DEFAULT);

        try {
            if ($tipo === 'cliente') {
                // ATUALIZADO: Adicionados os campos morada, codigo_postal e localidade à query
                $stmt = $conn->prepare(
                    "INSERT INTO clientes (nome, email, password_hash, telefone, morada, codigo_postal, localidade) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                // ATUALIZADO: 7 strings (sssssss) para corresponder aos 7 valores
                $stmt->bind_param("sssssss", $nome, $email, $hash, $tel, $morada, $cp, $local);
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO empresas (nome_empresa, nif, tipo_empresa, email, password_hash, telefone, morada, codigo_postal, localidade)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param("sssssssss", $empresa, $nif, $tipoEmp, $email, $hash, $tel, $morada, $cp, $local);
            }
            $stmt->execute();
            $stmt->close();

            $_SESSION['registo_sucesso'] = $tipo === 'empresa'
                ? "Pedido enviado! A sua conta de empresa foi criada com sucesso."
                : "Conta criada com sucesso! Já pode iniciar sessão.";
            header("Location: login.php");
            exit;
        } catch (mysqli_sql_exception $e) {
            if ((int)$e->getCode() === 1062) {
                $erro_servidor = "Já existe uma conta com este email ou NIF.";
            } else {
                error_log("Registo: " . $e->getMessage());
                $erro_servidor = APP_DEBUG ? "Erro de base de dados: " . $e->getMessage() : "Erro ao criar conta. Tenta novamente mais tarde.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Criar conta | LAM</title>
<script>try{var t=localStorage.getItem('lisam-theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="src/backgroud.css">
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
      <a href="parceiros.php">Parcerias</a>
      <a href="about.php">Sobre o projeto</a>
      <button class="theme" id="tema" type="button" aria-label="Alternar tema claro/escuro">🌙</button>
      <a href="login.php" class="cta">Entrar</a>
    </nav>
  </div>
</header>

<div class="login-page">
  <aside class="login-side">
    <svg class="disc" viewBox="0 0 200 200" aria-hidden="true"><defs><circle id="fh" cx="100" cy="30" r="7"/></defs><circle class="a" cx="100" cy="100" r="96"/><circle class="b" cx="100" cy="100" r="82"/><circle class="a" cx="100" cy="100" r="64"/><g class="b"><use href="#fh"/><use href="#fh" transform="rotate(45 100 100)"/><use href="#fh" transform="rotate(90 100 100)"/><use href="#fh" transform="rotate(135 100 100)"/><use href="#fh" transform="rotate(180 100 100)"/><use href="#fh" transform="rotate(225 100 100)"/><use href="#fh" transform="rotate(270 100 100)"/><use href="#fh" transform="rotate(315 100 100)"/></g><circle class="b" cx="100" cy="100" r="30"/><circle class="a" cx="100" cy="100" r="22"/><g class="b"><circle cx="100" cy="86" r="4"/><circle cx="114" cy="100" r="4"/><circle cx="100" cy="114" r="4"/><circle cx="86" cy="100" r="4"/></g></svg>
    <div>
      <h1 id="sideTitulo">Junte a sua oficina à <span class="gtext">rede LAM</span>.</h1>
      <p id="sideTexto">Crie a conta da sua empresa e comece a pesquisar, reservar e receber peças auto das empresas parceiras do distrito de Leiria.</p>
      <div class="vant" id="vantEmpresa">
        <div><span>🔎</span>Pesquisa por referência OEM em todo o stock</div>
        <div><span>🛒</span>Reservas com um clique</div>
        <div><span>🚚</span>Entrega no próprio dia num raio de 30 km</div>
        <div><span>✓</span>Apenas empresas verificadas</div>
      </div>
      <div class="vant" id="vantCliente" hidden>
        <div><span>🔎</span>Encontre a peça certa pela referência ou pelo carro</div>
        <div><span>🛒</span>Reserve e levante junto de quem tem em stock</div>
        <div><span>📍</span>Peças perto de si, no distrito de Leiria</div>
        <div><span>✓</span>Vendedores verificados</div>
      </div>
    </div>
  </aside>

  <div class="form-wrap">
    <div class="card wide" id="card">
      <form id="form" method="POST" action="registo.php" novalidate>
        <?= csrfCampo() ?>
        <h2>Criar conta</h2>
        <p class="sub" id="subTxt">Pedido de acesso para empresas e oficinas.</p>

        <div class="tipo-conta" role="radiogroup" aria-label="Tipo de conta">
          <label class="opt" id="optCliente">
            <input type="radio" name="conta" value="cliente" <?= (($_POST['conta'] ?? '') === 'cliente') ? 'checked' : '' ?>>
            <span class="ico">👤</span>
            <span><strong>Cliente</strong><small>Particular</small></span>
          </label>
          <label class="opt on" id="optEmpresa">
            <input type="radio" name="conta" value="empresa" <?= (($_POST['conta'] ?? 'empresa') === 'empresa') ? 'checked' : '' ?>>
            <span class="ico">🔧</span>
            <span><strong>Empresa</strong><small>Oficina, abate, retificadora…</small></span>
          </label>
        </div>

        <?php if (!empty($erro_servidor)): ?>
          <div class="erro shake" role="alert" style="display:block"><?= h($erro_servidor) ?></div>
        <?php endif; ?>
        <div class="erro" id="erroJS" role="alert" hidden></div>

        <div class="grid2">
          <div class="campo full" data-so="empresa">
            <label for="empresa">Nome da empresa</label>
            <input id="empresa" name="empresa" autocomplete="organization" placeholder="Oficina Exemplo, Lda." value="<?= h($_POST['empresa'] ?? '') ?>">
            <p class="msg"></p>
          </div>

          <div class="campo" id="campoNif">
            <label for="nif">NIF</label>
            <input id="nif" name="nif" inputmode="numeric" maxlength="9" placeholder="123456789" value="<?= h($_POST['nif'] ?? '') ?>">
            <p class="msg"></p>
          </div>

          <div class="campo" data-so="empresa">
            <label for="tipo">Tipo de empresa</label>
            <select id="tipo" name="tipo">
              <option value="">Selecione</option>
              <?php foreach (['Oficina', 'Centro de abate', 'Retificadora', 'Recondicionador', 'Outro'] as $op): ?>
                <option value="<?= h($op) ?>" <?= (($_POST['tipo'] ?? '') === $op) ? 'selected' : '' ?>><?= h($op) ?></option>
              <?php endforeach; ?>
            </select>
            <p class="msg"></p>
          </div>

          <div class="campo">
            <label for="nome" id="nomeLbl">Responsável</label>
            <input id="nome" name="nome" autocomplete="name" placeholder="Nome completo" value="<?= h($_POST['nome'] ?? '') ?>">
            <p class="msg"></p>
          </div>

          <div class="campo">
            <label for="tel">Telefone</label>
            <input id="tel" name="tel" type="tel" autocomplete="tel" placeholder="912 345 678" value="<?= h($_POST['tel'] ?? '') ?>">
            <p class="msg"></p>
          </div>

          <div class="campo full">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" autocomplete="email" placeholder="geral@oficina.pt" value="<?= h($_POST['email'] ?? '') ?>">
            <p class="msg"></p>
          </div>

          <div class="campo full">
            <label for="morada">Morada</label>
            <input id="morada" name="morada" autocomplete="street-address" placeholder="Rua, número, andar" value="<?= h($_POST['morada'] ?? '') ?>">
            <p class="msg"></p>
          </div>

          <div class="campo">
            <label for="cp">Código postal</label>
            <input id="cp" name="cp" inputmode="numeric" maxlength="8" autocomplete="postal-code" placeholder="2400-000" value="<?= h($_POST['cp'] ?? '') ?>">
            <p class="msg"></p>
          </div>

          <div class="campo">
            <label for="local">Localidade</label>
            <input id="local" name="local" autocomplete="address-level2" placeholder="Leiria" value="<?= h($_POST['local'] ?? '') ?>">
            <p class="msg"></p>
          </div>

          <div class="campo">
            <label for="pw">Palavra-passe</label>
            <div class="pw">
              <input id="pw" name="pw" type="password" autocomplete="new-password" placeholder="Mínimo 8 caracteres">
              <button class="ver" type="button" data-alvo="pw">Mostrar</button>
            </div>
            <div class="forca"><i></i><i></i><i></i><i></i><small id="forcaTxt"></small></div>
            <p class="msg"></p>
          </div>

          <div class="campo">
            <label for="pw2">Confirmar palavra-passe</label>
            <div class="pw">
              <input id="pw2" name="pw2" type="password" autocomplete="new-password" placeholder="Repita a palavra-passe">
              <button class="ver" type="button" data-alvo="pw2">Mostrar</button>
            </div>
            <p class="msg"></p>
          </div>
        </div>

        <label class="termos"><input type="checkbox" id="termos" name="termos"><span>Li e aceito os <a href="#">termos de utilização</a> e a <a href="#">política de privacidade</a>.</span></label>
        <button class="entrar" type="submit" id="btn">Pedir acesso</button>
        <p class="novo">Já tem conta? <a href="login.php">Entrar</a></p>
      </form>
    </div>
  </div>
</div>

<script src="src/animations.js"></script>

<!-- SCRIPT ADICIONADO: Para manter o visual correto se houver erro no servidor -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Verifica se o utilizador tentou registar como cliente
    const isCliente = <?= (($_POST['conta'] ?? '') === 'cliente') ? 'true' : 'false' ?>;
    if (isCliente) {
        const optCliente = document.getElementById('optCliente');
        const optEmpresa = document.getElementById('optEmpresa');
        if (optCliente && optEmpresa) {
            optCliente.classList.add('on');
            optEmpresa.classList.remove('on');
            // Esconde os campos de empresa se necessário (dependendo do teu JS animations.js)
            document.querySelectorAll('[data-so="empresa"]').forEach(el => el.style.display = 'none');
            const campoNif = document.getElementById('campoNif');
            if(campoNif) campoNif.style.display = 'none';
            document.getElementById('btn').textContent = 'Criar conta';
        }
    }
});
</script>
</body>
</html>