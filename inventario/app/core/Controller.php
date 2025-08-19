<?php
declare(strict_types=1);
class Controller{
  protected function render(string $view,array $data=[]){
    $flashes=get_flashes(); extract($data);
    include __DIR__.'/../views/layout/header.php';
    include __DIR__.'/../views/layout/nav.php';
    include __DIR__.'/../views/'.$view.'.php';
    include __DIR__.'/../views/layout/footer.php';
  }
}
