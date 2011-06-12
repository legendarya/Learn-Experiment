<?php
class UrlController extends PageTemplate{
	function _contenido(){
		if(!$this->usuario) redirect(base_url());
		
		return new vContainer(array(
			new vHeader('Enlazar cuentas'),
			new vDiv('Estas identificado con la cuenta vieja "'.$this->usuario->nombre_usuario.'" de Learnexperiment y a la vez intentas identificarte con Facebook ¿Quieres enlazar ambas cuentas?'),
			new vForm(array(
				new vInputHidden('link','1'),
				new vInputCheckBox('enlaza_facebook','Enlazar cuenta vieja con Facebook'),
			))
		));
	}
	
	function _processForm(){
		global $config;
		if($this->usuario and !$this->usuario->facebook and $config['facebook_api'] and $this->facebook->get_loggedin_user() and isset($_POST['enlaza_facebook'])){
			
			try{
				$usuario = new Usuario(new EqualCondition('facebook',$this->facebook->get_loggedin_user()));
				$usuario->delete();
			} catch(Exception $ex){};
			$this->usuario->facebook=$this->facebook->get_loggedin_user();
			$this->usuario->save();
		}
		unset($_SESSION['id']);
		redirect(base_url());
	}
	
	function _title(){
		return 'Enlazar cuentas';
	}
}
?>