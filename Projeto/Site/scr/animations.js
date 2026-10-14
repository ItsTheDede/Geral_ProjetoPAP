/* =============================================================
   LisAM — script único (common + home + stock + login)
   Deteta a página via <body data-page="...">
   ============================================================= */

/* ---------- Common: helpers ---------- */
const $   = s => document.querySelector(s);
const $$  = s => document.querySelectorAll(s);
const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const eur = v => new Intl.NumberFormat('pt-PT').format(v) + ' €';

/* =============================================================
   PRODUCTS
   ============================================================= */
function initProducts(){
  const dataEl = document.getElementById('data-lisam');
  if (!dataEl) return;
  const DADOS = JSON.parse(dataEl.textContent);

  const categorias = DADOS.categorias;   // [[nome, icone], ...]
  const pecas      = DADOS.pecas;
  const iconeCat   = Object.fromEntries(categorias);

  let catAtiva = '', estAtivo = '';
  const reservadas = new Set();

  /* Categorias */
  function renderCats(){
    $('#cats').innerHTML = categorias.map(([c,i]) =>
      `<a data-cat="${esc(c)}" class="${catAtiva===c?'on':''}"><span class="ic">${i}</span>${esc(c)}</a>`).join('');
  }
  $('#cats').addEventListener('click', e => {
    const a = e.target.closest('a'); if (!a) return;
    catAtiva = catAtiva === a.dataset.cat ? '' : a.dataset.cat;
    renderPecas();
  });

  /* Chips estado */
  function renderChips(){
    const estados = ['', 'Novo', 'Usado', 'Recondicionado'];
    $('#chipsEstado').innerHTML = estados.map(s =>
      `<button type="button" data-e="${s}" class="${estAtivo===s?'on':''}">${s || 'Todos'}</button>`).join('');
  }
  $('#chipsEstado').addEventListener('click', e => {
    const b = e.target.closest('button'); if (!b) return;
    estAtivo = b.dataset.e; renderPecas();
  });

  /* Limpar */
  function limparFiltros(){
    $('#q').value = ''; catAtiva = ''; estAtivo = ''; $('#ordem').value = '';
    renderPecas();
  }

  /* Grid */
  function renderPecas(){
    const raw = $('#q').value.trim(), q = raw.toLowerCase(), ord = $('#ordem').value;
    let lista = pecas.filter(p =>
      (!q || (p.titulo+' '+p.ref+' '+p.loja+' '+p.cat+' '+p.estado).toLowerCase().includes(q)) &&
      (!catAtiva || p.cat === catAtiva) &&
      (!estAtivo || p.estado === estAtivo));
    if (ord) lista = [...lista].sort((a,b) => ord === 'asc' ? a.preco-b.preco : b.preco-a.preco);

    const filtrado = q || catAtiva || estAtivo;
    $('#pecasSub').textContent = filtrado
      ? `${lista.length} ${lista.length === 1 ? 'resultado' : 'resultados'}` +
        (raw ? ` para "${raw}"` : '') +
        (catAtiva ? ` em ${catAtiva}` : '') +
        (estAtivo ? ` (${estAtivo})` : '') + '.'
      : 'Stock atualizado pelas próprias lojas.';

    renderCats(); renderChips();

    const box = $('#pecasGrid');
    if (!lista.length){
      box.innerHTML = '<div class="vazio-box"><b>🔍</b>Sem resultados. Tente outra pesquisa ou limpe os filtros.<br><button class="btn btn-ghost" type="button" id="limpar">Limpar filtros</button></div>';
      $('#limpar').addEventListener('click', limparFiltros);
      return;
    }
    box.innerHTML = lista.map((p, n) => {
      const r = reservadas.has(p.ref);
      return `
      <article class="peca c-${p.cor}" style="--i:${n}">
        <div class="foto"><strong>${esc(p.estado)}</strong><small>Hoje</small><span class="em">${iconeCat[p.cat] || '🔧'}</span></div>
        <div class="pb">
          <h3>${esc(p.titulo)}</h3>
          <p class="ref">Ref. ${esc(p.ref)} · ${esc(p.cat)}</p>
          <p class="loja">${esc(p.loja)}</p>
          <div class="pf">
            <span class="preco">${eur(p.preco)}</span>
            <button class="res${r?' ok':''}" type="button" data-ref="${esc(p.ref)}"${r?' disabled':''}>${r?'Reservado ✓':'Reservar'}</button>
          </div>
        </div>
      </article>`;
    }).join('');
  }

  $('#q').addEventListener('input', renderPecas);
  $('#ordem').addEventListener('change', renderPecas);
  $('#pecasGrid').addEventListener('click', e => {
    const b = e.target.closest('.res'); if (!b || b.disabled) return;
    reservadas.add(b.dataset.ref);
    b.classList.add('ok'); b.textContent = 'Reservado ✓'; b.disabled = true;
    toast('Peça reservada! Receberá confirmação por email.');
  });

  renderPecas();
  reveal();
}

