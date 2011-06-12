<?php
class UrlController extends ProcessTemplate{
	
	function execute($version=null){
		$version=new Version($version);
		if(!$this->esResponsable($version->ejercicio->apartado)) redirect(base_url());
		$version->ejercicio->titulo=$version->texto['titulo'];
		$version->ejercicio->descripcion=$version->texto['descripcion'];
		$version->ejercicio->tipo=$version->texto['tipo'];
		
		$version->ejercicio->preguntas->deleteAll();
		foreach($version->texto['preguntas'] as $pregunta){
			$preg=$version->ejercicio->preguntas->add();
			$preg->pregunta=$pregunta[0];
			$preg->respuesta=$pregunta[1];
			$preg->save();
		}
		
		$version->ejercicio->version=$version->id;
		$version->ejercicio->save();
		
		redirect(base_url().'aprender/'.$version->ejercicio->url.'/'.$version->ejercicio->id);
	}
}
?>