<?php
require_once 'templates/page_template.php';

class ProfileTemplate extends PageTemplate{
	
	function _contenido($usuario=null){
		$usuario=$this->getUsuario($usuario);
	
		$tabs=new vMenu('tabs');
		$tabs->add('Perfil',base_url().'usuario/perfil/'.$usuario->id,$this->tab()=='perfil');
		if($this->pagina_personal($usuario)) $tabs->add('Mensajes',base_url().'mensajes/',$this->tab()=='mensajes');
		$tabs->add('Ejercicios',base_url().'ejercicio/index/'.$usuario->id,$this->tab()=='ejercicios');
	
		return new vContainer(array(
			$tabs,
			new vDiv(array(
				new vDiv('','topTabs'),
				new vDiv(
					$this->_callWithParams('_contenido_pestana')
				,'contenidoTab show'),
				new vDiv('','bottomTabs')
			),'tabsBody')
		));
	}
	
	function tab(){
		return '';
	}
	
	function getUsuario($usuario){
		if($usuario) return new Usuario($usuario);
		else return $this->usuario;
	}
	
	function pagina_personal($usuario){
		return $usuario and $usuario->id==$this->usuario->id;
	}
}
?>