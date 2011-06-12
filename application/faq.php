<?php
class UrlController extends PageTemplate{
	function _contenido(){
		$this->page->addScript('js/behaviour.js');
		$this->page->addScript('js/ejercicio.js');
		$this->page->addScript('js/ejercicios_adicionales.js');
		$this->page->addScript('js/interfaz.js');
		$this->page->addScript('js/utilidades.js');
		
		$result = new vContainer();
		
		$result->add(new vHeader('Preguntas frecuentes'));
		$result->add($contenido = new vSection());
		

		
		return $result;
	}
	
	function _title(){
		return 'Preguntas frecuentes';
	}
	
	function comentarios_key(){
		return 'learnexperimentApartadoFaq';
	}
}
?>