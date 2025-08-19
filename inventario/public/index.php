<?php
declare(strict_types=1);
session_start();
// Autoload simple
spl_autoload_register(function($class){
  $base = __DIR__ . '/../app/';
  foreach (['core','controllers','models'] as $f){
    $file = $base . $f . '/' . $class . '.php';
    if (file_exists($file)) { require_once $file; return; }
  }
});
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/helpers.php';
$router = new Router();
$router->get('/', [AuthController::class, 'loginForm']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/inventario', [InventarioController::class, 'index']);
$router->post('/producto/nuevo', [InventarioController::class, 'nuevo']);
$router->post('/producto/{id}/baja', [InventarioController::class, 'baja']);
$router->post('/producto/{id}/reactivar', [InventarioController::class, 'reactivar']);
$router->post('/entrada', [InventarioController::class, 'entrada']);
$router->get('/salidas', [SalidaController::class, 'form']);
$router->post('/salidas', [SalidaController::class, 'registrar']);
$router->get('/movimientos', [MovimientosController::class, 'index']);
$router->dispatch();
