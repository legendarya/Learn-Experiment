<?php
class UrlController extends ProcessTemplate{

	function execute($apartado='',$ejercicio=''){
		$apartado=new Apartado($apartado);
		if($this->esResponsable($apartado)){
			$copia_ejercicio=new Ejercicio($ejercicio);
			$ejercicio=new Ejercicio($ejercicio);
			$copia_ejercicio->id=0;
			$copia_ejercicio->apartado=$apartado;
			$copia_ejercicio->original=$ejercicio;
			$copia_ejercicio->save();
			
			foreach($ejercicio->preguntas as $pregunta){
				$copia_pregunta=new PreguntaLeccion($pregunta->id);
				$copia_pregunta->leccion=$copia_ejercicio;
				$copia_pregunta->id=0;
				$copia_pregunta->save();
			}
		}
		
		redirect(base_url().'materias/'.str2url($apartado->nombre).'/'.$apartado->id);
	}
}
?>