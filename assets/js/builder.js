/**
 * Armador de PC: resumen en vivo, total y validación visual.
 *
 * El formulario funciona solo (radios/checkbox + POST). Este script únicamente:
 *   - arma la lista "Tu configuración" y suma el total cada vez que el usuario elige,
 *   - avisa qué componentes obligatorios faltan (con link a cada sección),
 *   - habilita "Finalizar" solo cuando se puede finalizar,
 *   - repone el emoji si la foto de un producto no carga.
 * La validación REAL la hace el servidor (cart/add_build.php); esto es solo ayuda visual.
 */
(() => {
  'use strict';

  const form = document.getElementById('builderForm');
  if (!form) return;

  const list       = document.getElementById('summaryList');
  const missingBox = document.getElementById('summaryMissing');
  const totalEl    = document.getElementById('summaryTotal');
  const finishBtn  = document.getElementById('finishBtn');
  const clearBtn   = document.getElementById('clearBtn');
  const sections   = [...form.querySelectorAll('[data-slot-section]')];

  const formatPrice = (n) => '$ ' + Number(n).toLocaleString('es-AR', { maximumFractionDigits: 0 });

  /** Crea un elemento con clase y texto (textContent: nunca interpreta HTML). */
  function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
  }

  /** Recalcula resumen, total y estado del botón a partir de lo marcado en el formulario. */
  function update() {
    let total = 0;
    let count = 0;
    const missing = [];
    list.replaceChildren();

    sections.forEach((section) => {
      // value === '' es la opción "Sin este componente"
      const chosen = [...section.querySelectorAll('input.build-input:checked')].filter((i) => i.value !== '');

      section.classList.toggle('is-done', chosen.length > 0);
      section.querySelector('.slot-done').classList.toggle('d-none', chosen.length === 0);

      if (chosen.length === 0 && section.dataset.required === '1') {
        missing.push(section);
      }

      chosen.forEach((input) => {
        total += Number(input.dataset.price) || 0;
        count++;

        const li   = el('li', 'd-flex justify-content-between gap-2 small mb-2');
        const info = el('span');
        info.append(el('span', 'text-secondary d-block', section.dataset.label), el('span', '', input.dataset.name));
        li.append(info, el('span', 'price text-nowrap', formatPrice(input.dataset.price)));
        list.append(li);
      });
    });

    totalEl.textContent = formatPrice(total);

    // Mensaje de estado
    missingBox.replaceChildren();
    if (count === 0) {
      missingBox.textContent = 'Elegí tus componentes para ver el resumen.';
    } else if (missing.length > 0) {
      missingBox.append('Falta elegir: ');
      missing.forEach((section, i) => {
        const link = el('a', '', section.dataset.label);
        link.href = '#slot-' + section.dataset.key;
        missingBox.append(link, i < missing.length - 1 ? ', ' : '.');
      });
    } else {
      missingBox.textContent = '¡Listo! Tu configuración está completa.';
    }

    finishBtn.disabled = count === 0 || missing.length > 0;
  }

  /** Deja todo sin elegir ("Sin este componente" en los opcionales de elección única). */
  function clearAll() {
    form.querySelectorAll('input.build-input').forEach((input) => {
      input.checked = input.type === 'radio' && input.value === '';
    });
    update();
  }

  // Si una foto no carga, se muestra el emoji de la categoría en lugar del ícono roto.
  function protectImages() {
    form.querySelectorAll('.cart-thumb img').forEach((img) => {
      const fallback = () => {
        const thumb = img.parentElement;
        thumb.classList.add('no-image');
        thumb.textContent = thumb.dataset.icon || '📦';
      };
      img.addEventListener('error', fallback);
      if (img.complete && img.naturalWidth === 0) fallback();
    });
  }

  form.addEventListener('change', update);
  clearBtn.addEventListener('click', clearAll);

  form.addEventListener('submit', (ev) => {
    if (finishBtn.disabled) {
      ev.preventDefault();
      return;
    }
    finishBtn.disabled = true;                     // evita doble envío
    finishBtn.textContent = 'Armando tu carrito...';
  });

  protectImages();
  update();
})();
