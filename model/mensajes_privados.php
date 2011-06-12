<?php

$entity_mensaje =new entity ('mensaje');
$entity_mensaje->addField(new DateField('fecha'));
$entity_mensaje->addField(new TextField('titulo'));
$entity_mensaje->addField(new TextField('texto'));
$entity_mensaje->addField(new IntegerField('leido'));

$entity_usuario->addSet('mensajesEnviados',$entity_mensaje,'de');
$entity_usuario->addSet('mensajesRecibidos',$entity_mensaje,'para');

class Mensaje extends Persistent{
	function __construct($condition=null){
		parent::__construct('mensaje',$condition);
	}
}

class Mensajes extends PersistentList{
	function __construct(){
		$this->table='mensaje';
	}
}
?>