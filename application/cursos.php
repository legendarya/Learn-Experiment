<?php
class UrlController extends PageTemplate{
	
	function _contenido($url='',$apartado=null){
		if($apartado){
			$apartado=new Apartado($apartado);
			$result=$this->muestraApartado($apartado);
		}
		else $result=new vContainer(array(
			new vHeader('Materias'),
			$this->lista_materias()
		));
		
		return new vContainer(array(
			$this->migas($apartado),
			$result
		));
	}
	
	function muestraApartado($apartado){
		return new vDiv(array(
				new vHeader($apartado->nombre),
				($this->esResponsable($apartado)?new vDiv(array(
					new vLink(base_url().'apartados/editar/'.$apartado->id,'Editar apartado','','buttonLink'),
					new vLink(base_url().'apartados/crear/'.$apartado->id,'Crear subapartado','','buttonLink'),
					new vLink(base_url().'ejercicio/crear_abierto//'.$apartado->id,'Crear ejercicio','','buttonLink')
				)):''),
				$this->navegacionApartados($apartado),
				$this->listaEjercicios($apartado),
				new vPreformatedText($apartado->descripcion),
				($apartado->subapartados->count()?new vSection(array(
					new vHeader('Apartados'),
					$this->jerarquiaSubapartados($apartado)
				),'subapartados'):''),
				$this->navegacionApartados($apartado)
			),'contenidos_apartado');
	}
	
	function jerarquiaSubapartados($apartado){
		$lista=new vList();
		foreach($apartado->subapartados as $subapartado){
			$lista->add(new vContainer(array(
				$this->apartadoLink($subapartado),
				(($num=$subapartado->ejercicios->count())?' ('.$num.' ejercicio'.($num>1?'s':'').')':''),
				$this->jerarquiaSubapartados($subapartado)
				
			)));
		}
		return $lista;
	}
	
	
	function listaEjercicios($apartado){
		if($apartado->ejercicios->count()){
			$ejercicios=new vMenu();
			foreach($apartado->ejercicios as $ejercicio){
				$ejercicios->add($ejercicio->titulo,base_url().'aprender/'.str2url($ejercicio->titulo).'/'.$ejercicio->id);
			}
			
			return new vSection(array(
				new vHeader('Ejercicios'),
				$ejercicios
			),'lista_ejercicios');
		}
	}
	
	function navegacionApartados($apartado){
		if(!$apartado->padre or !$apartado->padre->padre) return;
		
		$lista_apartados=array();
		array_unshift($lista_apartados,$apartado);
		while($lista_apartados[0]->padre)
			array_unshift($lista_apartados,$lista_apartados[0]->padre);
		
		if($anterior=$apartado->previous()) 
			$anterior=new vDiv(array(
				'< Anterior: ',
				$this->apartadoLink($anterior)
			),'enlace_anterior');
			
		if($siguiente=$apartado->next()) 
			$siguiente=new vDiv(array(
				'Siguiente: ',
				$this->apartadoLink($siguiente),
				' >'
			),'enlace_siguiente');
		
		return new vDiv(array(
			$anterior,
			$siguiente,
			new vDiv(
				new vLink(base_url().'materias/'.str2url($apartado->padre->nombre).'/'.$apartado->padre->id,'Padre')
			)
		),'opciones_navegacion');
	}
	
	function lista_materias(){
		$result=new vContainer();
		
		$apartados=new Apartados();
		foreach ($apartados->padreIs(0) as $apartado){
			$subapartados=new vList();
			foreach($apartado->subapartados->cursoIs(1) as $subapartado)
				$subapartados->add(new vContainer(array(
					$this->apartadoLink($subapartado),
					($subapartado->descripcion?new vPreformatedText(': '.recortaTexto(strip_tags($subapartado->descripcion),100)):'')
				)));
				
			$cursos_no_oficiales=array('Cursos no oficiales: ');
			foreach($apartado->subapartados->cursoIs(0) as $i=>$subapartado){
				if($i) $cursos_no_oficiales[]=', ';
				$cursos_no_oficiales[]=$this->apartadoLink($subapartado);
			}
				
			$result->add(new vSection(array(
				new vHeader($apartado->nombre),
				($this->usuario?
					new vLink(base_url().'apartados/crear/'.$apartado->id,'Crear nuevo curso','','buttonLink')
				:''),
				$subapartados,
				($apartado->subapartados->cursoIs(0)->count()?new vDiv($cursos_no_oficiales,'cursos_no_oficiales'):'')
				)));
		}
		
		return $result;
	}
	
	function apartadoLink($apartado,$clase='',$title=''){
		return new vLink(base_url().'materias/'.str2url($apartado->nombre).'/'.$apartado->id,$apartado->nombre,$title,$clase);
	}
	
	function migas($apartado){
		$migas=new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > ',
			($apartado?
			new vLink(base_url().'materias/','Materias')
			:'Materias'),
			($apartado?
			new vContainer(array(' > ',$apartado->nombre))
			:'')
		),'migas');
		
		if($apartado){
		$apartado_superior=$apartado->padre;
			while($apartado_superior){
				$migas->add($this->apartadoLink($apartado_superior),4);
				$migas->add(' > ',4);
				$apartado_superior=$apartado_superior->padre;
			}
		}
			
		
		return $migas;
	}
	
	function _title($url='',$apartado=null){
		if(!$apartado) return 'Materias';
		$apartado=new Apartado($apartado);
		return $apartado->nombre;
	}
}
?>