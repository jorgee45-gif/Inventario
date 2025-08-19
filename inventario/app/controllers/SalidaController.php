<?php
declare(strict_types=1);
class SalidaController extends Controller{
  private ProductoModel $productos;
  public function __construct(){ $this->productos=new ProductoModel(); }
  public function form():void{ require_login(); $u=current_user(); if(strtoupper($u['rol'])!=='ALMACENISTA'){ flash('danger','No autorizado: solo ALMACENISTA.'); redirect('/inventario'); }
    $items=$this->productos->productosParaSalida(); $this->render('salidas/index',['productos'=>$items]);
  }
  public function registrar():void{ require_login(); $u=current_user(); if(strtoupper($u['rol'])!=='ALMACENISTA'){ flash('danger','No autorizado: solo ALMACENISTA.'); redirect('/inventario'); }
    try{ $pid=(int)($_POST['producto_id']??0); $c=(int)($_POST['cantidad']??0); $this->productos->salida((int)$u['id'],$pid,$c); flash('success','Salida registrada.'); }
    catch(Throwable $e){ flash('danger',$e->getMessage()); } redirect('/salidas');
  }
}
