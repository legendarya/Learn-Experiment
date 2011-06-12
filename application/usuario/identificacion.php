<?php
class UrlController extends PageTemplate{
	function _contenido(){
		//$this->page->addScript('js/utilidades.js');
	
		$result = new vDiv('','genericform');
		
		$result->add($frmLogin = new vForm(array(
				new vPreformatedText('<legend><span class="sign">'.getInterface('login_title').'</span></legend>'),
				($this->error?new vDiv(getInterface($this->error),'error'):''),
				new vInputText('user',getInterface('login_user').':'),
				new vInputPassword('pass',getInterface('login_password').':'),
				new vInputCheckBox('recordar',getInterface('login_rememberme')),
				new vInputHidden('url',getParam('url'))
			))
		);
		
		$frmLogin->setSubmitText(getInterface('login_button'));
		
		return $result;
	}
	
	function _processForm(){
		if(!getParam('user')) $this->error='login_error_fill_username';
		else if(!getParam('pass')) $this->error='login_error_fill_password';
		else{
			$usuarios=new Usuarios();
			$usuario=$usuarios->nombreIs(getParam('user'))->getFirst();
			
			if(!$usuario) $this->error = 'login_error_user_not_exist';
			else if($usuario->clave!=md5(getParam('pass'))) $this->error = 'login_error_wrong_password';
			else{
				$_SESSION['id'] = $usuario->id;
				$url=getParam('url');
				if($url) $url=substr($url,1);
				
				define("SESSION_TIME",60*60*24*100);

				 if(getParam('recordar')=='on'){
				   setcookie("ideasp2p_name", $usuario->nombre, time()+SESSION_TIME, "/");
				   setcookie("ideasp2p_pass", getParam('pass'), time()+SESSION_TIME, "/");
				}
				
				redirect(base_url().(($_SERVER['SERVER_NAME']=='127.0.0.1' and $url)?
					implode('/',array_slice(explode('/',$url),1)):
					$url));
			};
		}
	}
	
	function _title(){
		return getInterface('login_title');
	}
	
	function comentarios_key(){
		return 'learnexperimentApartadoIdentificacion';
	}
}
?>