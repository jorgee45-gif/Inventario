<h3>Salidas de material</h3>
<div class="card">
  <div class="card-body">
    <form method="post" action="<?= url('/salidas') ?>" class="row gy-2 gx-2 align-items-end">
      <div class="col-md-6">
        <label class="form-label">Producto</label>
        <select name="producto_id" class="form-select" required>
          <option value="">Seleccione...</option>
          <?php foreach ($productos as $p): ?>
            <option value="<?= e((string)$p['id']) ?>"><?= e($p['nombre']) ?> (Stock: <?= e((string)$p['cantidad']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Cantidad</label>
        <input type="number" name="cantidad" min="1" class="form-control" required>
      </div>
      <div class="col-md-3">
        <button class="btn btn-primary w-100">Registrar salida</button>
      </div>
    </form>
  </div>
</div>
