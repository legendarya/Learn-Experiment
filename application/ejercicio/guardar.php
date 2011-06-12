<?php
class UrlController extends ProcessTemplate{
	
	function execute($url_ejercicio=null,$id_ejercicio=null){
		$ejercicio=new Ejercicio($id_ejercicio);
		
		$preguntas=array();
		foreach(explode('//',$_GET['preguntas']) as $pregunta){
			$pregunta=explode(',',$pregunta);
			$preguntas[$pregunta[0]]=array('tiempo'=>$pregunta[1],'nivel'=>$pregunta[2]);
		}
		
		// Si el usuario está identificado
		if($this->usuario){
			// Si  no se habían guardado datos antes, creamos registro
			if(!$estado=$this->usuario->ejercicios->where(new EqualCondition('leccion',$ejercicio->id))->getFirst()){
				$estado=$this->usuario->ejercicios->add();
				$estado->leccion=$ejercicio->id;
				$estado->mejor_fallos=$_GET['fallos'];
				$estado->mejor_porcentaje=$_GET['porcentaje'];
				$estado->mejor_tiempo=$_GET['tiempo'];
			}
			
			$estado->ultimo_fallos=$_GET['fallos'];
			$estado->ultimo_porcentaje=$_GET['porcentaje'];
			$estado->ultimo_tiempo=$_GET['tiempo'];
			
			if($estado->mejor_porcentaje<=$estado->ultimo_porcentaje){
				$estado->mejor_porcentaje=$estado->ultimo_porcentaje;
				if($estado->mejor_fallos<=$estado->ultimo_fallos){
					$estado->mejor_fallos=$estado->ultimo_fallos;
					if($estado->mejor_tiempo<$estado->ultimo_tiempo)
						$estado->mejor_tiempo=$estado->ultimo_tiempo;
				}
			}

			$estado->preguntas=$preguntas;
			$estado->repaso_anterior=new DateTime();
			$estado->save();
		}
		else{ // Si es un usuario no identificado
			if(!isset($_SESSION['estados'])) $_SESSION['estados']=array();
			$_SESSION['estados'][$ejercicio->id]=array('preguntas'=>$preguntas,'fecha'=>new DateTime(),
				'fallos'=>$_GET['fallos'],'porcentaje'=>$_GET['porcentaje'],'tiempo'=>$_GET['tiempo']);
		}
	}
}
?>