<?php
require_once("HTMLgenerator.php");
class HTMLRedirectGenerator extends HTMLgenerator{
	function make_vImage($element){
		// Si es una imagen externa requerimos ancho y alto
		if(substr($element->getFile(),0,7)=='http://'){
			$width=$element->getWidth();
			$height=$element->getHeight();
		}
		// Si están definidos ancho y alto, usamos esos
		else if($element->getWidth() or $element->getHeight()){
			$width=$element->getWidth();
			$height=$element->getHeight();
		}
		// Si no, miramos el tamaño de la imagen
		else{
			list($width,$height)=getimagesize($element->getFile());
		}
		
		if(!isset($_SERVER['REDIRECT_URL'])) $base_dir='';
		else if( $_SERVER['SERVER_NAME']=='127.0.0.1')
			$base_dir=str_repeat('../',substr_count($_SERVER['REDIRECT_URL'],'/')-2);
		else if(substr_count($_SERVER['REDIRECT_URL'],'/')>2) $base_dir=str_repeat('../',substr_count($_SERVER['REDIRECT_URL'],'/')-2);
		else $base_dir='';
		
		return '<img'.$this->setStyle($element).' width="'.$width.'" height="'.$height.'" alt="'.$element->getAltText().'" src="'.
			$base_dir.$element->getFile().'" />
		';
	}

	function base_url(){
		if(!isset($_SERVER['REDIRECT_URL'])) $base_dir='';
		else if( $_SERVER['SERVER_NAME']=='127.0.0.1')
			$base_dir=str_repeat('../',substr_count($_SERVER['REDIRECT_URL'],'/')-2);
		else if(substr_count($_SERVER['REDIRECT_URL'],'/')>2) $base_dir=str_repeat('../',substr_count($_SERVER['REDIRECT_URL'],'/')-2);
		else $base_dir='';
		
		return $base_dir;
	}
}
?>
