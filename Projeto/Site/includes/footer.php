<?php $extraJsHtml = $extraJsHtml ?? ''; ?>
<footer class="foot">
  <div class="foot-grid">
    <div>
      <a href="index.php" class="brand">
        <span class="mark">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M5.3 18.7l2.1-2.1M16.6 7.4l2.1-2.1"/></svg>
        </span>
        <span>Lis<b>AM</b></span>
      </a>
      <p>Marketplace automóvel B2B que liga oficinas às empresas de peças do distrito de Leiria.</p>
    </div>
    <div><h4>Plataforma</h4><a href="index.php#pecas">Peças</a><a href="stock.php">Stock</a><a href="index.php#lojas">Lojas</a></div>
    <div><h4>Conta</h4><a href="login.php">Entrar</a><a href="login.php">Pedir acesso</a><a href="index.php#projeto">Sobre o projeto</a></div>
  </div>
  <div class="copy">© LAM · Marketplace automóvel B2B · Leiria</div>
</footer>

<div class="toast" id="toast"></div>

<?= $extraJsHtml ?>
<script src="scr/animations.js"></script>
</body>
</html>