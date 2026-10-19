/**
 * JavaScript común a todas las páginas privadas:
 *  - peticiones al servidor con token CSRF (postForm)
 *  - avisos tipo toast
 *  - modal de detalle de producto
 *  - "Agregar al carrito" con estado de carga y manejo de errores
 *
 * No hay JS inline en las vistas (la Content-Security-Policy lo bloquea);
 * los eventos se enganchan acá por delegación usando atributos data-*.
 */
(() => {
  'use strict';

  const BASE_PATH  = document.querySelector('meta[name="base-path"]')?.content ?? '';
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
  const REQUEST_TIMEOUT_MS = 15000;

  /** Arma una URL respetando la ruta base del sitio. */
  const url = (path) => BASE_PATH + path;

  /**
   * POST (form-urlencoded) con token CSRF y timeout. Devuelve el JSON del servidor.
   * Lanza Error con un mensaje legible si falla la red, vence el tiempo o el servidor responde error.
   */
  async function postForm(path, fields, { signal } = {}) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), REQUEST_TIMEOUT_MS);
    signal?.addEventListener('abort', () => controller.abort());

    try {
      const res = await fetch(url(path), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-CSRF-Token': CSRF_TOKEN,
        },
        body: new URLSearchParams(fields),
        signal: controller.signal,
      });

      const data = await res.json().catch(() => null);

      if (res.status === 401) {                      // la sesión venció: volvemos al login
        window.location.href = url('/src/views/auth/login.php');
        throw new Error('Tu sesión venció.');
      }
      if (!res.ok || !data) {
        throw new Error(data?.error || data?.reply || `El servidor respondió con un error (${res.status}).`);
      }
      return data;
    } catch (err) {
      if (err.name === 'AbortError') throw new Error('El servidor tardó demasiado en responder.');
      if (err instanceof TypeError)  throw new Error('No hay conexión con el servidor.');
      throw err;
    } finally {
      clearTimeout(timer);
    }
  }

  /** Muestra un aviso (toast de Bootstrap). type: 'success' | 'danger' | 'warning' */
  function toast(message, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast align-items-center text-bg-${type} border-0`;
    el.setAttribute('role', 'alert');

    const wrap = document.createElement('div');
    wrap.className = 'd-flex';
    const body = document.createElement('div');
    body.className = 'toast-body';
    body.textContent = message;                       // textContent: nunca interpreta HTML
    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'btn-close btn-close-white me-2 m-auto';
    close.setAttribute('data-bs-dismiss', 'toast');
    close.setAttribute('aria-label', 'Cerrar');
    wrap.append(body, close);
    el.append(wrap);

    document.getElementById('toastContainer').append(el);
    el.addEventListener('hidden.bs.toast', () => el.remove());
    bootstrap.Toast.getOrCreateInstance(el, { delay: 4000 }).show();
  }

  // ---------- Modal de detalle de producto ----------
  let currentProductId = null;

  function openProductModal(card) {
    currentProductId = card.dataset.id;
    document.getElementById('pmName').textContent        = card.dataset.name;
    document.getElementById('pmCategory').textContent    = card.dataset.category;
    document.getElementById('pmDescription').textContent = card.dataset.description;
    document.getElementById('pmPrice').textContent       = '$ ' + Number(card.dataset.price).toLocaleString('es-AR');
    document.getElementById('pmQty').value = 1;

    const pmIcon = document.getElementById('pmIcon');
    pmIcon.classList.remove('has-image');
    pmIcon.textContent = card.dataset.icon || '📦';   // emoji por defecto

    if (card.dataset.image) {                         // solo reemplaza el emoji si la foto cargó
      const img = new Image();
      img.alt = card.dataset.name;
      img.onload = () => {
        pmIcon.classList.add('has-image');
        pmIcon.replaceChildren(img);
      };
      img.src = card.dataset.image;
    }

    bootstrap.Modal.getOrCreateInstance(document.getElementById('productModal')).show();
  }

  // ---------- Agregar al carrito ----------
  /**
   * @param {string} productId
   * @param {number} qty
   * @param {HTMLButtonElement} [button] botón que se deshabilita mientras dura la petición
   */
  async function addToCart(productId, qty, button) {
    const originalLabel = button?.textContent;
    if (button) {
      button.disabled = true;
      button.textContent = 'Agregando...';
    }

    try {
      const data = await postForm('/src/controllers/cart/add.php', { product_id: productId, qty });
      if (!data.success) throw new Error(data.error || 'No se pudo agregar el producto.');

      document.getElementById('cartBadge').textContent = data.cart_count;
      bootstrap.Modal.getInstance(document.getElementById('productModal'))?.hide();
      toast('Producto agregado al carrito');
    } catch (err) {
      toast(err.message, 'danger');
    } finally {
      if (button) {
        button.disabled = false;
        button.textContent = originalLabel;
      }
    }
  }

  // ---------- Eventos (delegación: también funciona con cards creadas después por JS) ----------
  document.addEventListener('click', (ev) => {
    const addBtn = ev.target.closest('[data-add-to-cart]');
    if (addBtn) {
      const card = addBtn.closest('[data-product-card]');
      if (card) addToCart(card.dataset.id, 1, addBtn);
      return;                                         // no abrir el modal
    }

    const card = ev.target.closest('[data-product-card]');
    if (card) openProductModal(card);
  });

  document.getElementById('pmAddBtn')?.addEventListener('click', (ev) => {
    const qty = Math.max(1, parseInt(document.getElementById('pmQty').value, 10) || 1);
    addToCart(currentProductId, qty, ev.currentTarget);
  });

  // Se expone solo lo que necesitan los otros scripts
  window.MLA = { url, postForm, toast };
})();
