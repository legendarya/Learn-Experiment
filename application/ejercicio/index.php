<?php
require_once 'templates/profile_template.php';

class UrlController extends ProfileTemplate{
	function _contenido_pestana($usuario=0){
		if($usuario) $usuario=new Usuario($usuario);
		else $usuario=$this->usuario;
	
		$tabla_ejercicios=new vTable(array('Ejercicio','Fecha','Preguntas','Descripción'));
		
		if($usuario){
			foreach($usuario->ejercicios_creados->orderBy('fecha_actualizacion',false) as $i=>$ejercicio)
				$tabla_ejercicios->add(new vRow(array(
					new vLink(base_url().'aprender/'.str2url($ejercicio->titulo).'/'.$ejercicio->id,$ejercicio->titulo),
					$ejercicio->fecha_actualizacion->format('d/m H:i'),
					$ejercicio->preguntas->count(),
					$ejercicio->descripcion
				)));
		}
		else if(isset($_SESSION['ejercicios']))
		foreach($_SESSION['ejercicios'] as $i=>$ejercicio){
			$fecha_actualizacion=new DateTime($ejercicio['fecha_actualizacion']);
			$tabla_ejercicios->add(new vRow(array(
				new vLink(base_url().'aprender/'.str2url($ejercicio['titulo']).'/0/'.($i+1),$ejercicio['titulo']),
				$fecha_actualizacion->format('d/m H:i'),
				count($ejercicio['preguntas']),
				$ejercicio['descripcion']
			)));
		}
			
	
		return new vContainer(array(
			($usuario->id==$this->usuario->id?new vLink(base_url().'ejercicio/crear_abierto/','Crear nuevo ejercicio','','buttonLink'):''),
			$tabla_ejercicios
		));
	}
	
	function tab(){
		return 'ejercicios';
	}
	
	function _title(){
		return 'Ejercicios';
	}
}
?>