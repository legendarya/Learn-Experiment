<?php
require_once 'lib/facebook/facebook.php';

class ProcessTemplate{
	protected $urlParams;
	protected $usuario=null;
	protected $facebook=null;

	function __construct($urlParams){
		global $config;
	
		if($config['facebook_api'])
			$this->facebook=new Facebook($config['facebook_api'],$config['facebook_secret'],true);
	
		$this->urlParams=$urlParams;
		
		if(isset($_SESSION['id'])){
			try{
				$this->usuario = new Usuario($_SESSION['id']);
			} catch(Exception $ex){};
		}
		elseif(isset($_COOKIE['learnexperiment_name']) && isset($_COOKIE['learnexperiment_pass'])){
			$usuario=new Usuario(new EqualCondition('nombre_usuario',$_COOKIE['learnexperiment_name']));
			if($usuario->clave==md5($_COOKIE['learnexperiment_pass'])){
				$_SESSION['id']=$usuario->id;
				$this->usuario=$usuario;
			}
		}
		
		if($config['db_server']=='127.0.0.1'){
			if(isset($_SESSION['id'])){
				try{
					$this->usuario = new Usuario($_SESSION['id']);
				} catch(Exception $ex){};
			}
			//$this->usuario = new Usuario(32);
		}
		else if($config['facebook_api'] and $this->facebook->get_loggedin_user()){
			if($this->usuario and !$this->usuario->facebook){
				if($_SERVER['QUERY_STRING']!='/usuario/enlaza_cuentas/') redirect(base_url().'usuario/enlaza_cuentas/');
			}
			else{
				unset($_SESSION['id']);
				try{
					$this->usuario = new Usuario(new EqualCondition('facebook',$this->facebook->get_loggedin_user()));
				} catch(Exception $ex){
					$this->usuario = new Usuario();
					$this->usuario->facebook=$this->facebook->get_loggedin_user();
					$this->usuario->save();
				};
			}
		}
		if($this->usuario){
			$this->usuario->ultima_vez_activo=new DateTime();
			$this->usuario->save();
		}
	}
	
	function _callWithParams($method){
		return call_user_func_array(array($this, $method), $this->urlParams);
	}
	
	function esResponsable($apartado){
		if(!$this->usuario) return false;
		if($this->usuario->administrador) return true;
		while($apartado){
			if($apartado->responsables->where(new  EqualCondition('usuario',$this->usuario->id))->count()) return true;
			$apartado=$apartado->padre;
		}
		return false;
	}
	
	function responsableEjercicio($ejercicio){
		if(is_array($ejercicio)) return false;
		return (($ejercicio->director and $this->usuario and $this->usuario->id==$ejercicio->director->id) or $this->esResponsable($ejercicio->apartado));
	}
	
	function guardaEjercicioTemporal($ej,$apartado=null,$ejercicio_editado=null){
		if($ejercicio_editado){
			$ejercicio=$ejercicio_editado;
			$ejercicio->director=$this->usuario;
			$ejercicio->preguntas->deleteAll();
		}
		else{
			$ejercicio=$this->usuario->ejercicios_creados->add();
			$ejercicio->save();
		}
		
		$i=0;
		foreach($ej['preguntas'] as $preg){
			$pregunta=$ejercicio->preguntas->add();
			$pregunta->pregunta=$preg[0];
			$pregunta->respuesta=$preg[1];
			$pregunta->save();
		}
		
		// Guardamos los nuevos valores del ejercicio
		$ejercicio->titulo=$ej['titulo'];
		$ejercicio->fecha_actualizacion=new DateTime($ej['fecha_actualizacion']);
		$ejercicio->descripcion=$ej['descripcion'];
		$ejercicio->tipo=$ej['tipo'];
		$ejercicio->ordenar=$ej['ordenar'];
		if($apartado) $ejercicio->apartado=$apartado;
		$ejercicio->pronuncia_pregunta=$ej['pronuncia_pregunta'];
		$ejercicio->pronuncia_respuesta=$ej['pronuncia_respuesta'];
		$ejercicio->idioma_pregunta=$ej['idioma_pregunta'];
		$ejercicio->idioma_respuesta=$ej['idioma_respuesta'];
		$ejercicio->url=urlencode($ej['titulo']);
		$this->guardaVersion($ejercicio);
		$ejercicio->save();
		
		return $ejercicio;
	}
	
	function guardaVersion($ejercicio){
		$version=$ejercicio->versiones->add();
		
		$preguntas=array();
		foreach($ejercicio->preguntas as $pregunta)
			$preguntas[]=array($pregunta->pregunta,$pregunta->respuesta);
		
		$version->texto=array(
			'titulo'=>$ejercicio->titulo,
			'descripcion'=>$ejercicio->descripcion,
			'tipo'=>$ejercicio->tipo,
			'ordenar'=>$ejercicio->ordenar,
			'pronuncia_pregunta'=>$ejercicio->pronuncia_pregunta,
			'pronuncia_respuesta'=>$ejercicio->pronuncia_respuesta,
			'idioma_pregunta'=>$ejercicio->idioma_pregunta,
			'idioma_respuesta'=>$ejercicio->idioma_respuesta,
			'preguntas'=>$preguntas);
		
		$version->padre=$ejercicio->version;
		$version->fecha=new DateTime();
		$version->usuario=$this->usuario;
		
		$version->save();
		$ejercicio->version=$version;
	}
}
?>