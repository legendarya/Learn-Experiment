<?php
require_once 'templates/profile_template.php';

class UrlController extends ProfileTemplate{
	function _contenido_pestana($usuario=0,$mensaje=0){
		$usuario=new Usuario($usuario);
		if($mensaje) $mensaje=new Mensaje($mensaje);
		
		if(!$this->usuario) redirect(base_url());
		
		$form=new vForm(array(
				new vHeader(($mensaje?'Responder mensaje de ':'Enviar mensaje a ').$usuario->nombre_usuario),
				($this->error?new vDiv(getInterface($this->error),'error'):''),
				new vInputText('titulo',getInterface('messages_title'),
					($mensaje?
						(substr($mensaje->titulo,0,3)=='RE:'?'':'RE: ').
						$mensaje->titulo
					:'')
				),
				new vInputTextArea('texto',getInterface('messages_text'))
			));
			
		$form->setSubmitText('Enviar');
		$form->setClass('form_mensaje');
		
		return new vContainer(array(
			$form,
			($mensaje?new vSection(array(
				new vHeader($mensaje->titulo),
				new vDiv(array(
					'de ',new vLink(base_url().'usuario/perfil/'.$mensaje->de->id,$mensaje->de->nombre_usuario),
					' ',$mensaje->fecha->format('H:i d/m'),
				),'sub_head'),
				new vDiv(new vPreformatedText(nl2br($mensaje->texto)),'message_text')
			),'message_info'):'')
		));
	}
	
	function _processForm($usuario=0,$mensaje=0){
		$usuario=new Usuario($usuario);
		
		if(!$this->usuario) redirect(base_url());
		
		if(!getParam('titulo')) $this->error = 'messages_error_title';
		else if(!getParam('texto')) $this->error = 'messages_error_text';
		else{
			$mensaje=$this->usuario->mensajesEnviados->add();
			$mensaje->fecha=new DateTime();
			$mensaje->para=$usuario;
			$mensaje->titulo=getParam('titulo');
			$mensaje->texto=getParam('texto');
			$mensaje->save();
			
			redirect(base_url().'mensajes/index/0/'.$mensaje->id);
		}
	}
	
	function getUsuario($usuario){
		return $this->usuario;
	}
	
	function tab(){
		return 'mensajes';
	}
	
	function _title($usuario=0,$mensaje=0){
		$usuario=new Usuario($usuario);
		
		return 'Enviar mensaje a '.$usuario->nombre_usuario;
	}
}
?>