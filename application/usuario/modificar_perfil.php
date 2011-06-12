<?php
require_once 'templates/profile_template.php';

class UrlController extends ProfileTemplate{
	function _contenido_pestana(){
		$form= new vContainer(array(
			new vHeader('Modificar perfil'),
			new vForm(array(
				new vDiv($this->error,'error'),
				new vInputText('nombre','Nombre:',$this->usuario->nombre),
				new vInputText('web','Página web:',$this->usuario->web),
				new vInputText('email','Email:',$this->usuario->email),
				$email_publico=new vInputOptions('email_publico','Email publico:',$this->usuario->email_publico),
				new vDiv(array(
					$dia=new vInputOptions('dia_nacimiento','Nacimiento:',$this->usuario->nacimiento->format('Y')=='-0001'?0:$this->usuario->nacimiento->format('d')),
					$mes=new vInputOptions('mes_nacimiento','mes: ',$this->usuario->nacimiento->format('Y')=='-0001'?0:$this->usuario->nacimiento->format('m')),
					$anio=new vInputOptions('anio_nacimiento','año: ',$this->usuario->nacimiento->format('Y')=='-0001'?0:$this->usuario->nacimiento->format('Y'))
				),'fecha_nacimiento'),
				$sexo=new vInputOptions('sexo','Sexo:',$this->usuario->sexo),
				new vInputText('localizacion','Localizacion:',$this->usuario->localizacion),
				new vInputTextArea('intereses','Tus intereses:',$this->usuario->intereses)
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
		if(getParam('email') and !$this->checkEmail(getParam('email'))) $this->error = 'El email indicado es incorrecto';
		else if($this->usuario){
			$this->usuario->nombre = getParam('nombre');
			$this->usuario->email = getParam('email');
			$this->usuario->email_publico = getParam('email_publico');
			$this->usuario->web = getParam('web');
			$this->usuario->sexo = getParam('sexo');
			$this->usuario->localizacion = getParam('localizacion');
			$this->usuario->intereses = getParam('intereses');
			if(!getParam('anio_nacimiento')) $this->usuario->nacimiento = new DateTime("0000-00-00");
			else $this->usuario->nacimiento = new DateTime((0+getParam('anio_nacimiento')).'-'.(0+getParam('mes_nacimiento')).'-'.(0+getParam('dia_nacimiento')));
			
			$this->usuario->save();
			redirect(base_url().'usuario/perfil/');
		}
	}
	
	function checkEmail($email) {
		if(preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $email)) return true;
		else return false;
	}
	
	function tab(){
		return 'perfil';
	}
	
	function _title(){
		return 'Modificar perfil';
	}
}
?>