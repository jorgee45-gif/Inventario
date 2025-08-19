<?php
declare(strict_types=1);
class InventarioController extends Controller{
  private ProductoModel $productos;
  public function __construct(){ $this->productos=new ProductoModel(); }
  public function index():void{ require_login(); $f=$_GET['f']??'activos'; $items=$this->productos->inventario($f); $this->render('inventario/index',['productos'=>$items,'filtro'=>$f]); }
  public function nuevo():void{ require_login(); $u=current_user(); if(strtoupper($u['rol'])!=='ADMIN'){ flash('danger','No autorizado.'); redirect('/inventario'); }
    try{ $n=trim($_POST['nombre']??''); if($n==='') throw new RuntimeException('Nombre requerido.'); $this->productos->registrarProducto((int)$u['id'],$n); flash('success','Producto registrado.'); }
    catch(Throwable $e){ flash('danger',$e->getMessage()); } redirect('/inventario');
  }
  public function baja(string $id):void{ require_login(); $u=current_user(); if(strtoupper($u['rol'])!=='ADMIN'){ flash('danger','No autorizado.'); redirect('/inventario'); }
    try{ $this->productos->darBaja((int)$u['id'],(int)$id); flash('info','Producto dado de baja.'); }
    catch(Throwable $e){ flash('danger',$e->getMessage()); } redirect('/inventario?f=activos');
  }
  public function reactivar(string $id):void{ require_login(); $u=current_user(); if(strtoupper($u['rol'])!=='ADMIN'){ flash('danger','No autorizado.'); redirect('/inventario'); }
    try{ $this->productos->reactivar((int)$u['id'],(int)$id); flash('success','Producto reactivado.'); }
    catch(Throwable $e){ flash('danger',$e->getMessage()); } redirect('/inventario?f=inactivos');
  }
  public function entrada():void{ require_login(); $u=current_user(); if(strtoupper($u['rol'])!=='ADMIN'){ flash('danger','No autorizado.'); redirect('/inventario'); }
    try{ $pid=(int)($_POST['producto_id']??0); $c=(int)($_POST['cantidad']??0); $this->productos->entrada((int)$u['id'],$pid,$c); flash('success','Entrada registrada.'); }
    catch(Throwable $e){ flash('danger',$e->getMessage()); } redirect('/inventario');
  }
}