function toast(msg, ms = 2400){
  const t = $('#toast'); if (!t) return;
  t.textContent = msg; t.classList.add('on');
  clearTimeout(t._t); t._t = setTimeout(() => t.classList.remove('on'), ms);
}

/* ---------- Common: menu móvel ---------- */
const burger = $('.burger');
if (burger) burger.addEventListener('click', () => $('.top nav').classList.toggle('open'));

/* ---------- Common: tema ---------- */
const temaBtn = $('#tema');
function pintarTema(){
  if (temaBtn) temaBtn.textContent = document.documentElement.dataset.theme === 'dark' ? '☀️' : '🌙';
}
pintarTema();
if (temaBtn) temaBtn.addEventListener('click', () => {
  const novo = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
  document.documentElement.dataset.theme = novo;
  try { localStorage.setItem('lisam-theme', novo); } catch(e) {}
  pintarTema();
});

/* ---------- Common: contadores + reveal ---------- */
function countUp(el){
  const n = parseFloat(el.dataset.count), suf = el.dataset.suf || '', t0 = performance.now(), d = 1300;
  (function f(t){
    const p = Math.min(1, (t - t0) / d);
    el.textContent = Math.round(n * (1 - Math.pow(1 - p, 3))) + suf;
    if (p < 1) requestAnimationFrame(f);
  })(t0);
}
const io = 'IntersectionObserver' in window ? new IntersectionObserver(es => es.forEach(e => {
  if (!e.isIntersecting) return;
  e.target.classList.add('in');
  e.target.querySelectorAll('[data-count]').forEach(countUp);
  io.unobserve(e.target);
}), {threshold:.12}) : null;
function reveal(root = document){
  root.querySelectorAll('.rv:not(.in)').forEach(el => {
    if (io) io.observe(el);
    else { el.classList.add('in'); el.querySelectorAll('[data-count]').forEach(countUp); }
  });
}

/* =============================================================
   HOME
   ============================================================= */
