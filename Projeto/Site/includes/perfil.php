<?php
require_once __DIR__ . '/config.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('h')) {
    function h(mixed $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}

const AVATAR_DIR   = __DIR__ . '/../uploads/avatars/';
const AVATAR_URL   = 'uploads/avatars/';
const AVATAR_MAX   = 2 * 1024 * 1024; // 2 MB
const AVATAR_TIPOS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

/**
 * Configuração manual. Deixa '' para o código detetar sozinho.
 * Depois de confirmares que a deteção está certa, preenche tudo aqui e põe 'debug' => false.
 */
function def_manual(): array
{
    return [
        'tabela'   => '',   // ex.: 'users'
        'c_id'     => '',   // ex.: 'id'
        'c_nome'   => '',   // ex.: 'nome_empresa'
        'c_email'  => '',   // ex.: 'email'
        'c_pass'   => '',   // ex.: 'password'
        'c_foto'   => '',   // ex.: 'foto' (criada automaticamente se não existir)
        'c_criado' => '',   // ex.: 'criado_em' (opcional)
        'debug'    => true, // true: mostra detalhes técnicos nos erros. Põe false em produção.

        // Só usado se o config.php não deixar nenhuma ligação disponível.
        'fallback' => ['host' => 'localhost', 'db' => '', 'user' => 'root', 'pass' => ''],
    ];
}

class DefErro extends RuntimeException
{
    public string $detalhe;

    public function __construct(string $titulo, string $detalhe = '')
    {
        parent::__construct($titulo);
        $this->detalhe = $detalhe;
    }
}

/* ============================ LIGAÇÃO À BD ============================ */

