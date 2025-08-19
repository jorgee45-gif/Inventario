<?php $flashes=$flashes??[]; ?>
<!doctype html><html lang="es"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body><div class="container py-3">
<?php foreach($flashes as $t=>$lst){ foreach($lst as $m){ ?>
<div class="alert alert-<?=e($t)?> alert-dismissible fade show" role="alert">
  <?=e($m)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php } } ?>