function initHome(){
  const dataEl = document.getElementById('data-lisam');
  if (!dataEl) return;
  const DADOS = JSON.parse(dataEl.textContent);

  const categorias = DADOS.categorias;   // [[nome, icone], ...]
  const parceiros  = DADOS.parceiros;
  const lojas      = DADOS.lojas;
  const pecas      = DADOS.pecas;
  const iconeCat   = Object.fromEntries(categorias);

  let catAtiva = '', estAtivo = '';
  const reservadas = new Set();

  /* Stats */
  $('#stats').innerHTML = `
    <div><b data-count="${parceiros.length}">0</b><span>Empresas parceiras</span></div>
    <div><b data-count="${pecas.length}">0</b><span>Peças em catálogo</span></div>
    <div><b data-count="30" data-suf=" km">0</b><span>Raio de entrega</span></div>
    <div><b>Hoje</b><span>Entrega no próprio dia</span></div>`;

  /* Atalhos + faixa */
  $('#quick').innerHTML = '<span>Popular:</span>' +
    ['Travagem','Motor','Pneus','Elétrica'].map(c => `<a data-q="${c}">${c}</a>`).join('');
  $('#quick').addEventListener('click', e => {
    const a = e.target.closest('a'); if (!a) return;
    $('#q').value = a.dataset.q; catAtiva = '';
    renderPecas(); document.querySelector('#pecas').scrollIntoView({behavior:'smooth'});
  });
  const faixa = categorias.map(([c,i]) => `<span>${i} ${esc(c)}</span>`).join('');
  $('#track').innerHTML = faixa + faixa + faixa + faixa;

  /* Parceiros */
  $('#parceirosList').innerHTML = parceiros.map(p => {
    const n = pecas.filter(x => x.loja === p.nome).length;
    return `
    <div class="parceiro c-${p.cor} rv" data-loja="${esc(p.nome)}">
      <div class="av">${esc(p.sigla)}</div>
      <b>${esc(p.nome)}</b>
      <div class="d">${esc(p.desc)}</div>
      <div class="meta"><span class="tag">📍 ${esc(p.local)}</span><span class="tag ver">✓ Verificado</span></div>
      <div class="ft"><span><b>${n}</b> peças · <b>${esc(p.nota)}</b> ★</span><span class="go">Ver peças →</span></div>
    </div>`;
  }).join('');
  $('#parceirosList').addEventListener('click', e => {
    const card = e.target.closest('.parceiro'); if (!card) return;
    $('#q').value = card.dataset.loja; catAtiva = ''; estAtivo = '';
    renderPecas(); document.querySelector('#pecas').scrollIntoView({behavior:'smooth'});
    toast('A mostrar peças de ' + card.dataset.loja);
  });

  /* Mapa (Leaflet tem de estar carregado antes deste script) */
  if (window.L && document.getElementById('mapa')) {
    $('#leg').innerHTML = parceiros.map(p => `<span class="c-${p.cor}">${esc(p.nome)}</span>`).join('');
    const map = L.map('mapa',{scrollWheelZoom:false}).setView([39.75,-8.83],11);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap',maxZoom:19}).addTo(map);
    lojas.forEach(l => {
      const icone = L.divIcon({className:'', html:`<div class="pin" style="--c:${l.cor}"><span>${esc(l.sigla)}</span></div>`, iconSize:[36,36], iconAnchor:[18,36], popupAnchor:[0,-36]});
      L.marker(l.coords,{icon:icone}).addTo(map).bindPopup(`<b style="color:${l.cor}">${esc(l.nome)}</b><br>${esc(l.tipo)}<br>${esc(l.info)}<br><a href="${l.gmaps}" target="_blank" rel="noopener">Abrir no Google Maps</a>`);
    });
    map.fitBounds(L.featureGroup(lojas.map(l => L.marker(l.coords))).getBounds().pad(0.25));
  }

  /* Categorias + chips + peças */
  function renderCats(){
    $('#cats').innerHTML = categorias.map(([c,i]) =>
      `<a data-cat="${esc(c)}" class="${catAtiva===c?'on':''}"><span class="ic">${i}</span>${esc(c)}</a>`).join('');
  }
  $('#cats').addEventListener('click', e => {
    const a = e.target.closest('a'); if (!a) return;
    catAtiva = catAtiva === a.dataset.cat ? '' : a.dataset.cat;
    renderPecas();
  });
  function renderChips(){
    const estados = ['', 'Novo', 'Usado', 'Recondicionado'];
    $('#chipsEstado').innerHTML = estados.map(s =>
      `<button type="button" data-e="${s}" class="${estAtivo===s?'on':''}">${s || 'Todos'}</button>`).join('');
  }
  $('#chipsEstado').addEventListener('click', e => {
    const b = e.target.closest('button'); if (!b) return;
    estAtivo = b.dataset.e; renderPecas();
  });
  function limparFiltros(){
    $('#q').value = ''; catAtiva = ''; estAtivo = ''; $('#ordem').value = '';
    renderPecas();
  }

  function renderPecas(){
    const raw = $('#q').value.trim(), q = raw.toLowerCase(), ord = $('#ordem').value;
    let lista = pecas.filter(p =>
      (!q || (p.titulo+' '+p.ref+' '+p.loja+' '+p.cat+' '+p.estado).toLowerCase().includes(q)) &&
      (!catAtiva || p.cat === catAtiva) &&
      (!estAtivo || p.estado === estAtivo));
    if (ord) lista = [...lista].sort((a,b) => ord === 'asc' ? a.preco-b.preco : b.preco-a.preco);

    const filtrado = q || catAtiva || estAtivo;
    $('#pecasSub').textContent = filtrado
      ? `${lista.length} ${lista.length === 1 ? 'resultado' : 'resultados'}` +
        (raw ? ` para "${raw}"` : '') +
        (catAtiva ? ` em ${catAtiva}` : '') +
        (estAtivo ? ` (${estAtivo})` : '') + '.'
      : 'Stock atualizado pelas próprias lojas.';
    renderCats(); renderChips();

    const box = $('#pecasGrid');
    if (!lista.length){
      box.innerHTML = '<div class="vazio-box"><b>🔍</b>Sem resultados. Tente outra pesquisa ou limpe os filtros.<br><button class="btn btn-ghost" type="button" id="limpar">Limpar filtros</button></div>';
      $('#limpar').addEventListener('click', limparFiltros);
      return;
    }
    box.innerHTML = lista.map((p, n) => {
      const r = reservadas.has(p.ref);
      return `
      <article class="peca c-${p.cor}" style="--i:${n}">
        <div class="foto"><strong>${esc(p.estado)}</strong><small>Hoje</small><span class="em">${iconeCat[p.cat] || '🔧'}</span></div>
        <div class="pb">
          <h3>${esc(p.titulo)}</h3>
          <p class="ref">Ref. ${esc(p.ref)} · ${esc(p.cat)}</p>
          <p class="loja">${esc(p.loja)}</p>
          <div class="pf">
            <span class="preco">${eur(p.preco)}</span>
            <button class="res${r?' ok':''}" type="button" data-ref="${esc(p.ref)}"${r?' disabled':''}>${r?'Reservado ✓':'Reservar'}</button>
          </div>
        </div>
      </article>`;
    }).join('');
  }
  $('#formProcura').addEventListener('submit', () => {
    catAtiva = ''; renderPecas(); document.querySelector('#pecas').scrollIntoView({behavior:'smooth'});
  });
  $('#q').addEventListener('input', renderPecas);
  $('#ordem').addEventListener('change', renderPecas);
  $('#pecasGrid').addEventListener('click', e => {
    const b = e.target.closest('.res'); if (!b || b.disabled) return;
    reservadas.add(b.dataset.ref);
    b.classList.add('ok'); b.textContent = 'Reservado ✓'; b.disabled = true;
    toast('Peça reservada! Receberá confirmação por email.');
  });

  renderPecas();
  reveal();
}

