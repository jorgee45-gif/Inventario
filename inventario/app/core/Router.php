<?php
declare(strict_types=1);

class Router {
  private array $routes = ['GET'=>[], 'POST'=>[]];

  public function get(string $pattern, $handler): void { $this->add('GET', $pattern, $handler); }
  public function post(string $pattern, $handler): void { $this->add('POST', $pattern, $handler); }

  private function add(string $method, string $pattern, $handler): void {
    // /producto/{id} -> (?P<id>[^/]+)
    $regex = preg_replace('#\{([a-zA-Z_]\w*)\}#', '(?P<$1>[^/]+)', $pattern);
    $this->routes[$method][] = ['regex' => '#^'.$regex.'$#', 'handler' => $handler];
  }

  public function dispatch(): void {
    // Ruta pedida tal como llega
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    
    $candidates = [];

   
    if (defined('APP_BASE') && APP_BASE !== '') {
      $candidates[] = rtrim(str_replace('\\','/', APP_BASE), '/');
    }

    // Derivados del servidor
    $sn = str_replace('\\','/', $_SERVER['SCRIPT_NAME'] ?? '');  //  /inventario/public/index.php
    $ps = str_replace('\\','/', $_SERVER['PHP_SELF']    ?? '');  // /inventario/public/index.php
    $candidates[] = rtrim(dirname($sn), '/');
    $candidates[] = rtrim(dirname($ps), '/');

    // Limpia duplicados y vacíos
    $bases = array_values(array_unique(array_filter($candidates)));

   
    foreach ($bases as $base) {
      if ($base && $base !== '/' && strpos($path, $base) === 0) {
        $path = substr($path, strlen($base));
        break;
      }
    }

    
    $pos = strpos($path, '/public');
    if ($pos !== false) {
      $path = substr($path, $pos + strlen('/public')); 
    }

    //  Normalizaciones
    if ($path === '' || $path === false) $path = '/';
    if ($path === '/index.php') $path = '/';
    if ($path[0] !== '/') $path = '/'.$path;

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    foreach ($this->routes[$method] ?? [] as $r) {
      if (preg_match($r['regex'], $path, $m)) {
        $params = [];
        foreach ($m as $k=>$v) if (!is_int($k)) $params[$k] = $v;
        $h = $r['handler'];
        if (is_callable($h)) { call_user_func_array($h, $params); return; }
        if (is_array($h) && count($h)===2) { [$cls,$fn] = $h; $c = new $cls(); call_user_func_array([$c,$fn], $params); return; }
      }
    }

    http_response_code(404);
    echo "<h1>404</h1><p>No encontrado: ".htmlspecialchars($path)."</p>";
  }
}

