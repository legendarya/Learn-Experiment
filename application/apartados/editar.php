<?php
class UrlController extends PageTemplate{
	
	function _contenido($apartado){
		$apartado=new Apartado($apartado);
			
		return new vContainer(array(
			$this->migas($apartado),
			new vHeader('Editar apartado'),
			$this->form($apartado)
		));
	}
	
	function form($apartado){
		$this->page->addScript('js/tinymce/tiny_mce.js');
		$this->page->addScript('js/tinyMCEinit.js');
		
		$form=new vForm(array(
				($this->error?new vDiv($this->error,'error'):''),
				new vInputText('nombre','Nombre:',$apartado->nombre),
				$descripcion=new vInputText('descripcion','Descripcion:',$apartado->descripcion),
			));
			
		$descripcion->setMaxLength(null);
			
		return $form;
	}
	
	function _processForm($apartado){
		$apartado=new Apartado($apartado);
		if($this->esResponsable($apartado)){
			if(!getParam('nombre')) $this->error='Debes escribir un nombre para el apartado';
			else{
				$apartado->nombre=getParam('nombre');
				$apartado->descripcion=getParam('descripcion');
				$apartado->save();
				
				redirect(base_url().'cursos/'.str2url($apartado->nombre).'/'.$apartado->id);
			}
		}
	}
	
	function migas($apartado){
		$migas=new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > Editar apartado'
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
		return 'Editar apartado';
	}
}
?>