<?php
class UrlController extends PageTemplate{
	function _contenido(){
		//$this->page->addScript('js/utilidades.js');
	
		$result = new vContainer();
		
		$result->add(new vHeader('Identificación'));
		$result->add($frmLogin = new vForm(array(
				($this->error?new vDiv($this->error,'error'):''),
				new vInputText('user','Usuario:'),
				new vInputPassword('pass','Contraseña:'),
				new vInputCheckBox('recordar','Recuerdame'),
				new vInputHidden('url',getParam('url'))
			))
		);
		
		$frmLogin->setSubmitText('Aceptar');
		
		return $result;
	}
	
	function _processForm(){
		if(!getParam('user')) $this->error='Debe rellenar el nombre de usuario';
		else if(!getParam('pass')) $this->error='Debe rellenar la contraseña';
		else{
			$usuarios=new Usuarios();
			$usuario=$usuarios->where(new EqualCondition('nombre_usuario',getParam('user')))->getFirst();
			
			if(!$usuario) $this->error = 'No existe el usuario en la base de datos';
			else if($usuario->clave!=md5(getParam('pass'))) $this->error = 'La contraseña no es correcta';
			else{
				$_SESSION['id'] = $usuario->id;
				$url=getParam('url');
				if($url) $url=substr($url,1);
				
				define("SESSION_TIME",60*60*24*100);

				 if(getParam('recordar')=='on'){
				   setcookie("learnexperiment_name", $usuario->nombre_usuario, time()+SESSION_TIME, "/");
				   setcookie("learnexperiment_pass", getParam('pass'), time()+SESSION_TIME, "/");
				}
				
				$this->usuario=$usuario;
				if(isset($_SESSION['ejercicios']))
				foreach($_SESSION['ejercicios'] as $ejercicio)
					$this->guardaEjercicioTemporal($ejercicio);
				unset($_SESSION['ejercicios']);
				
				redirect(base_url().(($_SERVER['SERVER_NAME']=='127.0.0.1' and $url)?
					implode('/',array_slice(explode('/',$url),1)):
					$url));
			};
		}
	}
	
	function _title(){
		return 'Identificación';
	}
	
	function comentarios_key(){
		return 'learnexperimentApartadoIdentificacion';
	}
}
?>