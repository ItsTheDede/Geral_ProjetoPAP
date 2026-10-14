<?php
require __DIR__ . '/includes/config.php';

$pageTitle = 'LAM, produtos';
$activeNav = 'produtos';
$bodyPage  = 'products';
$extraHead = '';
$extraJsHtml = '';

require __DIR__ . '/includes/header.php';
?>

<main class="wrap">

  <section id="pecas" style="margin-top:34px">
    <div class="sec-head rv">
      <span class="eyebrow">Produtos</span>
      <h2>Todas as peças disponíveis</h2>
      <p id="pecasSub">Stock atualizado pelas próprias lojas.</p>
    </div>

    <div class="cats" id="cats"></div>

    <div class="toolbar">
      <div class="chips" id="chipsEstado"></div>
      <input type="text" id="q" placeholder="Referência OEM, peça, marca ou loja"
             aria-label="Pesquisar produtos"
             style="background:var(--card);color:var(--ink);border:1px solid var(--line);
                    border-radius:12px;padding:10px 14px;font-size:14px;font-family:inherit;
                    min-width:220px;flex:0 1 260px">
      <select id="ordem" aria-label="Ordenar">
        <option value="">Ordenar: relevância</option>
        <option value="asc">Preço: mais baixo</option>
        <option value="desc">Preço: mais alto</option>
      </select>
    </div>

    <div class="pecas" id="pecasGrid"></div>
  </section>

</main>

<script id="data-lisam" type="application/json">
<?= json_encode([
  'categorias' => array_map(fn($k,$v) => [$k,$v], array_keys($categorias), array_values($categorias)),
  'parceiros'  => $parceiros,
  'lojas'      => $lojas,
  'pecas'      => $pecas,
], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>