<?php
class UrlController extends PageTemplate{
	
	function _contenido($url,$apartado){
		$apartado=new Apartado($apartado);
		
		$result = new vContainer();
		$box = new vDiv('','LE_Box');
		
		$box->add(new vDiv(new vHeader($apartado->nombre),'LE_BoxTitle'));
		
		if($this->usuario and $this->esResponsable($apartado)){
			$box->add(new vDiv(new vContainer(array( // Si elusuario está registrado ofrecemos opciones de edición
				($this->esResponsable($apartado)?new vContainer(array(
					new vLink(base_url().'apartados/crear/'.$apartado->id,new vSpan('Crear subapartado')),' ',
					new vLink(base_url().'apartados/editar/'.$apartado->id,new vSpan('Editar apartado')),
					new vLink(base_url().'ejercicio/crear_abierto//'.$apartado->id,new vSpan('Crear nuevo ejercicio')),
				)):'')
			)),'LE_BoxToolbar'));
		}
		
		$box->add($content = new vDiv('','LE_BoxContent'));
		
		$content->add($this->listaCursos($apartado));
		//$content->add(new vDiv($apartado->descripcion,'apartadoDesc'));
		$content->add(new vSection($this->listaEjercicios($apartado),'ejercicios'));
		
		$box->add(new vDiv($this->responsables($apartado),'LE_BoxFootInfo'));
		
		$result->add($this->migas($apartado));
		$result->add($box);
		return $result;
	}
	
	function listaEjercicios($apartado_actual){
		$result=new vContainer();
		
		$result->add($lista_ejercicios=new vList());
		foreach($apartado_actual->ejercicios as $ejercicio){
			
			$lista_ejercicios->add(
				new vLink(base_url().'aprender/'.str2url($ejercicio->titulo).'/'.$ejercicio->id,'- '.$ejercicio->titulo)
			);
		}
		
		foreach($apartado_actual->subapartados as $apartado){
			if($apartado->ejercicios->count()) $result->add(new vHeader($apartado->nombre));
			$result->add($this->listaEjercicios($apartado));
		}
		
		return $result;
	}
	
	
	function listaCursos($apartado_actual){
		$lista_cursos=new vList();
		
		$lista_hijos=$lista_cursos;
		$ap=$apartado_actual;
		while($ap->padre){
			$ap=$ap->padre;
			
			$lista_padre=new vList();
			$lista_padre->add(new vContainer(array(
				$this->apartadoLink($ap),
				$lista_hijos
			),'desplegado'));
			$lista_hijos=$lista_padre;
		}
		
		if($apartado_actual->padre)
			foreach($apartado_actual->padre->subapartados as $i=>$apartado){
				if($apartado->id==$apartado_actual->id){
					$lista_cursos->add(new vContainer(array(
						$this->apartadoLink($apartado,'actual'),
						$hijos=new vList()
					),'desplegado'));
					
					foreach($apartado->subapartados as $apartado2)
						$hijos->add($this->apartadoLink($apartado2));
				}
				else $lista_cursos->add($this->apartadoLink($apartado));
			}
		else{
			$lista_cursos->add(new vContainer(array(
				$this->apartadoLink($apartado_actual),
				$hijos=new vList()
			)));
			
			foreach($apartado_actual->subapartados as $apartado2)
				$hijos->add($this->apartadoLink($apartado2));
		}
		
		return new vDiv($lista_hijos,'apartadosList');
	}
	
	
	function comentarios_key(){
		return 'learnexperimentApartado'.$this->urlParams[1];
	}
	
	function responsables($apartado){
		$responsables=array();
		$coma=false;
		while($apartado){
			foreach($apartado->responsables as $responsable){
				if($coma) $responsables[]=', ';
				$responsables[]=
					($responsable->usuario->facebook?new vPreformatedText('<fb:profile-pic width="20" height="20" uid="'.$responsable->usuario->facebook.'" size="square" facebook-logo="true"></fb:profile-pic>
					<fb:name uid="'.$responsable->usuario->facebook.'" useyou="false" linked="true"></fb:name>'):
					$responsable->usuario->nombre_usuario);
				$responsables[]=' (';
				$responsables[]=$this->apartadoLink($apartado);
				$responsables[]=')';
				$coma=true;
				
			}
			$apartado=$apartado->padre;
		}
		
		return new vDiv($responsables,'responsables');
	}
	
	function migas($apartado){
		$migas=new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > ',$apartado->nombre
		),'migas');
		
		$apartado_superior=$apartado->padre;
		while($apartado_superior){
			$migas->add($this->apartadoLink($apartado_superior),3);
			$migas->add(' > ',4);
			$apartado_superior=$apartado_superior->padre;
		}
		
		return $migas;
	}
	
	function apartadoLink($apartado,$clase='',$title=''){
		return new vLink(base_url().'apartado/'.str2url($apartado->nombre).'/'.$apartado->id,$apartado->nombre,$title,$clase);
	}
	
	function _title($url,$apartado){
		$apartado=new Apartado($apartado);
		return $apartado->nombre;
	}
}
?>