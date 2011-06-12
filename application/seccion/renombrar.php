<?php
class UrlController extends PageTemplate{
	function _contenido($seccion=0){
		$seccion=new Seccion($seccion);
		
		if(!$this->usuario and $this->usuario!=$seccion->usuario->id) redirect(base_url());
		
		$form=new vForm(array(
				new vPreformatedText('<legend><span>'.getInterface('section_rename_title').'</span></legend>'),
				($this->error?new vDiv(getInterface($this->error),'error'):''),
				new vInputText('nombre',getInterface('section_name'),$seccion->nombre)
			));
			
		$form->setSubmitText(getInterface('button_save'));
		
		return new vDiv($form,'genericform');
	}
	
	function _processForm($seccion=0){
		$seccion=new Seccion($seccion);
		
		if(!$this->usuario and $this->usuario!=$seccion->usuario->id) redirect(base_url());
		
		if(!getParam('nombre')) $this->error = 'section_rename_error';
		else{
			$seccion->nombre=getParam('nombre');
			$seccion->save();
			
			redirect(base_url().'seccion/index/'.$seccion->id);
		}
	}
	
	function _title(){
		return getInterface('section_rename_title');
	}
}
?>