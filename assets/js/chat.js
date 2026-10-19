/**
 * Widget de soporte (chat flotante).
 * Flujo: submit -> POST a chat.php (PHP decide si responde con IA o con FAQ) -> JSON {reply} -> se dibuja el mensaje.
 * Usa window.MLA.postForm (app.js), que ya maneja CSRF, timeout y errores de red.
 */
(() => {
  'use strict';

  const toggle = document.getElementById('ai-chat-toggle');
  const win    = document.getElementById('ai-chat-window');
  const body   = document.getElementById('ai-chat-body');
  const form   = document.getElementById('ai-chat-form');
  const input  = document.getElementById('ai-chat-input');
  if (!toggle || !form) return;

  toggle.addEventListener('click', () => win.classList.toggle('open'));

  /** Agrega un mensaje al chat. autor: 'user' | 'bot'. Devuelve el nodo creado. */
  function appendMsg(text, author) {
    const div = document.createElement('div');
    div.className = 'chat-msg ' + author;
    div.textContent = text;                           // textContent evita XSS
    body.append(div);
    body.scrollTop = body.scrollHeight;
    return div;
  }

  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();
    const message = input.value.trim();
    if (!message) return;

    appendMsg(message, 'user');
    input.value = '';
    input.disabled = true;                            // evita enviar mientras se espera
    const typing = appendMsg('Escribiendo...', 'bot'); // indicador de carga

    try {
      const data = await window.MLA.postForm('/src/controllers/ai/chat.php', { message });
      typing.textContent = data.reply;
    } catch (err) {
      typing.textContent = err.message || 'No pude conectarme con el soporte, intentá de nuevo en unos segundos.';
    } finally {
      input.disabled = false;
      input.focus();
      body.scrollTop = body.scrollHeight;
    }
  });
})();
