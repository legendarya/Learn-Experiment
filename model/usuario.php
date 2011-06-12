<?php
$entity_usuario =new entity ('usuario');
$entity_usuario->addField(new TextField('nombre'));
$entity_usuario->addField(new TextField('nombre_usuario'));
$entity_usuario->addField(new PasswordField('clave'));
$entity_usuario->addField(new TextField('facebook'));

$entity_usuario->addField(new TextField('email'));
$entity_usuario->addField(new TextField('web'));
$entity_usuario->addField(new TextField('localizacion'));
$entity_usuario->addField(new TextField('intereses'));

$entity_usuario->addField(new DateField('nacimiento'));
$entity_usuario->addField(new IntegerField('email_publico'));
$entity_usuario->addField(new IntegerField('sexo'));
$entity_usuario->addField(new IntegerField('administrador'));

class Usuario extends Persistent{
	function __construct($condition=null){
		parent::__construct('usuario',$condition);
	}
}

class Usuarios extends PersistentList{
	function __construct(){
		$this->table='usuario';
	}
}
			
?>