<?php
class UrlController extends ProcessTemplate{
	function execute($apartado,$destino,$hijo=0){
		$apartado=new Apartado($apartado);
		$destino=new Apartado($destino);
		if(!$this->esResponsable($apartado) or !$this->esResponsable($destino)) return;
		if($hijo){
			$apartado->padre=$destino;
			$apartado->save();
		}
		else $apartado->insertBefore($destino);
	}
}
?>