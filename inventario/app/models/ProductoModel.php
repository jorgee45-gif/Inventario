<?php
declare(strict_types=1);

class ProductoModel {
  public function inventario(string $f): array {
    $view = 'vw_inventario';
    $f = strtolower($f);
    if ($f === 'activos')   $view = 'vw_inventario_activo';
    if ($f === 'inactivos') $view = 'vw_inventario_inactivo';
    return db()->query('SELECT * FROM '.$view)->fetchAll();
  }

  // Devuelve el producto insertado 
  public function registrarProducto(int $userId, string $nombre): array {
    $stmt = db()->prepare("CALL sp_registrar_producto(?, ?)");
    $stmt->execute([$userId, $nombre]);
    $row = $stmt->fetch();            // primer result set (el producto)
    while ($stmt->nextRowset()) {}    // consume cualquier result set remanente
    $stmt->closeCursor();
    return $row ?: [];
  }

  public function darBaja(int $userId, int $productoId): void {
    $stmt = db()->prepare("CALL sp_dar_baja_producto(?, ?)");
    $stmt->execute([$userId, $productoId]);
    while ($stmt->nextRowset()) {}
    $stmt->closeCursor();
  }

  public function reactivar(int $userId, int $productoId): void {
    $stmt = db()->prepare("CALL sp_reactivar_producto(?, ?)");
    $stmt->execute([$userId, $productoId]);
    while ($stmt->nextRowset()) {}
    $stmt->closeCursor();
  }

  public function entrada(int $userId, int $productoId, int $cantidad): void {
    $stmt = db()->prepare("CALL sp_entrada_producto(?, ?, ?)");
    $stmt->execute([$userId, $productoId, $cantidad]);
    while ($stmt->nextRowset()) {}
    $stmt->closeCursor();
  }

  public function productosParaSalida(): array {
    return db()->query("SELECT * FROM vw_productos_para_salida")->fetchAll();
  }

  public function salida(int $userId, int $productoId, int $cantidad): void {
    $stmt = db()->prepare("CALL sp_salida_producto(?, ?, ?)");
    $stmt->execute([$userId, $productoId, $cantidad]);
    while ($stmt->nextRowset()) {}
    $stmt->closeCursor();
  }
}

