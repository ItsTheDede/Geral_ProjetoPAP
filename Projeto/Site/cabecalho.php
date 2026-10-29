<?php
/**
 * includes/cabecalho.php
 * Cabeçalho partilhado (logo, menu, tema, menu do utilizador com foto).
 *
 * Variáveis que a página pode definir ANTES de incluir este ficheiro:
 *   $paginaAtiva  -> 'inicio' | 'produtos' | 'parcerias' | 'sobre' | '' (marca o link ativo)
 *   $cabecalhoJs  -> true se a página NÃO carrega o animations.js (liga tema, menu e burger aqui)
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/perfil.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$menu = def_dados_menu();

$paginaAtiva = $paginaAtiva ?? '';
$cabecalhoJs = $cabecalhoJs ?? false;
if (empty($logoMarca)) {
    $logoMarca = 'assets/images/logo.svg';
}

$logado = utilizadorAutenticado();
$menu   = $logado ? def_dados_menu() : ['nome' => '', 'foto' => ''];
$ativo  = fn(string $p): string => $paginaAtiva === $p ? ' class="active"' : '';
?>
<header class="top">
    <div class="top-inner">
        <a href="index.php" class="brand">
            <span class="mark">
                <img src="<?= h($logoMarca) ?>" alt="Gamipeças" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <svg style="display:none" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M5.3 18.7l2.1-2.1M16.6 7.4l2.1-2.1"/></svg>
            </span>
            <span>L<b>AM</b></span>
        </a>
        <span class="plate hide-s"><span>PEÇAS AUTO</span></span>
        <button class="burger" type="button" aria-label="Menu">☰</button>
        <nav>
            <a href="index.php"<?= $ativo('inicio') ?>>Início</a>
            <a href="products.php"<?= $ativo('produtos') ?>>Produtos</a>
            <a href="parceiros.php"<?= $ativo('parcerias') ?>>Parcerias</a>
            <a href="about.php"<?= $ativo('sobre') ?>>Sobre o projeto</a>
            <button class="theme" id="tema" type="button" aria-label="Alternar tema claro/escuro">🌙</button>

            <?php if ($logado): ?>
                <div class="user-menu">
                    <button class="user-btn" id="userBtn" type="button" aria-haspopup="true" aria-expanded="false">
                        <span class="avatar">
                            <?php if ($menu['foto']): ?>
                                <img src="<?= h($menu['foto']) ?>" alt="" style="width:28px;height:28px;border-radius:50%;object-fit:cover;display:block">
                            <?php else: ?>👤<?php endif; ?>
                        </span>
                        <span class="nome"><?= h($menu['nome']) ?></span>
                        <span class="seta">▼</span>
                    </button>
                    <div class="dropdown" id="userDropdown" hidden>
                        <a href="configura.php"><span>⚙️</span> Definições</a>
                        <a href="logout.php" class="sair"><span>🚪</span> Terminar sessão</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="cta">Entrar</a>
                <a href="registro.php" class="cta btn-ghost" style="margin-left: 6px;">Criar conta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<?php if ($cabecalhoJs): ?>
<script>
(function () {
    var root = document.documentElement;

    var tema = document.getElementById('tema');
    function icone() { if (tema) tema.textContent = root.dataset.theme === 'dark' ? '☀️' : '🌙'; }
    icone();
    if (tema) tema.addEventListener('click', function () {
        var novo = root.dataset.theme === 'dark' ? 'light' : 'dark';
        root.dataset.theme = novo;
        try { localStorage.setItem('lisam-theme', novo); } catch (e) {}
        icone();
    });

    var btn = document.getElementById('userBtn');
    var menu = document.getElementById('userDropdown');
    function fechar() { if (menu) { menu.hidden = true; btn.setAttribute('aria-expanded', 'false'); } }
    if (btn && menu) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var abrir = menu.hidden;
            menu.hidden = !abrir;
            btn.setAttribute('aria-expanded', abrir ? 'true' : 'false');
        });
        document.addEventListener('click', fechar);
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') fechar(); });
    }

    var burger = document.querySelector('.burger');
    var nav = document.querySelector('header.top nav');
    if (burger && nav) burger.addEventListener('click', function () { nav.classList.toggle('open'); });
})();
</script>
<?php endif; ?>