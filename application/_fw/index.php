<?php
require_once 'templates/framework_template.php';

class UrlController extends FW_Template{
	function _contenido(){
		//$this->page->addScript('js/utilidades.js');
	
		$result = new vContainer();
		
		$result->add(new vHeader('Identificación'));
		$result->add($frmLogin = new vForm(array(
				($this->error?new vDiv($this->error,'error'):''),
				new vInputText('user','Usuario:'),
				new vInputPassword('pass','Contraseña:')
			))
		);
		
		$frmLogin->setSubmitText('Aceptar');
		
		return $result;
	}
	
	function _processForm(){
		if(!getParam('user')) $this->error='Debe rellenar el nombre de usuario';
		else if(!getParam('pass')) $this->error='Debe rellenar la contraseña';
		else{
			$usuarios=new FW_Users();
			$usuario=$usuarios->where(new EqualCondition('name',getParam('user')))->getFirst();
			
			if(!$usuario) $this->error = 'No existe el usuario en la base de datos';
			else if($usuario->password!=md5(getParam('pass'))) $this->error = 'La contraseña no es correcta';
			else{
				$_SESSION['FW_id'] = $usuario->id;
				
				redirect(base_url().'_fw/errors/');
			};
		}
	}
	
	function _title(){
		return 'Login';
	}
}
?>