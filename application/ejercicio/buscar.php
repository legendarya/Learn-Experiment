<?php
class UrlController extends PageTemplate{

	function _contenido($busqueda='',$pagina=0){
		if(getParam('busqueda')) redirect(base_url().'ejercicio/buscar/'.getParam('busqueda').'/');
	
		$ejercicios=new Ejercicios();
		if($busqueda) $ejercicios=$ejercicios->tituloLike($busqueda);
		
		$lista_ejercicios=new vTable(array('Ejercicio','Preguntas','Autor','Fecha','Apartado'));
		foreach($ejercicios->paginate(30,$pagina)->orderBy('	fecha_actualizacion',false) as $ejercicio)
			$lista_ejercicios->add(array(
				new vLink(base_url().'aprender/'.str2url($ejercicio->titulo).'/'.$ejercicio->id,$ejercicio->titulo),
				$ejercicio->preguntas->count(),
				($ejercicio->director?$ejercicio->director->nombre_usuario:''),
				$ejercicio->fecha_actualizacion->format('d/m/y'),
				($ejercicio->apartado?$ejercicio->apartado->nombre:'')
			));
			
		$buscador=new vForm(array(
			new vInputText('busqueda','Busqueda:',$busqueda)
		),'','get');
		
		$buscador->setSubmitText('Buscar');
		$buscador->setClass('buscador');
			
		return new vContainer(array(
			new vHeader('Buscar ejercicios'),
			$buscador,
			$lista_ejercicios,
			new vDiv(array('Paginación:',new vPagination(floor($ejercicios->count()/30),$pagina,base_url().'ejercicio/buscar/'.$busqueda.'/')),'paginacion')
		));
	}
	
	function _title(){
		return 'Buscar ejercicios';
	}
}
?>