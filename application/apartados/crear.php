<?php
class UrlController extends PageTemplate{
	
	function _contenido($apartado){
		$apartado=new Apartado($apartado);
			
		return new vContainer(array(
			$this->migas($apartado),
			new vHeader($apartado->padre?'Crear apartado':'Crear curso'),
			$this->form($apartado)
		));
	}
	
	function form($apartado){
		$this->page->addScript('js/tinymce/tiny_mce.js');
		$this->page->addScript('js/tinyMCEinit.js');
		
		$form=new vForm(array(
				($this->error?new vDiv($this->error,'error'):''),
				new vInputText('nombre','Nombre:'),
				$descripcion=new vInputText('descripcion','Descripcion:'),
			));
			
		$descripcion->setMaxLength(null);
			
		return $form;
	}
	
	function _processForm($apartado){
		
		if($this->usuario){
			if(!getParam('nombre')) $this->error='Debes escribir un nombre';
			else{
				$apartado=new Apartado($apartado);
		
				$nuevo_apartado=$apartado->subapartados->add();
				$nuevo_apartado->nombre=getParam('nombre');
				$nuevo_apartado->descripcion=getParam('descripcion');
				$nuevo_apartado->save();
				
				redirect(base_url().'cursos/'.str2url($nuevo_apartado->nombre).'/'.$nuevo_apartado->id);
			}
		}
	}
	
	function migas($apartado){
		$migas=new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > Crear apartado'
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
		return 'Crear apartado';
	}
}
?>