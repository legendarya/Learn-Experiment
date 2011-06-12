<?php
class UrlController extends PageTemplate{
	
	function _contenido(){
		$result = new vDiv('','boxContainer');
		
		$result->add(new vSection(
			array(
				new vHeader('Introducción'),
				new vDiv(new vDiv(
					array(new vParagraph(
						'LearnExperiment te ayuda a aprender vocabulario en diferentes idiomas. Tiene la ventaja de que se adapta a tu ritmo de aprendizaje.'),
						new vParagraph(
						'Para probarlo selecciona uno de los cursos de abajo y escoge un ejercicio.')
						/*,new vLink('','Dar un paseo por la web','','lnkTour')*/
					)
				,'contBoxSec'),'box'),
			)
		,'sectorPortada introduccion'));
		
		
		
		$foros=new Foros();
		$lista_noticias=new vContainer();	
		foreach($foros->noticiasIs(1)->getFirst()->temas->orderBy('fecha_creacion',false)->limit(3) as $noticia)
			$lista_noticias->add(new vSection(array(
				new vHeader(new vLink(base_url().'foros/tema/'.str2url($noticia->titulo).'/'.$noticia->id,recortaTexto($noticia->titulo,38))),
				new vPreformatedText($noticia->fecha->format('d/m').' - '.recortaTexto($noticia->mensajes->getFirst()->mensaje,80))
			)));
			
		$lista_noticias->add(new vDiv(new vLink(base_url().'foros/foro/noticias/39/','Ver más noticias'),'ver_mas_noticias'));
		
		$result->add(new vSection(
			array(
				new vHeader('Noticias'),
				new vDiv(new vDiv($lista_noticias,'contBoxSec'),'box')
			)
		,'sectorPortada noticias'));
		
		$result->add(new vSection(
			array(
				new vHeader('Cursos'),
				new vDiv(new vDiv($this->muestraCursos(),'contBoxSec'),'box')
			)
		,'sectorPortada cursos'));
		
		$ejercicios=new Ejercicios();
		
		$list=new vMenu();
		foreach($ejercicios->limit(13)->orderBy('fecha_actualizacion',false) as $ejercicio){
			$idioma=$ejercicio->apartado;
			while($idioma and $idioma->padre) $idioma=$idioma->padre;
		
			$list->add(new vContainer(array(new vSpan($ejercicio->fecha_actualizacion->format('d/m')),$ejercicio->titulo.
				($idioma?' ('.$idioma->nombre.')':''))),
				base_url().'aprender/'.str2url($ejercicio->titulo).'/'.$ejercicio->id,'- '.$ejercicio->titulo);
		}
		
		$result->add(new vSection(
			array(
				new vHeader('Novedades'),
				new vDiv(new vDiv($list,'contBoxSec'),'box')
			)
		,'sectorPortada novedades'));
		
		$result->add(new vDiv('','end'));
		return $result;
	}
	
	function muestraCursos(){
		$cursos=new Apartados();
		$cursos=$cursos->where(new EqualCondition('padre',0));
		
		$lista_cursos=new vList();
		foreach($cursos->padreIs(0) as $curso){
			
			$lista_apartados=new vContainer();
			foreach($curso->subapartados->cursoIs(1) as $i=>$apartado){
				if($i) $lista_apartados->add(', ');
				$lista_apartados->add(new vLink(base_url().'cursos/'.str2url($apartado->nombre).'/'.$apartado->id,$apartado->nombre));
			}
			
			$lista_cursos->add(new vContainer(array(
				new vLink(base_url().'cursos/'.str2url($curso->nombre).'/'.$curso->id,$curso->nombre,'','catNombre'),' ',$lista_apartados
			)));
		}
			
		return $lista_cursos;
	}
	
	function _title(){
		return 'Portada';
	}
}
?>