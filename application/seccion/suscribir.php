<?php
class UrlController extends ProcessTemplate{
	
	function execute($seccion){
		$seccion=new Seccion($seccion);
		if(!$this->usuario or $this->usuario->id==$seccion->usuario->id) redirect(base_url());
		
		if(!$this->usuario->suscripciones->seccionIs($seccion)->count()){
			$suscripcion=$this->usuario->suscripciones->add();
			$suscripcion->seccion=$seccion;
			$suscripcion->save();
			
			$seccion->n_suscripciones++;
			$seccion->save();
		}
		redirect(base_url().'seccion/index/'.$seccion->id);
	}
}
?>