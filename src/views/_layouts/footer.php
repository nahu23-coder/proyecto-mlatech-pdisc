<?php
/**
 * Cierre del layout privado: footer, modal de producto, widget de chat y scripts.
 * Las vistas pueden definir $pageScripts = ['/assets/js/x.js'] antes de incluirlo.
 */
$pageScripts = $pageScripts ?? [];
?>
  </main>

  <footer class="site-footer">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
      <div>
        <strong>MLA <span class="brand-accent">TECH</span></strong>
        <span class="text-secondary small ms-2">Componentes, periféricos y accesorios para tu PC</span>
      </div>
      <div class="text-secondary small">
        <a href="<?= e(url('/src/views/contact.php')) ?>" class="text-secondary">Contacto</a>
        ·
        &copy; <?= date('Y') ?> <?= e(config('app.name')) ?> · Proyecto PDISC 7° Año 4° División
      </div>
    </div>
  </footer>

  <!-- Modal de detalle de producto (lo llena app.js con los data-* de cada card) -->
  <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-secondary-subtle">
          <h5 class="modal-title" id="pmName">Producto</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="product-thumb mb-3" id="pmIcon" style="font-size:4rem; border-radius:.5rem; height:260px;">📦</div>
          <span class="badge badge-stock mb-2" id="pmCategory">Categoría</span>
          <p id="pmDescription" class="text-secondary"></p>
          <p class="price fs-4" id="pmPrice">$ 0</p>
          <div class="d-flex align-items-center gap-2">
            <label for="pmQty" class="form-label mb-0">Cantidad</label>
            <input type="number" id="pmQty" class="form-control form-control-sm" style="width:80px"
                   value="1" min="1" max="<?= (int) config('cart.max_qty_per_item') ?>">
          </div>
        </div>
        <div class="modal-footer border-secondary-subtle">
          <button type="button" id="pmAddBtn" class="btn btn-accent w-100">Agregar al carrito</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Avisos (toasts) -->
  <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" aria-live="polite"></div>

  <!-- Widget de soporte con IA (lógica en assets/js/chat.js) -->
  <button id="ai-chat-toggle" type="button" aria-label="Abrir soporte">💬</button>
  <div id="ai-chat-window">
    <div id="ai-chat-header">Soporte <?= e(config('app.name')) ?></div>
    <div id="ai-chat-body">
      <div class="chat-msg bot">¡Hola! Soy el asistente de <?= e(config('app.name')) ?>. Preguntame sobre envíos, garantía, medios de pago o cualquier producto.</div>
    </div>
    <form id="ai-chat-form">
      <input type="text" id="ai-chat-input" placeholder="Escribí tu consulta..." autocomplete="off"
             maxlength="<?= (int) config('ai.max_chars') ?>" required>
      <button type="submit" aria-label="Enviar">➤</button>
    </form>
  </div>

  <script src="<?= e(url('/assets/js/bootstrap.min.js')) ?>"></script>
  <script src="<?= e(url('/assets/js/app.js')) ?>"></script>
  <script src="<?= e(url('/assets/js/chat.js')) ?>"></script>
  <?php foreach ($pageScripts as $script): ?>
  <script src="<?= e(url($script)) ?>"></script>
  <?php endforeach; ?>
</body>
</html>
