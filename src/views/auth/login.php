<?php require_once __DIR__ . '/../_layouts/auth.layout.php'; ?>

<div class="text-center mb-4">
  <div class="auth-logo mb-2"><img src="<?= e(url('/assets/img/logo.jpg')) ?>" alt="<?= e(config('app.name')) ?>"></div>
  <p class="text-secondary small">Ingresá con tu cuenta</p>
</div>

<form action="<?= e(url('/src/controllers/auth/login.php')) ?>" method="POST">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label for="email" class="form-label">E-mail</label>
    <input type="email" class="form-control" id="email" name="email" maxlength="150"
           value="<?= e($old['email'] ?? '') ?>" required autofocus autocomplete="email">
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Contraseña</label>
    <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
  </div>

  <button type="submit" class="btn btn-accent w-100">Ingresar</button>
</form>

<div class="text-center mt-4">
  <p class="text-secondary small">¿No tenés una cuenta? <a href="<?= e(url('/src/views/auth/register.php')) ?>">¡Registrate ahora!</a></p>
</div>

<?php require_once __DIR__ . '/../_layouts/auth.footer.php'; ?>
