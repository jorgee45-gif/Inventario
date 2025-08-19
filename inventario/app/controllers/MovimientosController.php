<?php
declare(strict_types=1);
class MovimientosController extends Controller{
  private MovimientoModel $movs;
  public function __construct(){ $this->movs=new MovimientoModel(); }
  public function index():void{ require_login(); $u=current_user(); if(strtoupper($u['rol'])!=='ADMIN'){ flash('danger','No autorizado: solo ADMIN.'); redirect('/inventario'); }
    $tipo=strtoupper(trim($_GET['tipo']??'TODOS')); try{ $items=$this->movs->historial((int)$u['id'],$tipo); $this->render('movimientos/index',['movimientos'=>$items,'tipo'=>$tipo]); }
    catch(Throwable $e){ flash('danger',$e->getMessage()); redirect('/inventario'); }
  }
}
