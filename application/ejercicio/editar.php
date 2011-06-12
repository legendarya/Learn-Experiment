<?php
class UrlController extends ProcessTemplate{
	
	function execute($id_ejercicio=null){
		if($id_ejercicio){
			$ejercicio=new Ejercicio($id_ejercicio);
			if(($ejercicio->director and $this->usuario->id==$ejercicio->director->id) or $this->esResponsable($ejercicio->apartado)){
			
				// Miramos si había versión anterior
				if(!$ejercicio->version) $this->guardaVersion($ejercicio);
			
				$ejercicio->preguntas->deleteAll();
				
				if(getParam('cuadro_preguntas')){
					$preguntas=explode("\n",getParam('cuadro_preguntas'));
					foreach($preguntas as $pregunta){
						$pregunta=explode("\t",$pregunta);
						if(isset($pregunta[0])){
							$preg=$ejercicio->preguntas->add();
							$preg->pregunta=$pregunta[0];
							if(isset($pregunta[1])) $preg->respuesta=$pregunta[1];
							$preg->save();
						}
					}
				}else{
					$i=0;
					while(isset($_POST['c0'.$i])){ 
						if($_POST['c0'.$i]){
							$pregunta=$ejercicio->preguntas->add();
							$pregunta->pregunta=$_POST['c0'.$i];
							$pregunta->respuesta=$_POST['c1'.$i];
							$pregunta->save();
						}
						$i++;
					}
				}
				
				// Guardamos los nuevos valores del ejercicio
				$ejercicio->titulo=$_POST['titulo'];
				//$ejercicio->fecha_actualizacion=new DateTime();
				$ejercicio->descripcion=$_POST['descripcion'];
				$ejercicio->pronunciar=$_POST['pronunciar'];
				$ejercicio->ordenar=isset($_POST['ordenar'])?$_POST['ordenar']=='on':false;
				$ejercicio->tipo=$_POST['tipo'];
				$this->guardaVersion($ejercicio);
				$ejercicio->save();
			}
			
			redirect(base_url().'aprender/'.$ejercicio->url.'/'.$ejercicio->id);
		}
	}
}
?>