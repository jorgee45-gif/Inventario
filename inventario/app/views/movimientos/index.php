<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Histórico de movimientos</h3>
  <div>
    <a class="btn btn-outline-secondary btn-sm<?= $tipo==='TODOS'?' active':'' ?>" href="<?= url('/movimientos') ?>?tipo=TODOS">Todos</a>
    <a class="btn btn-outline-secondary btn-sm<?= $tipo==='ENTRADA'?' active':'' ?>" href="<?= url('/movimientos') ?>?tipo=ENTRADA">Entradas</a>
    <a class="btn btn-outline-secondary btn-sm<?= $tipo==='SALIDA'?' active':'' ?>" href="<?= url('/movimientos') ?>?tipo=SALIDA">Salidas</a>
  </div>
</div>

<div class="card">
  <div class="card-body table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>#</th><th>Fecha</th><th>Tipo</th><th>Producto</th><th>Cantidad</th><th>Usuario</th><th>Rol</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($movimientos as $m): ?>
        <tr>
          <td><?= e((string)$m['id']) ?></td>
          <td><?= e((string)$m['fecha']) ?></td>
          <td><?= $m['tipo']==='ENTRADA' ? '<span class="badge bg-primary">ENTRADA</span>' : '<span class="badge bg-warning text-dark">SALIDA</span>' ?></td>
          <td>[<?= e((string)$m['producto_id']) ?>] <?= e($m['producto']) ?></td>
          <td><?= e((string)$m['cantidad']) ?></td>
          <td>[<?= e((string)$m['usuario_id']) ?>] <?= e($m['usuario']) ?></td>
          <td><?= e($m['rol']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