function def_ligacao()
{
    static $lig = null;
    if ($lig !== null) {
        return $lig;
    }

    // 1) Nomes habituais, antes de varrer tudo
    foreach (['pdo', 'conn', 'con', 'db', 'mysqli', 'conexao', 'ligacao', 'link'] as $nome) {
        $v = $GLOBALS[$nome] ?? null;
        if ($v instanceof PDO || $v instanceof mysqli) {
            return $lig = $v;
        }
    }
    // 2) Qualquer global que seja PDO/mysqli
    foreach ($GLOBALS as $v) {
        if ($v instanceof PDO || $v instanceof mysqli) {
            return $lig = $v;
        }
    }
    // 3) Fallback manual
    $f = def_manual()['fallback'];
    if ($f['db'] !== '') {
        return $lig = new PDO(
            "mysql:host={$f['host']};dbname={$f['db']};charset=utf8mb4",
            $f['user'],
            $f['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
    throw new DefErro(
        'Não encontrei a ligação à base de dados',
        '<p>O <code>includes/config.php</code> não deixa nenhuma variável PDO ou mysqli disponível.</p>'
        . '<p>Preenche o campo <code>fallback</code> em <code>def_manual()</code> (includes/perfil.php).</p>'
    );
}

function def_select(string $sql, array $params = []): array
{
    $lig = def_ligacao();
    if ($lig instanceof PDO) {
        $st = $lig->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    $st = $lig->prepare($sql);
    if ($params) {
        $st->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $st->execute();
    $res = $st->get_result();
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function def_exec(string $sql, array $params = []): void
{
    $lig = def_ligacao();
    if ($lig instanceof PDO) {
        $lig->prepare($sql)->execute($params);
        return;
    }
    $st = $lig->prepare($sql);
    if ($params) {
        $st->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $st->execute();
}

/* ====================== DETEÇÃO TABELA / COLUNAS ====================== */

function def_achar_coluna(array $campos, string $manual, string $regex): string
{
    if ($manual !== '' && in_array($manual, $campos, true)) {
        return $manual;
    }
    foreach ($campos as $c) {
        if (preg_match($regex, $c)) {
            return $c;
        }
    }
    return '';
}

function def_detetar(): array
{
    $M = def_manual();

    $tabelas = array_map(fn($r) => (string) array_values($r)[0], def_select('SHOW TABLES'));
    if (!$tabelas) {
        throw new DefErro('A base de dados está vazia', '<p>Não existem tabelas na base de dados ligada.</p>');
    }

    // 1) Tabela
    $tabela = $M['tabela'];
    if ($tabela === '' || !in_array($tabela, $tabelas, true)) {
        $tabela = '';
        $preferidas = ['utilizadores', 'utilizador', 'users', 'user', 'usuarios', 'usuario',
                       'clientes', 'cliente', 'contas', 'conta', 'membros', 'registos', 'empresas'];
        foreach ($preferidas as $p) {
            foreach ($tabelas as $t) {
                if (strtolower($t) === $p) {
                    $tabela = $t;
                    break 2;
                }
            }
        }
        if ($tabela === '') {
            foreach ($tabelas as $t) {
                $campos = array_column(def_select("DESCRIBE `$t`"), 'Field');
                if (def_achar_coluna($campos, '', '/mail/i') !== ''
                    && def_achar_coluna($campos, '', '/pass|senha|palavra/i') !== '') {
                    $tabela = $t;
                    break;
                }
            }
        }
        if ($tabela === '') {
            throw new DefErro(
                'Não encontrei a tabela de utilizadores',
                '<p>Tabelas existentes: <code>' . h(implode(', ', $tabelas)) . '</code></p>'
                . '<p>Preenche <code>\'tabela\'</code> em <code>def_manual()</code>.</p>'
            );
        }
    }

    // 2) Colunas
    $desc   = def_select("DESCRIBE `$tabela`");
    $campos = array_column($desc, 'Field');

    $id = '';
    if ($M['c_id'] !== '' && in_array($M['c_id'], $campos, true)) {
        $id = $M['c_id'];
    } else {
        foreach ($desc as $d) {
            if (($d['Key'] ?? '') === 'PRI') {
                $id = $d['Field'];
                break;
            }
        }
        if ($id === '' && in_array('id', $campos, true)) {
            $id = 'id';
        }
    }

    $email = def_achar_coluna($campos, $M['c_email'], '/mail/i');
    $pass  = def_achar_coluna($campos, $M['c_pass'], '/pass|senha|palavra/i');

    $nome = '';
    if ($M['c_nome'] !== '' && in_array($M['c_nome'], $campos, true)) {
        $nome = $M['c_nome'];
    } else {
        $minusculas = array_map('strtolower', $campos);
        foreach (['nome', 'name', 'username', 'nome_utilizador', 'nome_completo', 'nome_empresa', 'utilizador', 'user'] as $n) {
            $pos = array_search($n, $minusculas, true);
            if ($pos !== false) {
                $nome = $campos[$pos];
                break;
            }
        }
        if ($nome === '') {
            foreach ($campos as $c) {
                if ($c !== $id && $c !== $email && $c !== $pass
                    && !preg_match('/(^|_)id$/i', $c) && preg_match('/nome|name|user/i', $c)) {
                    $nome = $c;
                    break;
                }
            }
        }
    }

    if ($id === '' || $nome === '' || $email === '' || $pass === '') {
        throw new DefErro(
            'Não consegui identificar todas as colunas',
            '<p>Tabela usada: <code>' . h($tabela) . '</code></p>'
            . '<p>Colunas existentes: <code>' . h(implode(', ', $campos)) . '</code></p>'
            . '<p>Detetado &rarr; id: <code>' . h($id ?: '?') . '</code>, nome: <code>' . h($nome ?: '?')
            . '</code>, email: <code>' . h($email ?: '?') . '</code>, password: <code>' . h($pass ?: '?') . '</code></p>'
            . '<p>Preenche os campos em falta em <code>def_manual()</code>.</p>'
        );
    }

    $foto = def_achar_coluna($campos, $M['c_foto'], '/foto|avatar|imagem|photo|image/i');
    if ($foto === '') {
        $foto = $M['c_foto'] !== '' ? $M['c_foto'] : 'foto';
        try {
            def_exec("ALTER TABLE `$tabela` ADD COLUMN `$foto` VARCHAR(255) NULL");
        } catch (Throwable $e) {
            throw new DefErro(
                'Não consegui criar a coluna da foto',
                '<p>Corre isto no phpMyAdmin (separador SQL) e recarrega a página:</p>'
                . '<pre>ALTER TABLE `' . h($tabela) . '` ADD COLUMN `' . h($foto) . '` VARCHAR(255) NULL;</pre>'
                . '<p>Erro: ' . h($e->getMessage()) . '</p>'
            );
        }
    }

    $criado = def_achar_coluna($campos, $M['c_criado'], '/criado|created|registo|registro|data_cri/i');

    return [
        'tabela'   => "`$tabela`",
        'c_id'     => "`$id`",
        'c_nome'   => "`$nome`",
        'c_email'  => "`$email`",
        'c_pass'   => "`$pass`",
        'c_foto'   => "`$foto`",
        'c_criado' => $criado !== '' ? "`$criado`" : '',
    ];
}

/** Configuração detetada (guardada na sessão para não repetir SHOW TABLES/DESCRIBE a cada página). */
function def_cfg(): array
{
    static $cfg = null;
    if ($cfg !== null) {
        return $cfg;
    }
    $hash = md5(serialize(def_manual()));
    if (!empty($_SESSION['def_cfg']['dados']) && ($_SESSION['def_cfg']['hash'] ?? '') === $hash) {
        return $cfg = $_SESSION['def_cfg']['dados'];
    }
    $cfg = def_detetar();
    $_SESSION['def_cfg'] = ['hash' => $hash, 'dados' => $cfg];
    return $cfg;
}

/* ============================ UTILIZADOR ============================ */

function def_id_utilizador(): int
{
    $chavesId = ['utilizador_id', 'user_id', 'id_utilizador', 'id_user', 'userid', 'uid', 'id'];
    foreach ($chavesId as $k) {
        if (!empty($_SESSION[$k]) && is_numeric($_SESSION[$k])) {
            return (int) $_SESSION[$k];
        }
    }
    foreach (['utilizador', 'user', 'usuario', 'utilizador_dados'] as $k) {
        if (isset($_SESSION[$k]) && is_array($_SESSION[$k])) {
            foreach ($chavesId as $sub) {
                if (!empty($_SESSION[$k][$sub]) && is_numeric($_SESSION[$k][$sub])) {
                    return (int) $_SESSION[$k][$sub];
                }
            }
        }
    }
    $c = def_cfg();
    foreach (['email', 'utilizador_email', 'user_email'] as $k) {
        if (!empty($_SESSION[$k]) && filter_var($_SESSION[$k], FILTER_VALIDATE_EMAIL)) {
            $r = def_select(
                "SELECT {$c['c_id']} AS id FROM {$c['tabela']} WHERE {$c['c_email']} = ?",
                [$_SESSION[$k]]
            );
            if ($r) {
                return (int) $r[0]['id'];
            }
        }
    }
    return 0;
}

function def_buscar_utilizador(int $id): array
{
    $c = def_cfg();
    $criado = $c['c_criado'] !== '' ? ", {$c['c_criado']} AS criado_em" : '';
    $r = def_select(
        "SELECT {$c['c_id']} AS id, {$c['c_nome']} AS nome, {$c['c_email']} AS email,
                {$c['c_foto']} AS foto $criado
         FROM {$c['tabela']} WHERE {$c['c_id']} = ?",
        [$id]
    );
    return $r[0] ?? [];
}

function def_apagar_foto(?string $ficheiro): void
{
    if ($ficheiro && is_file(AVATAR_DIR . basename($ficheiro))) {
        unlink(AVATAR_DIR . basename($ficheiro));
    }
}

/** Nome e foto para o menu do cabeçalho. Nunca lança erro: se algo falhar, usa a sessão. */
function def_dados_menu(): array
{
    $dados = [
        'nome' => $_SESSION['nome'] ?? $_SESSION['nome_empresa'] ?? 'Utilizador',
        'foto' => '',
    ];
    try {
        $id = def_id_utilizador();
        if ($id > 0) {
            $u = def_buscar_utilizador($id);
            if (!empty($u['nome'])) {
                $dados['nome'] = $u['nome'];
            }
            if (!empty($u['foto']) && is_file(AVATAR_DIR . basename($u['foto']))) {
                $dados['foto'] = AVATAR_URL . basename($u['foto']);
            }
        }
    } catch (Throwable $e) {
        // O cabeçalho nunca deve rebentar a página.
    }
    return $dados;
}