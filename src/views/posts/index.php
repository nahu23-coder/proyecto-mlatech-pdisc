<?php
include(__DIR__ . '/../_layouts/layout.php');
?>

<header class="row">
  <div class="col">
    <h1>Listado de productos</h1>
    <p>Aquí puedes ver todos los productos de nuestra pagina</p>
  </div>
  <div class="col d-flex justify-content-end align-items-center">
    
  </div>
</header>

<main>
  <?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
      <article class="card mb-3">
        <div class="card-body">
          <h2 class="card-title"><a href="/src/controllers/posts/show.php?id=<?= $post['id'] ?>"><?= $post['name'] ?></a></h2>
          <p class="card-text"><?= $post['description'] ?></p>
          
          <small class="text-muted"><?= $post['price'] ?></small>
          
          <p class="card-text"><strong></strong> <?= $post['stock'] ?></p>
          
          
        </div>
      </article>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No hay productos todavía.</p>
  <?php endif; ?>
</main>