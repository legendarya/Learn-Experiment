<?php
class UrlController extends PageTemplate{
	function _contenido(){
		$img_captcha=new vImage('usuario/captcha');
		$img_captcha->setWidth(140);
		$img_captcha->setHeight(40);
		
		$_POST['captcha']='';
	
		$form= new vForm(array(
				new vPreformatedText('<legend><span class="registry">'.getInterface('signin_title').'</span></legend>'),
				($this->error?new vDiv(getInterface($this->error),'error'):''),
				new vInputText('user',getInterface('signin_user').':'),
				new vInputPassword('pass',getInterface('signin_password').':'),
				new vInputPassword('passRepeat',getInterface('signin_repeat_password').':'),
				/*$img_captcha,
				new vInputText('captcha',getInterface('signin_captcha').':'),*/
			));
		
		$form->setSubmitText(getInterface('signin_button'));
		
		return new vDiv($form,'genericform');
	}
	
	function _processForm(){
		$usuarios=new Usuarios();
		if(!getParam('user')) $this->error = 'signin_error_fill_username';
		else if($usuarios->nombreIs(getParam('user'))->count())
			$this->error = 'signin_error_user_exists';
		else if(getParam('pass') != getParam('passRepeat')) $this->error = 'signin_error_wrong_passwords';
		else if(!getParam('pass')) $this->error='signin_error_fill_password';
		//else if(getParam('captcha')!=$_SESSION['captcha'])
		//	$this->error = 'signin_error_captcha';
		else{
			$usuario = new Usuario();
			$usuario->nombre = getParam('user');
			$usuario->clave = getParam('pass');
			$usuario->save();
			
			$_SESSION['id'] = $usuario->id;
			redirect(base_url());
		}
	}
	
	function _title(){
		return getInterface('signin_title');
	}
}
?>