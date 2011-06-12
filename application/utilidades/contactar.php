<?php
class UrlController extends PageTemplate{
	function _contenido(){
		$contact_form = new vForm(array(
			($this->error?new vDiv($this->error,'error'):''),
			new vInputText('email','Email (opcional):'),
			new vInputTextArea('mensaje','Mensaje:')
		));
		
		$contact_form->setSubmitText('Enviar');
		
		return new vContainer(array(
			new vHeader('Contacta con nosotros'),
			new vDiv(array('Learnexperiment es un proyecto abierto, que se nutre con la iniciativa de los usuarios. Así que no dudes en contactar
			con nosotros. Puedes hacerlo a través del correo electrónico ',
			new vLink('mailto:contacto@learnexperiment.com','contacto@learnexperiment.com'),', en nuestra ',
			new vLink('https://groups.google.com/group/learn-experiment','lista de correo en google'),', o usando el siguiente formulario: ')),
		(isset($this->email_enviado)?new vSection(new vHeader('¡Gracias por tu mensaje! Esperamos poder contestarte en breve.')):$contact_form)
		));
	}
	
	function _processForm(){
		if(!getParam('mensaje')) $this->error='Debes escribir un mensaje';
		else if(getParam('email') and !$this->is_email(getParam('email'))) $this->error='El email que has escrito no parece correcto';
		else{
		include_once("lib/mail/class.phpmailer.php");
		
		$mail             = new PHPMailer();
		$mail->From       = getParam('email')?getParam('email'):'contacto@learnexperiment.com';
		$mail->FromName   = getParam('email')?getParam('email'):'Usuario anonimo';
		$mail->Subject    = utf8_decode("Mensaje de contacto");
		$mail->AltBody    = getParam('mensaje');
		$mail->MsgHTML(getParam('mensaje'));
		$mail->WordWrap   = 50; // set word wrap
		
		$mail->AddAddress('jaime@legendarya.com',"");

		$mail->Send();
		
			$this->email_enviado=true;
		}
	}
	
	 function is_email($email){
        return preg_match('/^[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+(?:\.[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+)*\@[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+(?:\.[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+)+$/i', $email);
     }  
	
	function _title(){
		return 'Contacta';
	}
}
?>