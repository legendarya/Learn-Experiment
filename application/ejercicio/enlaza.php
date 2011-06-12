<?php
class UrlController extends PageTemplate{

	function _contenido($apartado='',$busqueda='',$pagina=0){
		if(getParam('busqueda')) redirect(base_url().'ejercicio/enlaza/'.$apartado.'/'.getParam('busqueda').'/');
	
		$ejercicios=new Ejercicios();
		if($busqueda) $ejercicios=$ejercicios->tituloLike($busqueda);
		
		$lista_ejercicios=new vTable(array('Ejercicio','Preguntas','Autor','Fecha','Apartado'));
		foreach($ejercicios->paginate(30,$pagina)->orderBy('	fecha_actualizacion',false) as $ejercicio)
			$lista_ejercicios->add(array(
				new vLink(base_url().'ejercicio/realiza_enlace/'.$apartado.'/'.$ejercicio->id,$ejercicio->titulo),
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
			new vHeader('Escoge un ejercicio para incluir en la lección'),
			$buscador,
			$lista_ejercicios,
			new vDiv(array('Paginación:',new vPagination(floor($ejercicios->count()/30),$pagina,base_url().'ejercicio/enlaza/'.$apartado.'/'.$busqueda.'/')),'paginacion')
		));
	}
	
	function _title(){
		return 'Enlazar ejercicios';
	}
}
?>