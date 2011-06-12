<?php
class UrlController extends PageTemplate{
	
	function _contenido($curso){
		$this->page->addScript('js/jquery.js');
		$this->page->addScript('js/jquery-ui.js');
		$this->page->addScript('js/reorganiza.js');
		$this->page->addScript('js/utilidades.js');
		
		$apartado=new Apartado($curso);
		if(!$this->esResponsable($apartado)) redirect();
			
			
		return new vContainer(array(
			$this->migas($apartado),
			new vHeader('Reorganizar curso'),
			new vDiv($this->jerarquia($apartado),'jerarquia_reorganiza')
		));
	}
	
	function jerarquia($apartado){
		$lista_supapartados=new vList();
		foreach($apartado->subapartados as $subapartado){
			$lista_supapartados->add(new vContainer(array(
				new vSpan($subapartado->nombre,'apartado','apartado'.$subapartado->id),
				$this->jerarquia($subapartado)
			)));
		}
		
		$lista_supapartados->add(new vContainer(array(
			new vSpan('','ultimo_nodo','hijo'.$apartado->id)
		)),'ultimo_nodo');
		
		return $lista_supapartados;
	}
	
	function migas($apartado){
		$migas=new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > Reorganizar curso'
		),'migas');
		
		$apartado_superior=$apartado;
		while($apartado_superior){
			$migas->add($this->apartadoLink($apartado_superior),2);
			$migas->add(' > ',2);
			$apartado_superior=$apartado_superior->padre;
		}
		
		return $migas;
	}
	
	function apartadoLink($apartado){
		return new vLink(base_url().'cursos/'.str2url($apartado->nombre).'/'.$apartado->id,$apartado->nombre);
	}
	
	function _title(){
		return 'Reorganiza curso';
	}
}
?>