/* =============================================================
   STOCK
   ============================================================= */
function initStock(){
  const dataEl = document.getElementById('data-stock');
  if (!dataEl) return;
  const {parceiros, stock} = JSON.parse(dataEl.textContent);

  const estadoStock = q => q === 0 ? ['esgotado','Esgotado'] : (q <= 2 ? ['baixo','Stock baixo'] : ['ok','Em stock']);

  let filtroEmp = '', filtroQ = '', filtroEst = '';

  function renderResumoTotais(){
    let totRef=0, totUn=0, totVal=0;
    Object.values(stock).forEach(itens => itens.forEach(i => { totRef++; totUn += i.qtd; totVal += i.qtd*i.preco; }));
    $('#resumo').innerHTML = `
      <div><span class="ic">📦</span><div><b>${totRef}</b><span>Referências</span></div></div>
      <div><span class="ic">🔢</span><div><b>${totUn}</b><span>Unidades</span></div></div>
      <div><span class="ic">💶</span><div><b>${eur(totVal)}</b><span>Valor em stock</span></div></div>
      <div><span class="ic">🏪</span><div><b>${parceiros.length}</b><span>Empresas</span></div></div>`;
  }
  function renderTabsStock(){
    $('#tabs').innerHTML = `
      <a class="${filtroEmp===''?'on':''}" data-emp="">Todas</a>
      ${parceiros.map(p => `
        <a class="c-${p.cor} ${filtroEmp===p.cor?'on':''}" data-emp="${p.cor}">
          ${esc(p.nome)} <small>${(stock[p.cor]||[]).length}</small>
        </a>`).join('')}`;
  }
  function renderConteudoStock(){
    const q = filtroQ.trim().toLowerCase(), est = filtroEst;
    $('#limpar').style.display = (q !== '' || est !== '') ? 'inline-flex' : 'none';

    let html = '';
    parceiros.forEach((p, n) => {
      if (filtroEmp && filtroEmp !== p.cor) return;
      const todos = stock[p.cor] || [];
      const itens = todos.filter(i => {
        if (q && !(i.titulo + ' ' + i.ref + ' ' + i.cat).toLowerCase().includes(q)) return false;
        if (est && estadoStock(i.qtd)[0] !== est) return false;
        return true;
      });
      const un  = todos.reduce((s,i)=>s+i.qtd,0);
      const val = todos.reduce((s,i)=>s+i.qtd*i.preco,0);

      html += `
        <section class="empresa c-${p.cor}" id="${p.cor}" style="margin-bottom:24px;animation-delay:${n*80}ms">
          <div class="empresa-top">
            <div class="empresa-id">
              <div class="av">${esc(p.sigla)}</div>
              <div>
                <h2>${esc(p.nome)}</h2>
                <p class="sub">${esc(p.desc)} · ${esc(p.local)}</p>
              </div>
            </div>
            <div class="empresa-n"><span><b>${todos.length}</b> refs</span><span><b>${un}</b> un.</span><span><b>${eur(val)}</b></span></div>
          </div>`;
      if (!itens.length){
        html += `<p class="vazio">Sem resultados nesta empresa para os filtros escolhidos.</p>`;
      } else {
        html += `<div class="tabela-wrap"><table class="tabela">
          <thead><tr>
            <th>Referência</th><th>Peça</th><th>Categoria</th><th>Estado</th>
            <th class="r">Qtd.</th><th>Stock</th><th class="r">Preço</th>
          </tr></thead>
          <tbody>${itens.map(i => {
            const [cl, txt] = estadoStock(i.qtd);
            return `<tr>
              <td class="ref"><b>${esc(i.ref)}</b></td>
              <td>${esc(i.titulo)}</td>
              <td>${esc(i.cat)}</td>
              <td>${esc(i.estado)}</td>
              <td class="r"><div class="q"><b>${i.qtd}</b><div class="qbar"><i class="${cl}" style="width:${Math.max(i.qtd?8:0, Math.min(100, i.qtd*10))}%"></i></div></div></td>
              <td><span class="chip ${cl}">${txt}</span></td>
              <td class="r preco-s">${eur(i.preco)}</td>
            </tr>`;
          }).join('')}</tbody></table></div>`;
      }
      html += `</section>`;
    });
    $('#conteudoStock').innerHTML = html || '<p class="vazio">Sem resultados.</p>';
  }

  $('#qStock').addEventListener('input', e => { filtroQ = e.target.value; renderConteudoStock(); });
  $('#estado').addEventListener('change', e => { filtroEst = e.target.value; renderConteudoStock(); });
  $('#filtros').addEventListener('submit', e => { e.preventDefault(); renderConteudoStock(); });
  $('#limpar').addEventListener('click', () => {
    filtroQ = ''; filtroEst = '';
    $('#qStock').value = ''; $('#estado').value = '';
    renderConteudoStock();
  });
  $('#tabs').addEventListener('click', e => {
    const a = e.target.closest('a'); if (!a) return;
    filtroEmp = a.dataset.emp; renderTabsStock(); renderConteudoStock();
  });

  $('#exportar').addEventListener('click', () => {
    const cel = v => '"' + String(v).replace(/"/g, '""') + '"';
    const linhas = [['Empresa','Referência','Peça','Categoria','Estado','Quantidade','Preço (€)'].map(cel).join(';')];
    parceiros.forEach(p => (stock[p.cor] || []).forEach(i =>
      linhas.push([p.nome, i.ref, i.titulo, i.cat, i.estado, i.qtd, i.preco].map(cel).join(';'))));
    try {
      const blob = new Blob(['\ufeff' + linhas.join('\n')], {type:'text/csv;charset=utf-8'});
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob); a.download = 'lisam-stock.csv';
      document.body.appendChild(a); a.click(); a.remove();
      toast('Stock exportado.');
    } catch (e) { toast('Não foi possível exportar.'); }
  });

  renderResumoTotais();
  renderTabsStock();
  renderConteudoStock();
}

/* =============================================================
   LOGIN
   ============================================================= */
function initLogin(){
  const ver = $('#ver');
  if (ver) ver.addEventListener('click', () => {
    const p = $('#pass');
    const mostrar = p.type === 'password';
    p.type = mostrar ? 'text' : 'password';
    ver.textContent = mostrar ? 'Ocultar' : 'Mostrar';
  });
  const esq = $('#esqueci');
  if (esq) esq.addEventListener('click', e => { e.preventDefault(); toast('Pedido de recuperação enviado (demo).'); });
  const ped = $('#pedir');
  if (ped) ped.addEventListener('click', e => { e.preventDefault(); toast('Pedido de acesso enviado (demo).'); });

  const form = $('#loginForm');
  if (form) form.addEventListener('submit', () => {
    const btn = $('#btnEntrar');
    if (form.checkValidity()) { btn.disabled = true; btn.textContent = 'A entrar…'; }
  });
}

/* =============================================================
   Dispatcher
   ============================================================= */
document.addEventListener('DOMContentLoaded', () => {
  const page = document.body.dataset.page;
  if (page === 'home')  initHome();
  if (page === 'stock') initStock();
  if (page === 'login') initLogin();
  if (page === 'products') initProducts();
});