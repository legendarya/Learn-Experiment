<?php
require_once("HTMLgenerator.php");
class HTMLRedirectGenerator extends HTMLgenerator{
	function make_vImage($element){
		
		// Si es una imagen externa requerimos ancho y alto
		$dir=base_url().$element->getFile();
		if(substr($element->getFile(),0,7)=='http://'){
			$dir=$element->getFile();
			$width=$element->getWidth();
			$height=$element->getHeight();
		}
		// Si est�n definidos ancho y alto, usamos esos
		else if($element->getWidth() or $element->getHeight()){
			$width=$element->getWidth();
			$height=$element->getHeight();
		}
		// Si no, miramos el tama�o de la imagen
		else list($width,$height)=getimagesize($element->getFile());
		
		return '<img'.$this->setStyle($element).($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' alt="'.$element->getAltText().'" src="'.
			$dir.'" />
		';
	}

	function base_url(){
		return base_url();
	}
}
?>
