<?php
class UrlController extends ProcessTemplate{
	
	function execute($url_ejercicio=null,$id_ejercicio=null,$indiceEjercicio=0){
		if($indiceEjercicio){
			$ejercicio=$_SESSION['ejercicios'][$indiceEjercicio-1];
			
			$preguntas=array();
			foreach($ejercicio['preguntas'] as $pregunta){
				$preguntas[]= '<pregunta id="0" pregunta="'.str_replace('"','&quot;', $pregunta[0]).'" 
					respuesta="'.str_replace('"','&quot;', ($pregunta[1])).'" tiempo="0" nivel="0"/>';
			}
		
			//cabecera del xml de respuesta
			header('Content-Type: text/xml');
			echo '<?xml version="1.0" encoding="UTF-8"?>
			<leccion pronuncia_pregunta="'.$ejercicio['pronuncia_pregunta'].'" pronuncia_respuesta="'.$ejercicio['pronuncia_respuesta'].
			'" idioma_pregunta="'.$ejercicio['idioma_pregunta'].'" idioma_respuesta="'.$ejercicio['idioma_respuesta'].
			'" ordenar="0" video="0" contenido_externo="" titulo="'.
				htmlspecialchars($ejercicio['titulo']).'" tipo="'.$ejercicio['tipo'].'" fallos="0" tiempo="0" siguiente="'.
				'"><desc>'.nl2br(htmlspecialchars($ejercicio['descripcion'])).'</desc>'.
			implode('',$preguntas).'</leccion>';
		
			exit;
		}
	
		$leccion=new Ejercicio($id_ejercicio);
		if(!isset($_SESSION['estados'])) $_SESSION['estados']=array();
		
		$estado_preguntas=null;
		if($this->usuario and $estado=$this->usuario->ejercicios->where(new EqualCondition('leccion',$id_ejercicio))->getFirst()){
			$estado_preguntas=$estado->preguntas;
			$fallos=$estado->ultimo_fallos;
			$tiempo=$estado->ultimo_tiempo;
		}
		else if(isset($_SESSION['estados'][$id_ejercicio])){
			$estado_preguntas=$_SESSION['estados'][$id_ejercicio]['preguntas'];
			$fallos=$_SESSION['estados'][$id_ejercicio]['fallos'];
			$tiempo=$_SESSION['estados'][$id_ejercicio]['tiempo'];
		}
		else{
			$fallos=0;
			$tiempo=0;
		}
		
		$preguntas=array();
		foreach($leccion->preguntas->orderBy('id') as $pregunta){
			if($estado_preguntas and isset($estado_preguntas[$pregunta->id])) $estado_pregunta=$estado_preguntas[$pregunta->id];
			else $estado_pregunta=array('tiempo'=>0,'nivel'=>-1);
		
			$preguntas[]= '<pregunta id="'.$pregunta->id.'" pregunta="'.str_replace('"','&quot;', $pregunta->pregunta).'" 
				respuesta="'.str_replace('"','&quot;', ($pregunta->respuesta)).'" tiempo="'.$estado_pregunta['tiempo'].'" nivel="'.$estado_pregunta['nivel'].'"/>';
		}
		
		if(!$leccion->ordenar) shuffle ($preguntas);
		
		if($leccion->teclado)
			$teclado='<teclado>'.htmlspecialchars($leccion->teclado->teclado).'</teclado>';
		else $teclado='';
		
		//cabecera del xml de respuesta
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>
		<leccion pronuncia_pregunta="'.$leccion->pronuncia_pregunta.'" pronuncia_respuesta="'.$leccion->pronuncia_respuesta.
			'" idioma_pregunta="'.($leccion->idioma_pregunta?$leccion->idioma_pregunta->id:'').'" idioma_respuesta="'.($leccion->idioma_respuesta?$leccion->idioma_respuesta->id:'').
			'" ordenar="'.$leccion->ordenar.'" video="'.$leccion->video.'" contenido_externo="" titulo="'.htmlspecialchars($leccion->titulo).'" tipo="'.$leccion->tipo.'" fallos="'.$fallos.'" tiempo="'.$tiempo.'" siguiente="'.
			'"><desc>'.nl2br(htmlspecialchars($leccion->descripcion)).'</desc>'.
			$teclado.
		implode('',$preguntas).'</leccion>';
	}
}
?>