<?php
declare(strict_types=1);
class UserModel{
  public function login(string $correo,string $password):array{
    $st=db()->prepare('CALL sp_login(?, ?)'); $st->execute([$correo,$password]);
    $u=$st->fetch(); $st->closeCursor();
    if(!$u) throw new RuntimeException('Credenciales inválidas.');
    return $u;
  }
}
