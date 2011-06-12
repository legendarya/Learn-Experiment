<?php
require_once 'templates/profile_template.php';

class UrlController extends ProfileTemplate{
	function _contenido_pestana($usuario=null){
		if(!$usuario) $usuario=$this->usuario;
		else $usuario=new Usuario($usuario);
	
		$lista_opciones=new vMenu();
		if($usuario->id!=$this->usuario->id) $lista_opciones->add('Enviar mensaje',base_url().'mensajes/escribir/'.$usuario->id);
		else{
			$lista_opciones->add('Modificar perfil',base_url().'usuario/modificar_perfil/');
			$lista_opciones->add('Cambiar contraseña',base_url().'usuario/cambia_contrasena/');
		}
	
		$info=new vTable();
		if($usuario->nombre) $info->add(array('Nombre',$usuario->nombre));
		if($usuario->fecha_registro->format('Y')!='-0001') $info->add(array('Registrado',$usuario->fecha_registro->format('d/m/Y')));
		if($usuario->ultima_vez_activo->format('Y')!='-0001') $info->add(array('Última vez activo',$usuario->ultima_vez_activo->format('d/m/Y')));
		if($usuario->web) $info->add(array('Web',new vLink($usuario->web,$usuario->web)));
		if($this->usuario and $usuario->email_publico and $usuario->email) $info->add(array('Email',new vLink('mail:'.$usuario->email,$usuario->email)));
		if($usuario->sexo) $info->add(array('Sexo',$usuario->sexo==1?'Hombre':'Mujer'));
		if($usuario->nacimiento->format('Y')!='-0001') $info->add(array('Fecha de nacimiento',$usuario->nacimiento->format('d/m/Y')));
		if($usuario->localizacion) $info->add(array('Localización',$usuario->localizacion));
		if($usuario->intereses) $info->add(array('Intereses',new vPreformatedText(nl2br(htmlspecialchars($usuario->intereses)))));
		
		$info->setClass('info_perfil');
	
		return new vContainer(array(
			new vSection(array(
				new vDiv(array(
					new vHeader('Opciones'),
					new vDiv($lista_opciones,'comments')
				),'sidebox')
			),'','sidebar'),
			new vHeader('Perfil de '.$usuario->nombre_usuario),
			$info
		));
	}
	
	function tab(){
		return 'perfil';
	}
	
	function _title($usuario=null){
		if(!$usuario) $usuario=$this->usuario;
		else $usuario=new Usuario($usuario);
		
		return 'Perfil de '.$usuario->nombre_usuario;
	}
}
?>