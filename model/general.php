<?php
require_once 'usuario.php';
require_once 'foro.php';
require_once 'mensajes_privados.php';

$entity_usuario->addField(new IntegerField('administrador'));
$entity_usuario->addField(new DateField('fecha_registro'));
$entity_usuario->addField(new DateField('ultima_vez_activo'));


$entity_preferencias=new entity ('aprender_preferencias');
$entity_preferencias->addField(new IntegerField('enter'));
$entity_preferencias->addField(new IntegerField('ignorar_signos'));
$entity_usuario->addSet('preferencias',$entity_preferencias,'usuario');

class Preferencia extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_preferencias',$condition);
	}
}

$entity_articulo=new entity ('aprender_articulo');
$entity_articulo->addField(new TextField('titulo'));
$entity_articulo->addField(new TextField('dir'));
$entity_articulo->addField(new TextField('texto'));
$entity_articulo->addField(new IntegerField('ocultar_ejercicio'));

$entity_articulo->addSet('articulos',$entity_articulo,'padre');

class Articulo extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_articulo',$condition);
	}
}


$entity_historial=new entity ('aprender_historial');
$entity_historial->addField(new IntegerField('estado'));
$entity_historial->addField(new DateField('fecha'));
$entity_historial->addField(new StructField('texto'));
$entity_historial->addField(new TextField('motivo'));

$entity_historial->addSet('subversiones',$entity_historial,'padre');
$entity_usuario->addSet('subversiones',$entity_historial,'usuario');

class Version extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_historial',$condition);
	}
}

class Historial extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_historial',$condition);
	}
}

class Versiones extends PersistentList{
	function __construct(){
		$this->table='aprender_historial';}}


$entity_teclado=new entity ('aprender_teclado');
$entity_teclado->addField(new TextField('nombre'));
$entity_teclado->addField(new TextField('teclado'));

class Teclado extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_teclado',$condition);
	}
}

class Teclados extends PersistentList{
	function __construct(){
		$this->table='aprender_teclado';}}

global $entity_apartado_curso;
$entity_apartado_curso=new entity ('aprender_apartado_curso');
$entity_apartado_curso->addField(new TextField('nombre'));
$entity_apartado_curso->addField(new TextField('resumen'));
$entity_apartado_curso->addField(new TextField('descripcion'));
$entity_apartado_curso->addList('subapartados',$entity_apartado_curso,'padre');
$entity_apartado_curso->addField(new DateField('fecha_actualizacion'));
$entity_apartado_curso->addField(new IntegerField('curso'));

class Apartado extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_apartado_curso',$condition);
	}
}

class Apartados extends PersistentList{
	function __construct(){
		$this->table='aprender_apartado_curso';}}

$entity_responsable_apartado=new entity('responsable_apartado');
$entity_apartado_curso->addSet('responsables',$entity_responsable_apartado,'apartado');
$entity_usuario->addSet('apartados_responsable',$entity_responsable_apartado,'usuario');
		
$entity_tabla_equivalencia=new entity ('aprender_tabla_equivalencia');
$entity_tabla_equivalencia->addField(new TextField('titulo'));
$entity_tabla_equivalencia->addField(new TextField('tabla'));

$entity_apartado_curso->addSet('tablas_equivalencia',$entity_tabla_equivalencia,'apartado');

class TablaEquivalencia extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_tabla_equivalencia',$condition);
	}
}

class TablasEquivalencia extends PersistentList{
	function __construct(){
		$this->table='aprender_tabla_equivalencia';}}

		

$entity_idioma=new entity ('idioma');
$entity_idioma->addField(new TextField('codigo'));
$entity_idioma->addField(new TextField('nombre'));

class Idioma extends Persistent{
	function __construct($condition=null){
		parent::__construct('idioma',$condition);
	}
}

class Idiomas extends PersistentList{
	function __construct(){
		$this->table='idioma';
	}
}
		
