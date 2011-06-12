<?php
class UrlController extends PageTemplate{
	function _contenido($url,$id_tema,$pagina=0){
		$this->page->addCss('css/foro/index.css');
		
		if($this->usuario){
			if(!$tema_leido=$this->usuario->temasLeidos->temaIs($id_tema)->getFirst()){
				$tema_leido=$this->usuario->temasLeidos->add();
				$tema_leido->tema=$id_tema;
			}
			$tema_leido->fecha=new DateTime();
			$tema_leido->save();
		}
		
		$tema=new Tema($id_tema);
		
		$list=new vContainer();
		
		foreach($tema->mensajes->orderBy('fecha')->limit(10,$pagina) as $mensaje)
			$list->add(new vDiv(array(
				new vDiv($ultimo_mensaje=new vSpan($mensaje->fecha->format('d/m/Y').' a las '.$mensaje->fecha->format('H:i:s'),'fechaMsj')),
				new vDiv($this->muestraNombreUsuario($mensaje),'infoMsj'),
				new vDiv(new vPreformatedText(nl2br($mensaje->mensaje)),'cuerpoMsj')
			),'contenedorMsj'));
			
		$ultimo_mensaje->add($ultimo_mensaje_link=new vLink('',''));
		$ultimo_mensaje_link->setName('ultimoMensaje');
	
		$paginacion=new vDiv(array('Paginas:',
			new vPagination($tema->mensajes->count()/10,$pagina,base_url().'foros/tema/'.str2url($tema->titulo).'/'.$id_tema.'/')
		),'paginacion');
		
		
		return new vContainer(array(
			$this->migas($tema),
			new vHeader($tema->titulo),
			($this->usuario?
				new vLink(base_url().'foros/responder_tema/'.$tema->id,'Responder tema','','leButton')
				:new vDiv('Debes identificarte o registrarte para escribir en el foro','infoAlert')
			),
			$paginacion,
			$list,
			$paginacion,
			($this->usuario?
				new vLink(base_url().'foros/responder_tema/'.$tema->id,'Responder tema','','leButton')
			:'')
		));
	}
	
	function migas($tema){
		return new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > ',
			new vLink(base_url().'foros/','Foros'),' > ',
			new vLink(base_url().'foros/foro/'.str2url($tema->foro->nombre).'/'.$tema->foro->id.'/',$tema->foro->nombre),' > ',
			$tema->titulo
		),'migas');
	}
	
	function _title($url,$id_tema,$pagina=0){
		$tema=new Tema($id_tema);
		
		return $tema->titulo.' - '.$tema->foro->nombre;
	}
}
?>