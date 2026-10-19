/**
 * Búsqueda de productos (página Productos).
 *
 * Hay UNA sola barra de búsqueda: la de la navbar (#searchInput).
 *   - En las otras páginas, Enter navega a products.php?q=...
 *   - Acá busca en vivo sin recargar y mantiene la URL igual a lo que dice la barra.
 *
 * Flujo: input / Enter / chip -> fetch a search.php (PHP consulta y responde JSON) -> JS dibuja las cards.
 * Los clicks en las cards ("ver detalle" / "agregar al carrito") los maneja app.js por delegación.
 */
(() => {
  'use strict';

  const DEBOUNCE_MS = 250;                            // espera a que el usuario termine de tipear
  const SEARCH_ENDPOINT = '/src/controllers/products/search.php';

  const input     = document.getElementById('searchInput');
  const form      = input?.closest('form') ?? null;
  const chips     = document.querySelectorAll('#filterChips .filter-chip');
  const grid      = document.getElementById('productsGrid');
  const noResults = document.getElementById('noResults');
  const spinner   = document.getElementById('loadingIndicator');

  // Estado inicial = lo que dice la URL
  const urlParams = new URLSearchParams(location.search);
  let categoriaActiva = urlParams.get('categoria') || '';
  if (input) input.value = urlParams.get('q') || '';

  let debounceTimer = null;
  let controller    = null;                           // para cancelar la búsqueda anterior

  // ---------- Dibujo ----------
  const formatPrecio = (n) => '$ ' + Number(n).toLocaleString('es-AR', { maximumFractionDigits: 0 });

  /** Crea un elemento con clase y texto (textContent: nunca interpreta HTML). */
  function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
  }

  /** Construye la card de un producto. */
  function crearCard(p) {
    const col  = el('div', 'col-6 col-md-4 col-lg-3 product-item');
    const card = el('div', 'card product-card');

    // app.js lee estos data-* para el modal; dataset escapa los valores solo
    card.dataset.productCard = '';
    Object.assign(card.dataset, {
      id: p.id,
      name: p.name,
      category: p.category_name,
      price: p.price,
      description: p.description || '',
      icon: p.category_icon || '',
      image: p.image_url || '',
    });

    const thumb = el('div', 'product-thumb', p.category_icon || ''); // emoji por defecto
    if (p.image_url) {
      const img = new Image();
      img.alt = p.name;
      // Sin loading="lazy": la <img> todavía no está en el documento y el navegador no la cargaría.
      img.addEventListener('load', () => {            // recién cuando carga, reemplaza el emoji
        thumb.classList.add('has-image');
        thumb.replaceChildren(img);
      });
      img.src = p.image_url;                          // si no existe, queda el emoji (sin ícono roto)
    }

    const btn = el('button', 'btn btn-accent btn-sm w-100', 'Agregar al carrito');
    btn.type = 'button';
    btn.disabled = p.stock <= 0;
    btn.dataset.addToCart = '';

    const body = el('div', 'card-body');
    body.append(
      el('span', 'badge badge-stock mb-2', p.stock > 0 ? 'En stock' : 'Sin stock'),
      el('h3', 'h6 card-title mb-1', p.name),
      el('p', 'price mb-2', formatPrecio(p.price)),
      btn
    );

    card.append(thumb, body);
    col.append(card);
    return col;
  }

  function dibujar(productos) {
    grid.replaceChildren(...productos.map(crearCard));
    noResults.textContent = 'No se encontraron productos.';
    noResults.classList.toggle('d-none', productos.length > 0);
  }

  function mostrarError(mensaje) {
    grid.replaceChildren();
    noResults.textContent = mensaje;
    noResults.classList.remove('d-none');
  }

  /** Muestra/oculta el spinner de carga. */
  function setCargando(cargando) {
    spinner?.classList.toggle('d-none', !cargando);
    grid.setAttribute('aria-busy', String(cargando));
  }

  // ---------- URL: siempre refleja lo que dice la barra ----------
  function actualizarURL(texto, categoria) {
    const params = new URLSearchParams();
    if (texto)     params.set('q', texto);
    if (categoria) params.set('categoria', categoria);
    const qs = params.toString();
    history.replaceState(null, '', location.pathname + (qs ? '?' + qs : ''));
  }

  // ---------- Búsqueda ----------
  async function buscar() {
    const texto     = input ? input.value.trim() : '';
    const categoria = categoriaActiva;

    actualizarURL(texto, categoria);

    controller?.abort();                              // cancela la búsqueda anterior
    controller = new AbortController();
    const miController = controller;

    setCargando(true);
    noResults.classList.add('d-none');

    try {
      const params = new URLSearchParams({ q: texto, categoria });
      const res  = await fetch(window.MLA.url(SEARCH_ENDPOINT) + '?' + params, { signal: miController.signal });

      if (res.status === 401) {                       // sesión vencida
        location.href = window.MLA.url('/src/views/auth/login.php');
        return;
      }

      const data = await res.json().catch(() => null);
      if (!res.ok || !data?.success) throw new Error(data?.error || `HTTP ${res.status}`);

      dibujar(data.products);
    } catch (err) {
      if (err.name === 'AbortError') return;          // la reemplazó una búsqueda más nueva
      console.error('[búsqueda]', err);
      mostrarError('No se pudieron cargar los productos. Revisá tu conexión e intentá de nuevo.');
    } finally {
      if (miController === controller) setCargando(false); // solo la última búsqueda apaga el spinner
    }
  }

  // ---------- Eventos ----------
  input?.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(buscar, DEBOUNCE_MS);
  });
  input?.addEventListener('search', () => {           // la "x" de borrar del input type=search
    clearTimeout(debounceTimer);
    buscar();
  });

  form?.addEventListener('submit', (e) => {           // Enter: busca acá, sin recargar la página
    e.preventDefault();
    clearTimeout(debounceTimer);
    buscar();
  });

  chips.forEach((chip) => {
    chip.addEventListener('click', () => {
      categoriaActiva = chip.dataset.categoria;
      chips.forEach((c) => c.classList.toggle('active', c === chip));
      buscar();
    });
    chip.classList.toggle('active', chip.dataset.categoria === categoriaActiva); // chip inicial
  });

  buscar(); // carga inicial
})();
