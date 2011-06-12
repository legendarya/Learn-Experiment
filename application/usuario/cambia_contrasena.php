<?php
require_once 'templates/profile_template.php';

class UrlController extends ProfileTemplate{
	function _contenido_pestana(){
		$form= new vContainer(array(
			new vHeader('Cambiar contraseña'),
			new vForm(array(
				new vDiv($this->error,'error'),
				new vInputPassword('actual','Contraseña actual:'),
				new vInputPassword('nueva', 'Nueva contraseña: '),
				new vInputPassword('repite','Repite contraseña:')
			))
		));
		
		return $form;
	}
	
	function _processForm(){
		if(md5(getParam('actual'))!=$this->usuario->clave) $this->error = 'La contraseña actual no es correcta';
		else if(getParam('nueva')!=getParam('repite')) $this->error = 'La contraseña nueva no coincide al repetirla';
		else if($this->usuario){
			$this->usuario->clave = getParam('nueva');
			$this->usuario->save();
			redirect(base_url().'usuario/perfil/');
		}
	}
	
	function tab(){
		return 'perfil';
	}
	
	function _title(){
		return 'Cambiar contraseña';
	}
}
?>