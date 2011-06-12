<?php
class UrlController extends PageTemplate{
	function _contenido(){
		$form= new vContainer(array(
			new vHeader('Registro de usuario'),
			new vForm(array(
				new vDiv($this->error,'error'),
				new vInputText('user','Usuario:'),
				new vInputPassword('pass','Contraseña:'),
				new vInputPassword('passRepeat','Repite contraseña:'),
				new vSection(array(
					new vHeader('Datos opcionales'),
					new vInputText('nombre','Nombre:'),
					new vInputText('web','Página web:'),
					new vInputText('email','Email:'),
					$email_publico=new vInputOptions('email_publico','Email publico:'),
					new vDiv(array(
						$dia=new vInputOptions('dia_nacimiento','Nacimiento:',''),
						$mes=new vInputOptions('mes_nacimiento','mes: '),
						$anio=new vInputOptions('anio_nacimiento','año: ')
					),'fecha_nacimiento'),
					$sexo=new vInputOptions('sexo','Sexo:'),
					new vInputText('localizacion','Localizacion:'),
					new vInputTextArea('intereses','Tus intereses:')
				))
			))
		));
		
		$email_publico->add(0,'No');
		$email_publico->add(1,'Si');
		
		$sexo->add(0,'No indicado');
		$sexo->add(1,'Hombre');
		$sexo->add(2,'Mujer');
		
		$dia->add(0,'Escoge');
		for($i=1;$i<32;$i++)
			$dia->add($i,$i);
			
		$meses=array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
		$mes->add(0,'Escoge');
		for($i=1;$i<13;$i++)
			$mes->add($i,$meses[$i-1]);
			
		$anio->add(0,'Escoge');
		for($i=date('Y');$i>1900;$i--)
			$anio->add($i,$i);
		
		return $form;
	}
	
	function _processForm(){
		$usuarios=new Usuarios();
		if(!getParam('user')) $this->error = 'El campo usuario es obligatorio';
		else if($usuarios->where(new EqualCondition('nombre_usuario',getParam('user')))->count())
			$this->error = 'Ya existe ese usuario';
		else if(getParam('email') and !$this->checkEmail(getParam('email'))) $this->error = 'El email indicado es incorrecto';
		else if(getParam('pass') != getParam('passRepeat')) $this->error = 'Los campos contraseña y repetir contraseña no coinciden.';
		else{
			$usuario = new Usuario();
			$usuario->nombre = getParam('nombre');
			$usuario->nombre_usuario = getParam('user');
			$usuario->clave = getParam('pass');
			$usuario->fecha_registro = new DateTime();
			
			$usuario->email = getParam('email');
			$usuario->email_publico = getParam('email_publico');
			$usuario->web = getParam('web');
			$usuario->sexo = getParam('sexo');
			$usuario->localizacion = getParam('localizacion');
			$usuario->intereses = getParam('intereses');
			$usuario->nacimiento = new DateTime((0+getParam('anio_nacimiento')).'-'.(0+getParam('mes_nacimiento')).'-'.(getParam('dia_nacimiento')+0));
			$usuario->save();
			
			$preferencia=$usuario->preferencias->add();
			$preferencia->save();
			
			$_SESSION['id'] = $usuario->id;
			
			$this->usuario=$usuario;
			if(isset($_SESSION['ejercicios']))
			foreach($_SESSION['ejercicios'] as $ejercicio)
				$this->guardaEjercicioTemporal($ejercicio);
			unset($_SESSION['ejercicios']);
			
			redirect(base_url());
		}
	}
	
	function checkEmail($email) {
		if(preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $email)) return true;
		else return false;
	}
	
	function _title(){
		return 'Registro de usuario';
	}
}
?>