$entity_ejercicio=new entity ('aprender_leccion');
$entity_ejercicio->addField(new TextField('titulo'));
$entity_ejercicio->addField(new TextField('descripcion'));
$entity_ejercicio->addField(new TextField('url'));
$entity_ejercicio->addField(new IntegerField('nivel'));
$entity_ejercicio->addField(new IntegerField('tipo'));
$entity_ejercicio->addField(new IntegerField('fecha_publicacion'));
$entity_ejercicio->addField(new DateField('fecha_actualizacion'));
$entity_ejercicio->addField(new TextField('video'));
$entity_ejercicio->addField(new TextField('contenido_externo'));
$entity_ejercicio->addField(new IntegerField('ordenar'));
$entity_ejercicio->addField(new IntegerField('pronuncia_pregunta'));
$entity_ejercicio->addField(new IntegerField('pronuncia_respuesta'));

$entity_apartado_curso->addList('ejercicios',$entity_ejercicio,'apartado');
$entity_teclado->addSet('ejercicios',$entity_ejercicio,'teclado');
$entity_usuario->addSet('ejercicios_creados',$entity_ejercicio,'director');
$entity_ejercicio->addSet('copias',$entity_ejercicio,'original');

$entity_ejercicio->addSet('articulos',$entity_articulo,'ejercicio');
$entity_ejercicio->addSet('versiones',$entity_historial,'ejercicio');

$entity_idioma->addSet('pronuncia_preguntas',$entity_ejercicio,'idioma_pregunta');
$entity_idioma->addSet('pronuncia_respuestas',$entity_ejercicio,'idioma_respuesta');

$entity_ejercicio->addField(new ReferenceField('version',$entity_historial));

class Leccion extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_leccion',$condition);
	}
}

class Ejercicio extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_leccion',$condition);
	}
}

class Ejercicios extends PersistentList{
	function __construct(){
		$this->table='aprender_leccion';
	}
}

$entity_pregunta=new entity ('aprender_pregunta');
$entity_pregunta->addField(new TextField('pregunta'));
$entity_pregunta->addField(new TextField('respuesta'));
$entity_pregunta->addField(new StructField('opciones'));

$entity_ejercicio->addSet('preguntas',$entity_pregunta,'leccion');

class PreguntaLeccion extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_pregunta',$condition);
	}
}

class Preguntas extends PersistentList{
	function __construct(){
		$this->table='aprender_pregunta';
	}
}

$entity_estado_leccion=new entity ('aprender_leccion_estado');
$entity_estado_leccion->addField(new FloatField('mejor_porcentaje'));
$entity_estado_leccion->addField(new FloatField('ultimo_porcentaje'));
$entity_estado_leccion->addField(new IntegerField('mejor_fallos'));
$entity_estado_leccion->addField(new IntegerField('ultimo_fallos'));
$entity_estado_leccion->addField(new IntegerField('mejor_tiempo'));
$entity_estado_leccion->addField(new IntegerField('ultimo_tiempo'));
$entity_estado_leccion->addField(new DateField('repaso_anterior'));
$entity_estado_leccion->addField(new StructField('preguntas'));

$entity_articulo->addSet('usuarios',$entity_estado_leccion,'articulo');
$entity_ejercicio->addSet('usuarios',$entity_estado_leccion,'leccion');
$entity_usuario->addSet('ejercicios',$entity_estado_leccion,'usuario');

class EstadoLeccion extends Persistent{
	function __construct($condition=null){
		parent::__construct('aprender_leccion_estado',$condition);
	}
}

$entity_noticia=new entity ('noticia');
$entity_noticia->addField(new TextField('titulo'));
$entity_noticia->addField(new TextField('texto'));
$entity_noticia->addField(new DateField('fecha'));

class Noticia extends Persistent{
	function __construct($condition=null){
		parent::__construct('noticia',$condition);
	}
}

class Noticias extends PersistentList{
	function __construct(){
		$this->table='noticia';
	}
}
?>