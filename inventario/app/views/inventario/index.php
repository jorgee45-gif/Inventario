<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Inventario</h3>
  <div>
    <a class="btn btn-outline-secondary btn-sm<?= $filtro==='activos'?' active':'' ?>" href="<?= url('/inventario') ?>?f=activos">Activos</a>
    <a class="btn btn-outline-secondary btn-sm<?= $filtro==='inactivos'?' active':'' ?>" href="<?= url('/inventario') ?>?f=inactivos">Inactivos</a>
    <a class="btn btn-outline-secondary btn-sm<?= $filtro==='todos'?' active':'' ?>" href="<?= url('/inventario') ?>?f=todos">Todos</a>
  </div>
</div>

<?php $u=current_user(); if ($u && strtoupper($u['rol'])==='ADMIN'): ?>
<div class="card mb-4">
  <div class="card-header">Nuevo producto</div>
  <div class="card-body">
    <form class="row g-2" method="post" action="<?= url('/producto/nuevo') ?>">
      <div class="col-md-8">
        <input type="text" name="nombre" class="form-control" placeholder="Nombre del producto" required>
      </div>
      <div class="col-md-4">
        <button class="btn btn-success w-100">Registrar</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-body table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Estatus</th>
          <th style="width: 360px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($productos as $p): ?>
        <tr>
          <td><?= e((string)$p['id']) ?></td>
          <td><?= e($p['nombre']) ?></td>
          <td><?= e((string)$p['cantidad']) ?></td>
          <td>
            <?php if ((int)$p['activo']===1): ?>
              <span class="badge bg-success">ACTIVO</span>
            <?php else: ?>
              <span class="badge bg-secondary">INACTIVO</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($u && strtoupper($u['rol'])==='ADMIN'): ?>
              <?php if ((int)$p['activo']===1): ?>
                <form class="d-inline" method="post" action="<?= url('/producto/' . $p['id'] . '/baja') ?>" onsubmit="return confirm('¿Dar de baja este producto?')">
                  <button class="btn btn-outline-danger btn-sm">Baja</button>
                </form>

                <form class="d-inline" method="post" action="<?= url('/entrada') ?>">
                  <input type="hidden" name="producto_id" value="<?= e((string)$p['id']) ?>">
                  <div class="input-group input-group-sm" style="width: 200px;">
                    <input type="number" name="cantidad" class="form-control" min="1" placeholder="Cantidad" required>
                    <button class="btn btn-outline-primary">Entrada</button>
                  </div>
                </form>
              <?php else: ?>
                <form class="d-inline" method="post" action="<?= url('/producto/' . $p['id'] . '/reactivar') ?>">
                  <button class="btn btn-outline-success btn-sm">Reactivar</button>
                </form>
              <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
