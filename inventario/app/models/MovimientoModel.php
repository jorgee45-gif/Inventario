<?php
declare(strict_types=1);
class MovimientoModel{
  public function historial(int $uid,string $tipo):array{
    $st=db()->prepare('CALL sp_historial_movimientos(?, ?)'); $st->execute([$uid,$tipo]); $rows=$st->fetchAll(); $st->closeCursor(); return $rows;
  }
}
