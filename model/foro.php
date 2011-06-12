<?php

$entity_foro=new entity ('foro');
$entity_foro->addField(new TextField('nombre'));
$entity_foro->addField(new TextField('descripcion'));
$entity_foro->addField(new IntegerField('orden'));
$entity_foro->addField(new IntegerField('noticias'));

class Foro extends Persistent{
	function __construct($condition=null){
		parent::__construct('foro',$condition);
	}
}

class Foros extends PersistentList{
	function __construct(){
		$this->table='foro';
	}
}

$entity_tema=new entity ('foro_tema');
$entity_tema->addField(new TextField('titulo'));
$entity_tema->addField(new DateField('fecha'));
$entity_tema->addField(new DateField('fecha_creacion'));

$entity_foro->addSet('temas',$entity_tema,'foro');

class Tema extends Persistent{
	function __construct($condition=null){
		parent::__construct('foro_tema',$condition);
	}
}

class Temas extends PersistentList{
	function __construct(){
		$this->table='foro_tema';
	}
}

$entity_mensaje=new entity ('foro_mensaje');
$entity_mensaje->addField(new TextField('mensaje'));
$entity_mensaje->addField(new TextField('nombre'));
$entity_mensaje->addField(new DateField('fecha'));

$entity_usuario->addSet('mensajes',$entity_mensaje,'usuario');
$entity_tema->addSet('mensajes',$entity_mensaje,'tema');

$entity_tema->addField(new ReferenceField('ultimoMensaje',$entity_mensaje));
$entity_foro->addField(new ReferenceField('ultimoMensaje',$entity_mensaje));

class MensajeForo extends Persistent{
	function __construct($condition=null){
		parent::__construct('foro_mensaje',$condition);
	}
}

class MensajesForo extends PersistentList{
	function __construct(){
		$this->table='foro_mensaje';
	}
}

$entity_ultima_lectura=new entity ('foro_ultima_lectura');
$entity_ultima_lectura->addField(new DateField('fecha'));

$entity_usuario->addSet('temasLeidos',$entity_ultima_lectura,'usuario');
$entity_tema->addSet('temasLeidos',$entity_ultima_lectura,'tema');

class LecturaTema extends Persistent{
	function __construct($condition=null){
		parent::__construct('foro_ultima_lectura',$condition);
	}
}

class LecturasTema extends PersistentList{
	function __construct(){
		$this->table='foro_ultima_lectura';
	}
}
?>