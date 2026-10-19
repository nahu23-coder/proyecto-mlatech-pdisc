<?php require_once __DIR__ . '/../_layouts/auth.layout.php'; ?>

<div class="text-center mb-4">
  <div class="auth-logo mb-2"><img src="<?= e(url('/assets/img/logo.jpg')) ?>" alt="<?= e(config('app.name')) ?>"></div>
  <p class="text-secondary small">Creá tu cuenta</p>
</div>

<form action="<?= e(url('/src/controllers/auth/register.php')) ?>" method="POST">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label for="email" class="form-label">E-mail</label>
    <input type="email" class="form-control" id="email" name="email" maxlength="150"
           value="<?= e($old['email'] ?? '') ?>" required autofocus autocomplete="email">
  </div>

  <div class="mb-3">
    <label for="name" class="form-label">Usuario</label>
    <input type="text" class="form-control" id="name" name="name" minlength="2" maxlength="50"
           value="<?= e($old['name'] ?? '') ?>" required autocomplete="username">
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Contraseña</label>
    <input type="password" class="form-control" id="password" name="password"
           minlength="<?= (int) config('auth.password_min') ?>" maxlength="<?= (int) config('auth.password_max') ?>"
           required autocomplete="new-password">
    <div class="form-text">Mínimo <?= (int) config('auth.password_min') ?> caracteres.</div>
  </div>

  <div class="mb-3">
    <label for="repeatPassword" class="form-label">Repetí tu contraseña</label>
    <input type="password" class="form-control" id="repeatPassword" name="repeatPassword" required autocomplete="new-password">
  </div>

  <button type="submit" class="btn btn-accent w-100">Registrarme</button>
</form>

<div class="text-center mt-4">
  <p class="text-secondary small">¿Ya tenés una cuenta? <a href="<?= e(url('/src/views/auth/login.php')) ?>">¡Inicia sesión!</a></p>
</div>

<?php require_once __DIR__ . '/../_layouts/auth.footer.php'; ?>
