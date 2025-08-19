<a class="navbar-brand" href="<?= url('/inventario') ?>">Inventario</a>
<!-- … -->
<a class="nav-link" href="<?= url('/inventario') ?>">Inventario</a>
<a class="nav-link" href="<?= url('/salidas') ?>">Salidas</a>
<a class="nav-link" href="<?= url('/movimientos') ?>">Histórico</a>
<a class="btn btn-outline-light btn-sm" href="<?= url('/logout') ?>">Salir</a>




<<?php $u = current_user(); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 rounded">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= url('/inventario') ?>">Inventario</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if ($u): ?>
          <li class="nav-item"><a class="nav-link" href="<?= url('/inventario') ?>">Inventario</a></li>
          <?php if (strtoupper($u['rol']) === 'ALMACENISTA'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= url('/salidas') ?>">Salidas</a></li>
          <?php endif; ?>
          <?php if (strtoupper($u['rol']) === 'ADMIN'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= url('/movimientos') ?>">Histórico</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if ($u): ?>
          <li class="nav-item"><span class="navbar-text text-white me-3"><?= e($u['nombre']) ?> (<?= e($u['rol']) ?>)</span></li>
          <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?= url('/logout') ?>">Salir</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
