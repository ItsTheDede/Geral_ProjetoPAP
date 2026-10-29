/* =========================================================
   animation.js
   Código isolado por página para evitar conflitos de variáveis
   ========================================================= */

/* =========================================================
   BLOCO 1 · PÁGINA INICIAL (index.php)
   ========================================================= */
(function () {
  // Se não existir o elemento #pecasGrid, não estamos na index → sai
  if (!document.querySelector('#pecasGrid')) return;

  const $ = s => document.querySelector(s);
  const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const eur = v => new Intl.NumberFormat('pt-PT').format(v) + ' €';

  function toast(msg, ms = 2400){
    const t = $('#toast'); if (!t) return;
    t.textContent = msg; t.classList.add('on');
    clearTimeout(t._t); t._t = setTimeout(() => t.classList.remove('on'), ms);
  }

  /* Menu móvel */
  const burger = $('.burger');
  if (burger) burger.addEventListener('click', () => $('.top nav').classList.toggle('open'));

  /* Tema claro/escuro */
  const temaBtn = $('#tema');
  function pintarTema(){
    if (temaBtn) temaBtn.textContent = document.documentElement.dataset.theme === 'dark' ? '☀️' : '🌙';
  }
  pintarTema();
  if (temaBtn) temaBtn.addEventListener('click', () => {
    const novo = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = novo;
    try { localStorage.setItem('lisam-theme', novo); } catch (e) {}
    pintarTema();
  });

  /* ===== Dados ===== */
  const categorias = [
    ['Motor','⚙️'],['Travagem','🛑'],['Suspensão','🔩'],['Elétrica','⚡'],
    ['Arrefecimento','❄️'],['Iluminação','💡'],['Pneus','🛞'],['Carroçaria','🚗']
  ];

  // Pasta dos logótipos dos parceiros (relativa à index.php)
  const PASTA_LOGOS = 'assets/images/parcerias/';

  // "logo" = nome do ficheiro dentro de PASTA_LOGOS.
  // Se o ficheiro não existir, o cartão mostra a sigla automaticamente.
  const parceiros = [
    {cor:'c1', nome:'Samiparts', sigla:'SP', logo:'samiparts.svg',                 desc:'Peças originais e equivalentes', local:'Flandes',        nota:'4.8'},
    {cor:'c2', nome:'Guipeças',  sigla:'GP', logo:'guipecas.svg',                  desc:'Peças multimarca',              local:'Marinha Grande', nota:'4.6'},
    {cor:'c3', nome:'Gamipeças', sigla:'GA', logo:'GAMIPECAS_Logotipo_2019.svg',   desc:'Peças e acessórios',            local:'Pombal',         nota:'4.9'}
  ];
  const lojas = [
    {nome:'Samiparts', sigla:'SP', cor:'#0057ff', tipo:'Loja', info:'R. da Primavera B1, 3100-339 Flandes', coords:[39.90397077796481, -8.633328093923474], gmaps:'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3060.679557972519!2d-8.636278523758442!3d39.903806171526675!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd2242ad02d70a0b%3A0xcbb75624fabda40!2sSamiparts%20-%20Loja%20Pombal!5e0!3m2!1spt-PT!2spt!4v1791985627806!5m2!1spt-PT!2spt'},
    {nome:'Guipeças',  sigla:'GP', cor:'#ff2e63', tipo:'Loja', info:'Av. Nossa Sra. da Guia 124, 3105-089 Pombal', coords:[39.944691581714224, -8.788271679867668], gmaps:'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d382.3568059311385!2d-8.788602932815367!3d39.94464531343782!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd226a57df9964df%3A0x926564241f6de896!2sGuipe%C3%A7as%20Do%20Oeste%20Lda!5e0!3m2!1spt-PT!2spt!4v1791985551360!5m2!1spt-PT!2spt'},
    {nome:'Gamipeças', sigla:'GP', cor:'#00a651', tipo:'Loja', info:'R. António Champalimaud, 3100-578 Pombal', coords:[39.92511456042214, -8.631716401323908], gmaps:'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3059.7326230887106!2d-8.634366423757498!3d39.92499937152296!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd226669ed8d3ca5%3A0x9cdaabe1bd17885!2sGAMIPE%C3%87AS%20%2C%20LDA%20Pe%C3%A7as%20Auto!5e0!3m2!1spt-PT!2spt!4v1791985714778!5m2!1spt-PT!2spt'}
  ];
  const iconeCat = Object.fromEntries(categorias);
  const pecas = [
    {titulo:'Pastilhas de travão dianteiras', ref:'BRK-1024', cat:'Travagem',      loja:'Samiparts', cor:'c1', preco:120, estado:'Novo'},
    {titulo:'Filtro de óleo',                 ref:'FLT-2210', cat:'Motor',         loja:'Samiparts', cor:'c1', preco:15,  estado:'Novo'},
    {titulo:'Amortecedor traseiro',           ref:'AMT-7781', cat:'Suspensão',     loja:'Samiparts', cor:'c1', preco:95,  estado:'Usado'},
    {titulo:'Alternador 90A',                 ref:'ALT-5540', cat:'Elétrica',      loja:'Guipeças',  cor:'c2', preco:210, estado:'Recondicionado'},
    {titulo:'Radiador de água',               ref:'RAD-3302', cat:'Arrefecimento', loja:'Guipeças',  cor:'c2', preco:140, estado:'Novo'},
    {titulo:'Farol dianteiro esquerdo',       ref:'FAR-9910', cat:'Iluminação',    loja:'Gamipeças', cor:'c3', preco:320, estado:'Novo'},
    {titulo:'Pneu 205/55 R16',                ref:'PNE-4408', cat:'Pneus',         loja:'Gamipeças', cor:'c3', preco:85,  estado:'Novo'}
  ];

  let catAtiva = '', estAtivo = '';
  const reservadas = new Set();

  /* Números do hero */
  const statsEl = $('#stats');
  if (statsEl) statsEl.innerHTML = `
    <div><b data-count="${parceiros.length}">0</b><span>Empresas parceiras</span></div>
    <div><b data-count="${pecas.length}">0</b><span>Peças em catálogo</span></div>
    <div><b data-count="30" data-suf=" km">0</b><span>Raio de entrega</span></div>
    <div><b>Hoje</b><span>Entrega no próprio dia</span></div>`;

  /* Atalhos */
  const quickEl = $('#quick');
  if (quickEl) {
    quickEl.innerHTML = '<span>Popular:</span>' + ['Travagem','Motor','Pneus','Elétrica'].map(c => `<a data-q="${c}">${c}</a>`).join('');
    quickEl.addEventListener('click', e => {
      const a = e.target.closest('a'); if (!a) return;
      $('#q').value = a.dataset.q; catAtiva = ''; renderPecas();
      document.querySelector('#pecas').scrollIntoView({behavior:'smooth'});
    });
  }

  /* Faixa */
  const trackEl = $('#track');
  if (trackEl) {
    const faixa = categorias.map(([c,i]) => `<span>${i} ${esc(c)}</span>`).join('');
    trackEl.innerHTML = faixa + faixa + faixa + faixa;
  }

  /* Parceiros */
  const parceirosList = $('#parceirosList');
  if (parceirosList) {
    parceirosList.innerHTML = parceiros.map(p => {
      const n = pecas.filter(x => x.loja === p.nome).length;

      // Logótipo do parceiro; se falhar a carregar, volta a mostrar a sigla
      const avatar = p.logo
        ? `<div class="av" style="background:#fff;padding:6px"><img src="${esc(PASTA_LOGOS + p.logo)}" alt="${esc(p.nome)}" loading="lazy" style="width:100%;height:100%;object-fit:contain;display:block" onerror="this.parentNode.removeAttribute('style');this.parentNode.textContent='${esc(p.sigla)}'"></div>`
        : `<div class="av">${esc(p.sigla)}</div>`;

      return `
      <div class="parceiro c-${p.cor} rv" data-loja="${esc(p.nome)}">
        ${avatar}
        <b>${esc(p.nome)}</b>
        <div class="d">${esc(p.desc)}</div>
        <div class="meta"><span class="tag">📍 ${esc(p.local)}</span><span class="tag ver">✓ Verificado</span></div>
        <div class="ft"><span><b>${n}</b> peças · <b>${esc(p.nota)}</b> ★</span><span class="go">Ver peças →</span></div>
      </div>`;
    }).join('');
    parceirosList.addEventListener('click', e => {
      const card = e.target.closest('.parceiro'); if (!card) return;
      $('#q').value = card.dataset.loja; catAtiva = ''; estAtivo = ''; renderPecas();
      document.querySelector('#pecas').scrollIntoView({behavior:'smooth'});
      toast('A mostrar peças de ' + card.dataset.loja);
    });
  }

  /* Mapa */
  if (typeof L !== 'undefined' && $('#mapa')) {
    const legEl = $('#leg');
    if (legEl) legEl.innerHTML = parceiros.map(p => `<span class="c-${p.cor}" style="color:var(--cc)">${esc(p.nome)}</span>`).join('');
    const map = L.map('mapa',{scrollWheelZoom:false}).setView([39.75,-8.83],11);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',maxZoom:19}).addTo(map);
    lojas.forEach(l => {
      const icone = L.divIcon({className:'', html:`<div class="pin" style="--c:${l.cor}"><span>${esc(l.sigla)}</span></div>`, iconSize:[36,36], iconAnchor:[18,36], popupAnchor:[0,-36]});
      L.marker(l.coords,{icon:icone}).addTo(map)
        .bindPopup(`<b style="color:${l.cor}">${esc(l.nome)}</b><br>${esc(l.tipo)}<br>${esc(l.info)}<br><a href="${l.gmaps}" target="_blank" rel="noopener">Abrir no Google Maps</a>`);
    });
    map.fitBounds(L.featureGroup(lojas.map(l => L.marker(l.coords))).getBounds().pad(0.25));
  }

  /* Categorias e filtros */
  function renderCats(){
    const el = $('#cats'); if (!el) return;
    el.innerHTML = categorias.map(([c,i]) =>
      `<a data-cat="${esc(c)}" class="${catAtiva===c?'on':''}"><span class="ic">${i}</span>${esc(c)}</a>`).join('');
  }
  const catsEl = $('#cats');
  if (catsEl) catsEl.addEventListener('click', e => {
    const a = e.target.closest('a'); if (!a) return;
    catAtiva = catAtiva === a.dataset.cat ? '' : a.dataset.cat;
    renderPecas();
  });

  function renderChips(){
    const el = $('#chipsEstado'); if (!el) return;
    const estados = ['', 'Novo', 'Usado', 'Recondicionado'];
    el.innerHTML = estados.map(s =>
      `<button type="button" data-e="${s}" class="${estAtivo===s?'on':''}">${s || 'Todos'}</button>`).join('');
  }
  const chipsEl = $('#chipsEstado');
  if (chipsEl) chipsEl.addEventListener('click', e => {
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
      ? `${lista.length} ${lista.length === 1 ? 'resultado' : 'resultados'}` + (raw ? ` para "${raw}"` : '') + (catAtiva ? ` em ${catAtiva}` : '') + (estAtivo ? ` (${estAtivo})` : '') + '.'
      : 'Stock atualizado pelas próprias lojas.';
    renderCats(); renderChips();

    const box = $('#pecasGrid');
    if (!lista.length){
      box.innerHTML = '<div class="vazio-box"><b>🔍</b>Sem resultados. Tente outra pesquisa ou limpe os filtros.<br><button class="btn btn-ghost" type="button" id="limpar">Limpar filtros</button></div>';
      const l = $('#limpar'); if (l) l.addEventListener('click', limparFiltros);
      return;
    }
    box.innerHTML = lista.map((p, n) => {
      const r = reservadas.has(p.ref);
      return `
      <article class="peca c-${p.cor}" style="--i:${n}">
        <div class="foto"><strong>${esc(p.estado)}</strong><small>Hoje</small><span class="em">${iconeCat[p.cat] || '🔧'}</span></div>
        <div class="pb">
          <h3>${esc(p.titulo)}</h3>
          <p class="ref"><span class="plate"><span>${esc(p.ref)}</span></span>${esc(p.cat)}</p>
          <p class="loja">${esc(p.loja)}</p>
          <div class="pf">
            <span class="preco">${eur(p.preco)}</span>
            <button class="res${r?' ok':''}" type="button" data-ref="${esc(p.ref)}"${r?' disabled':''}>${r?'Reservado ✓':'Reservar'}</button>
          </div>
        </div>
      </article>`;
    }).join('');
  }

  const formProcura = $('#formProcura');
  if (formProcura) formProcura.addEventListener('submit', () => {
    catAtiva = ''; renderPecas();
    document.querySelector('#pecas').scrollIntoView({behavior:'smooth'});
  });
  const qEl = $('#q'); if (qEl) qEl.addEventListener('input', renderPecas);
  const ordemEl = $('#ordem'); if (ordemEl) ordemEl.addEventListener('change', renderPecas);
  const gridEl = $('#pecasGrid');
  if (gridEl) gridEl.addEventListener('click', e => {
    const b = e.target.closest('.res'); if (!b || b.disabled) return;
    reservadas.add(b.dataset.ref);
    b.classList.add('ok'); b.textContent = 'Reservado ✓'; b.disabled = true;
    toast('Peça reservada! Receberá confirmação por email.');
  });

  renderPecas();
})();


/* =========================================================
   BLOCO 2 · PÁGINA STOCK (products.php)
   ========================================================= */
(function () {
  // Se não existir #loginForm nem #form, não estamos na login/registo → sai
  const isLogin  = !!document.querySelector('#loginForm');
  const isRegisto = !!document.querySelector('#form');
  if (!isLogin && !isRegisto) return;

  const $ = s => document.querySelector(s);
  const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const eur = v => new Intl.NumberFormat('pt-PT').format(v) + ' €';

  function toast(msg, ms = 2400){
    const t = $('#toast'); if (!t) return;
    t.textContent = msg; t.classList.add('on');
    clearTimeout(t._t); t._t = setTimeout(() => t.classList.remove('on'), ms);
  }

  const burger = $('.burger');
  if (burger) burger.addEventListener('click', () => $('.top nav').classList.toggle('open'));

  const temaBtn = $('#tema');
  function pintarTema(){ if (temaBtn) temaBtn.textContent = document.documentElement.dataset.theme === 'dark' ? '☀️' : '🌙'; }
  pintarTema();
  if (temaBtn) temaBtn.addEventListener('click', () => {
    const novo = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = novo;
    try { localStorage.setItem('lisam-theme', novo); } catch (e) {}
    pintarTema();
  });

  /* ---------- LOGIN ---------- */
  if (isLogin) {
    const textos = {
      empresa: {
        eyebrow: 'Área de parceiros',
        titulo: 'Bem-vindo de volta à <span class="gtext">oficina</span>.',
        texto: 'Entre na sua conta para reservar peças auto, acompanhar encomendas e levantar na loja ou receber no próprio dia.',
        sub: 'Acesso para oficinas e empresas parceiras.',
        emailPh: 'oficina@exemplo.pt',
        dica: 'oficina@exemplo.pt / 123456'
      },
      cliente: {
        eyebrow: 'Área de cliente',
        titulo: 'Bem-vindo de volta à <span class="gtext">estrada</span>.',
        texto: 'Entre na sua conta para pesquisar peças auto, guardar reservas e levantar junto de quem tem stock, perto de si.',
        sub: 'Acesso para clientes particulares.',
        emailPh: 'cliente@exemplo.pt',
        dica: 'demo@lam.pt / demo'
      }
    };

    const tipoConta = () => document.querySelector('input[name="conta"]:checked').value;

    function aplicarTipo(){
      const t = tipoConta(), x = textos[t];
      const oc = $('#optCliente'); if (oc) oc.classList.toggle('on', t === 'cliente');
      const oe = $('#optEmpresa'); if (oe) oe.classList.toggle('on', t === 'empresa');
      const ve = $('#vantEmpresa'); if (ve) ve.hidden = t !== 'empresa';
      const vc = $('#vantCliente'); if (vc) vc.hidden = t !== 'cliente';
      const se = $('#sideEyebrow'); if (se) se.textContent = x.eyebrow;
      const st = $('#sideTitulo');  if (st) st.innerHTML = x.titulo;
      const sx = $('#sideTexto');   if (sx) sx.textContent = x.texto;
      const su = $('#subTxt');      if (su) su.textContent = x.sub;
      const em = $('#email');       if (em) em.placeholder = x.emailPh;
      const er = $('#erro');        if (er) er.style.display = 'none';
    }

    document.querySelectorAll('input[name="conta"]').forEach(r => r.addEventListener('change', aplicarTipo));
    if (new URLSearchParams(location.search).get('tipo') === 'cliente') {
      const c = document.querySelector('input[value="cliente"]');
      if (c) c.checked = true;
    }
    aplicarTipo();

    const verBtn = $('#ver');
    if (verBtn) verBtn.addEventListener('click', () => {
      const p = $('#pass');
      const mostrar = p.type === 'password';
      p.type = mostrar ? 'text' : 'password';
      verBtn.textContent = mostrar ? 'Ocultar' : 'Mostrar';
    });

    const esqueci = $('#esqueci');
    if (esqueci) esqueci.addEventListener('click', e => {
      e.preventDefault();
      toast('Pedido de recuperação enviado (demo).');
    });

    const utilizadoresDemo = {
      'oficina@exemplo.pt': { nome:'Oficina Demo', pass:'123456', tipo:'empresa' },
      'demo@lam.pt':        { nome:'Demo',         pass:'demo',   tipo:'cliente' }
    };
    let tentativas = 0;

    const loginForm = $('#loginForm');
    if (loginForm) loginForm.addEventListener('submit', e => {
      e.preventDefault();
      const email = $('#email').value.trim().toLowerCase();
      const pass  = $('#pass').value;
      const tipo  = tipoConta();
      const erro  = $('#erro');

      if (tentativas >= 5){
        erro.textContent = 'Demasiadas tentativas. Recarregue a página.';
        erro.style.display = 'block';
        return;
      }
      if (!email || !pass){
        erro.textContent = 'Introduza um email e uma palavra-passe válidos.';
        erro.style.display = 'block';
        return;
      }
      const u = utilizadoresDemo[email];
      if (u && u.pass === pass){
        if (u.tipo !== tipo){
          erro.textContent = u.tipo === 'empresa'
            ? 'Esta é uma conta de empresa. Selecione "Empresa" para entrar.'
            : 'Esta é uma conta de cliente. Selecione "Cliente" para entrar.';
          erro.style.display = 'block';
          erro.classList.remove('shake'); void erro.offsetWidth; erro.classList.add('shake');
          return;
        }
        erro.style.display = 'none';
        try { sessionStorage.setItem('lam-sessao', JSON.stringify({ nome:u.nome, tipo:u.tipo })); } catch (err) {}
        const btn = $('#btnEntrar');
        btn.disabled = true; btn.textContent = 'A entrar…';
        toast('Bem-vindo, ' + u.nome + '!');
        setTimeout(() => {
          btn.textContent = 'Entrado ✓';
          btn.style.background = 'var(--ok)'; btn.style.color = '#fff';
          setTimeout(() => location.href = 'index.php', 700);
        }, 600);
        return;
      }
      tentativas++;
      erro.textContent = 'Email ou palavra-passe incorretos.';
      erro.style.display = 'block';
      erro.classList.remove('shake'); void erro.offsetWidth; erro.classList.add('shake');
      toast('Credenciais inválidas (demo: ' + textos[tipo].dica + ')');
    });
  }

  /* ---------- REGISTO ---------- */
  if (isRegisto) {
    const textos = {
      empresa: {
        titulo: 'Junte a sua oficina à <span class="gtext">rede LAM</span>.',
        texto: 'Crie a conta da sua empresa e comece a pesquisar, reservar e receber peças auto das empresas parceiras do distrito de Leiria.',
        sub: 'Pedido de acesso para empresas e oficinas.',
        nome: 'Responsável', emailPh: 'geral@oficina.pt', btn: 'Pedir acesso'
      },
      cliente: {
        titulo: 'Encontre a peça certa, <span class="gtext">perto de si</span>.',
        texto: 'Crie a sua conta de cliente para pesquisar e reservar peças auto junto das empresas parceiras do distrito de Leiria.',
        sub: 'Conta de cliente particular.',
        nome: 'Nome', emailPh: 'nome@email.pt', btn: 'Criar conta'
      }
    };
    const tipoConta = () => document.querySelector('input[name="conta"]:checked').value;

    function aplicarTipo(){
      const t = tipoConta(), x = textos[t];
      const oc = $('#optCliente'); if (oc) oc.classList.toggle('on', t === 'cliente');
      const oe = $('#optEmpresa'); if (oe) oe.classList.toggle('on', t === 'empresa');
      document.querySelectorAll('[data-so="empresa"]').forEach(c => {
        c.hidden = t !== 'empresa';
        c.classList.remove('bad'); c.querySelector('.msg').textContent = '';
      });
      const cn = $('#campoNif'); if (cn) cn.classList.toggle('full', t === 'cliente');
      const ve = $('#vantEmpresa'); if (ve) ve.hidden = t !== 'empresa';
      const vc = $('#vantCliente'); if (vc) vc.hidden = t !== 'cliente';
      const st = $('#sideTitulo'); if (st) st.innerHTML = x.titulo;
      const sx = $('#sideTexto');  if (sx) sx.textContent = x.texto;
      const su = $('#subTxt');     if (su) su.textContent = x.sub;
      const nl = $('#nomeLbl');    if (nl) nl.textContent = x.nome;
      const em = $('#email');      if (em) em.placeholder = x.emailPh;
      const bt = $('#btn');        if (bt) bt.textContent = x.btn;
      const er = $('#erro');       if (er) er.hidden = true;
    }
    document.querySelectorAll('input[name="conta"]').forEach(r => r.addEventListener('change', aplicarTipo));
    if (new URLSearchParams(location.search).get('tipo') === 'cliente') {
      const c = document.querySelector('input[value="cliente"]');
      if (c) c.checked = true;
    }
    aplicarTipo();

    document.querySelectorAll('.ver').forEach(b => b.addEventListener('click', () => {
      const i = $('#' + b.dataset.alvo), mostrar = i.type === 'password';
      i.type = mostrar ? 'text' : 'password'; b.textContent = mostrar ? 'Ocultar' : 'Mostrar';
    }));

    function forca(p){
      let s = 0;
      if (p.length >= 8) s++;
      if (/[a-z]/.test(p) && /[A-Z]/.test(p)) s++;
      if (/\d/.test(p)) s++;
      if (/[^A-Za-z0-9]/.test(p) || p.length >= 12) s++;
      return p ? Math.max(1, s) : 0;
    }
    const pwEl = $('#pw');
    if (pwEl) pwEl.addEventListener('input', e => {
      const s = forca(e.target.value), cores = ['', '#dc2626', '#d97706', '#65a30d', '#16a34a'], nomes = ['', 'Fraca', 'Razoável', 'Boa', 'Forte'];
      document.querySelectorAll('.forca i').forEach((el, n) => el.style.background = n < s ? cores[s] : '');
      const ft = $('#forcaTxt'); if (ft) ft.textContent = nomes[s];
    });

    function nifValido(n){
      if (!/^\d{9}$/.test(n) || !'123568912'.includes(n[0]) && !['45','70','71','72','74','75','77','78','79','90','91','98','99'].includes(n.slice(0,2))) return false;
      let soma = 0;
      for (let i = 0; i < 8; i++) soma += +n[i] * (9 - i);
      const d = 11 - (soma % 11);
      return +n[8] === (d >= 10 ? 0 : d);
    }

    const regras = {
      empresa: v => v.trim().length >= 2 || 'Indique o nome da empresa.',
      nif:     v => nifValido(v.trim()) || 'NIF inválido.',
      tipo:    v => !!v || 'Selecione o tipo de empresa.',
      nome:    v => v.trim().length >= 3 || (tipoConta() === 'empresa' ? 'Indique o nome do responsável.' : 'Indique o seu nome.'),
      tel:     v => /^(\+351)?\s?[29]\d{2}\s?\d{3}\s?\d{3}$/.test(v.trim()) || 'Telefone inválido.',
      email:   v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) || 'Email inválido.',
      morada:  v => v.trim().length >= 5 || 'Indique a morada.',
      cp:      v => /^\d{4}-\d{3}$/.test(v.trim()) || 'Código postal inválido (ex.: 2400-000).',
      local:   v => v.trim().length >= 2 || 'Indique a localidade.',
      pw:      v => v.length >= 8 || 'Mínimo de 8 caracteres.',
      pw2:     v => v === $('#pw').value || 'As palavras-passe não coincidem.'
    };
    function validar(id){
      const el = $('#' + id), campo = el.closest('.campo');
      if (!campo || campo.hidden) return true;
      const r = regras[id](el.value);
      campo.classList.toggle('bad', r !== true);
      campo.querySelector('.msg').textContent = r === true ? '' : r;
      return r === true;
    }
    Object.keys(regras).forEach(id => {
      const el = $('#' + id);
      if (el) el.addEventListener('blur', () => validar(id));
    });
    const nifEl = $('#nif');
    if (nifEl) nifEl.addEventListener('input', e => e.target.value = e.target.value.replace(/\D/g, ''));
    const cpEl = $('#cp');
    if (cpEl) cpEl.addEventListener('input', e => {
      const d = e.target.value.replace(/\D/g, '').slice(0, 7);
      e.target.value = d.length > 4 ? d.slice(0, 4) + '-' + d.slice(4) : d;
    });

    const formEl = $('#form');
    if (formEl) formEl.addEventListener('submit', e => {
      e.preventDefault();
      const erro = $('#erro');
      const todos = Object.keys(regras).map(validar).every(Boolean);
      if (!todos || !$('#termos').checked){
        erro.textContent = todos ? 'Tem de aceitar os termos de utilização.' : 'Corrija os campos assinalados.';
        erro.hidden = false; erro.classList.remove('shake'); void erro.offsetWidth; erro.classList.add('shake');
        return;
      }
      erro.hidden = true;
      const empresa = tipoConta() === 'empresa';
      const btn = $('#btn'); btn.disabled = true; btn.textContent = 'A enviar…';
      setTimeout(() => {
        const mail = $('#email').value.trim().replace(/[&<>"']/g, '');
        $('#card').innerHTML = `<div class="ok-box"><div class="tick">✓</div>
          <h2>${empresa ? 'Pedido enviado' : 'Conta criada'}</h2>
          <p>${empresa
            ? `Vamos verificar os dados da sua empresa e enviar a confirmação para <b>${mail}</b>.`
            : `Enviámos um email de confirmação para <b>${mail}</b>. Já pode iniciar sessão.`}</p>
          <a class="entrar" href="login.php">Ir para o login</a></div>`;
      }, 900);
    });
  }
})();