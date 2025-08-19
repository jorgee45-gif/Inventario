<?php
declare(strict_types=1);
class AuthController extends Controller{
  private UserModel $users;
  public function __construct(){ $this->users=new UserModel(); }
  public function loginForm():void{ if(current_user()) redirect('/inventario'); $this->render('auth/login'); }
  public function login():void{
    try{ $c=trim($_POST['correo']??''); $p=trim($_POST['password']??''); if($c===''||$p==='') throw new RuntimeException('Correo y contraseña requeridos.');
      $u=$this->users->login($c,$p); $_SESSION['user']=$u; flash('success','Bienvenido '.$u['nombre'].' ('.$u['rol'].')'); redirect('/inventario');
    }catch(Throwable $e){ flash('danger',$e->getMessage()); redirect('/login'); }
  }
  public function logout():void{ session_destroy(); redirect('/login'); }
}
