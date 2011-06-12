<?php
class UrlController extends PageTemplate{
	
	function _contenido($tema){
		$tema=new Tema($tema);
			
		return new vContainer(array(
			$this->migas($tema),
			new vHeader('Responder tema'),
			$this->form($tema)
		));
	}
	
	function form($tema){
		$form=new vForm(array(
				($this->error?new vDiv($this->error,'error'):''),
				$mensaje=new vInputText('mensaje','Mensaje:'),
			));
			
		$mensaje->setMaxLength(null);
		return $form;
	}
	
	function _processForm($tema){
		
		if($this->usuario){
			if(!getParam('mensaje')) $this->error='Debes escribir un mensaje inicial';
			else{
				$tema=new Tema($tema);
				
				$mensaje=$tema->mensajes->add();
				$mensaje->usuario=$this->usuario;
				$mensaje->fecha=new DateTime();
				$mensaje->mensaje=getParam('mensaje');
				$mensaje->save();
				
				$tema->fecha=new DateTime();
				$tema->ultimoMensaje=$mensaje->id;
				$tema->save();
				
				$tema->foro->ultimoMensaje=$mensaje->id;
				$tema->foro->save();
				
				redirect(base_url().'foros/tema/'.str2url($tema->titulo).'/'.$tema->id.'/'.(ceil($tema->mensajes->count()/10)-1).'#ultimoMensaje');
			}
		}
	}
	
	function migas($tema){
		return new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > ',
			new vLink(base_url().'foros/','Foros'),' > ',
			new vLink(base_url().'foros/foro/'.str2url($tema->foro->nombre).'/'.$tema->foro->id.'/',$tema->foro->nombre),' > ',
			new vLink(base_url().'foros/tema/'.str2url($tema->titulo).'/'.$tema->id.'/',$tema->titulo),' > ',
			'Responder tema'
		),'migas');
	}
	
	function _title($tema){
		$tema=new Tema($tema);
		return 'Rersponder tema '.$tema->titulo;
	}
}
?>