<?php
class UrlController extends PageTemplate{
	
	function _contenido($seccion){
		$seccion=new Seccion($seccion);
		
		$lista_suscritos=new vList();
		foreach($seccion->suscripciones->usuario->limit(30) as $usuario){
			$lista_suscritos->add(new vContainer(array(
				new vLink(base_url().'usuario/perfil/'.$usuario->id,$usuario->nombre))));
		}
		
		return new vSection(array(
			new vHeader(array(getInterface('section_title').$seccion->nombre.' (de ',
				new vLink(base_url().'usuario/perfil/'.$seccion->usuario->id,$seccion->usuario->nombre),')')),
			new vSection(new vSection(array(
				new vHeader(getInterface('section_followers')),
				new vDiv($lista_suscritos,'comments')
			),'sidebox'),'','sidebar'),
			($this->usuario?
				($this->usuario->id==$seccion->usuario->id?
						new vLink(base_url().'seccion/renombrar/'.$seccion->id,getInterface('section_rename'),'','link_button')
				:
					(($suscripcion=$this->usuario->suscripciones->seccionIs($seccion)->getFirst())?
						new vContainer(array(
							getInterface('section_following'),
							new vLink(base_url().'seccion/cancelar_suscripcion/'.$suscripcion->id,getInterface('section_unfollow'),'','link_button')
						))
					:
						new vLink(base_url().'seccion/suscribir/'.$seccion->id,getInterface('section_follow'),'','link_button')
					)
				)
			:''),
			$this->listaNoticias($seccion->noticias->noticia)
		));
	}
	
	function _title($seccion){
		$seccion=new Seccion($seccion);
		
		return getInterface('section_title').$seccion->nombre.' de '.$seccion->usuario->nombre;
	}
}
?>