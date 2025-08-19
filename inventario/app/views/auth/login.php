<div class="row justify-content-center">
  <div class="col-md-4">
    <div class="card shadow">
      <div class="card-header bg-primary text-white">Iniciar sesión</div>
      <div class="card-body">
        <form method="post" action="<?= url('/login') ?>">
          <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button class="btn btn-primary w-100">Entrar</button>
        </form>
      </div>
    </div>
  </div>
</div>
