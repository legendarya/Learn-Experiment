<?php
class UrlController extends PageTemplate{
	
	function _contenido($foro){
		$foro=new Foro($foro);
			
		return new vContainer(array(
			$this->migas($foro),
			new vHeader('Iniciar nuevo tema'),
			$this->form($foro)
		));
	}
	
	function form($foro){
		$form=new vForm(array(
				($this->error?new vDiv($this->error,'error'):''),
				new vInputText('titulo','Titulo:'),
				$mensaje=new vInputText('mensaje','Mensaje:'),
			));
			
		$mensaje->setMaxLength(null);
		return $form;
	}
	
	function _processForm($foro){
		$foro=new Foro($foro);
		if($this->usuario and (!$foro->noticias or $this->usuario->administrador)){
			if(!getParam('titulo')) $this->error='Debes escribir un titulo para el tema';
			else if(!getParam('mensaje')) $this->error='Debes escribir un mensaje inicial';
			else{
		
				$tema=$foro->temas->add();
				$tema->titulo=getParam('titulo');
				$tema->fecha=new DateTime();
				$tema->fecha_creacion=new DateTime();
				$tema->save();
				
				$mensaje=$tema->mensajes->add();
				$mensaje->usuario=$this->usuario;
				$mensaje->fecha=new DateTime();
				$mensaje->mensaje=getParam('mensaje');
				$mensaje->save();
				
				$tema->ultimoMensaje=$mensaje->id;
				$tema->save();
				
				$tema->foro->ultimoMensaje=$mensaje->id;
				$tema->foro->save();
				
				redirect(base_url().'foros/tema/'.str2url($tema->titulo).'/'.$tema->id);
			}
		}
	}
	
	function migas($foro){
		return new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > ',
			new vLink(base_url().'foros/','Foros'),' > ',
			new vLink(base_url().'foros/foro/'.str2url($foro->nombre).'/'.$foro->id.'/',$foro->nombre),' > ',
			'Iniciar nuevo tema'
		),'migas');
	}
	
	function _title($foro){
		$foro=new Foro($foro);
		return 'Iniciar nuevo tema en '.$foro->nombre;
	}
}
?>