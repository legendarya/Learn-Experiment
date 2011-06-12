<?php
class UrlController extends ProcessTemplate{
	
	function execute($suscripcion){
		$suscripcion=new Suscripcion($suscripcion);
		if(!$this->usuario or $this->usuario->id!=$suscripcion->usuario->id) redirect(base_url());
	
		$suscripcion->seccion->n_suscripciones--;
		$suscripcion->seccion->save();
		
		$suscripcion->delete();
		redirect(base_url().'seccion/index/'.$suscripcion->seccion->id);
	}
}
?>