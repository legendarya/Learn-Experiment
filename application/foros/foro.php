<?php
class UrlController extends PageTemplate{
	function _contenido($url,$id_foro,$pagina=0){
		$this->page->addCss('css/foro/index.css');
		
		$foro=new Foro($id_foro);
		
		$table=new vTable(array('','Tema','Último mensaje','Respuestas'));
		$table->setClass('foro');
		
		foreach($foro->temas->orderBy('fecha',false)->limit(10,$pagina) as $tema){
			$table->add($row=new vRow());
			
			$row->add(new vDiv(),
				($this->usuario and $tema_leido=$this->usuario->temasLeidos->temaIs($tema)->getFirst() and
				$tema_leido->fecha->format('U')>$tema->fecha->format('U'))?
				'temaNoLeido'
				:'temaLeido');
			
			$row->add(new vContainer(array(
					new vLink(base_url().'foros/tema/'.str2url($tema->titulo).'/'.$tema->id,$tema->titulo,null,'titulo'),
					' autor: ',$this->muestraNombreUsuario($tema->mensajes->orderBy('fecha')->getFirst())
				)));
			$row->add(($tema->ultimoMensaje?new vContainer(array(
					new vDiv(array(' ',$this->fechaRelativa(new DateTime(),$tema->ultimoMensaje->fecha))),
					new vDiv(array('por ',$this->muestraNombreUsuario($tema->ultimoMensaje),' ',
						new vLink(base_url().'foros/tema/'.str2url($tema->titulo).'/'.$tema->id.'/'.(ceil($tema->mensajes->count()/10)-1).'#ultimoMensaje',
							new vImage('css/foro/lastPost.png'))
					))
				)):'')
				,'colUltimo');
			$row->add($tema->mensajes->count()-1);
		}
	
		$cabecera=new vHeader($foro->nombre);
		$cabecera->setClass('cabecera');
	
		$paginacion=new vDiv(array('Paginas:',
			new vPagination($foro->temas->count()/10,$pagina,base_url().'foros/foro/'.str2url($foro->nombre).'/'.$id_foro.'/')
		),'paginacion');
		
		return new vContainer(array(
			$this->migas($foro),
			($this->usuario?
				((!$foro->noticias or $this->usuario->administrador)?new vLink(base_url().'foros/nuevo_tema/'.$foro->id,'Iniciar nuevo tema','','leButton'):'')
				:new vDiv('Debes identificarte o registrarte para escribir en el foro','infoAlert')
			),
			$paginacion,
			$cabecera,
			$table,
			$paginacion
		));
	}
	
	function migas($foro){
		return new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > ',
			new vLink(base_url().'foros/','Foros'),' > ',
			$foro->nombre
		),'migas');
	}
	
	function muestraNombreUsuario($mensaje){
		return ($mensaje->usuario?new vLink('',$mensaje->usuario->nombre_usuario):$mensaje->nombre);
	}
	
	// Devuelve una cadena de texto con una facha fácil de entender de un vistazo como "hace 15 minutos", "ayer" o "hace dos semanas".
	function fechaRelativa($fecha1,$fecha2){
		$dif=$fecha1->format('U')-$fecha2->format('U');
		if($dif<40) return 'hace un momento';
		if($dif<120) return 'hace un minuto';
		if($dif<3540) return ' hace '.floor($dif/60).' minutos';
		if($dif<3660) return ' hace una hora';
		if($dif<7200) return ' hace una hora y '.floor($dif/60).' minutos';
		
		if($fecha1->format('Y')==$fecha2->format('Y') and $fecha1->format('z')==$fecha2->format('z')) return 'hace '.ceil($dif/3600).' horas';
		
		$diff_dias=$fecha1->format('z')-$fecha2->format('z')+($fecha1->format('Y')-$fecha2->format('Y'))*($fecha2->format('L')?366:365);
		
		if($diff_dias==1) return 'ayer a las'.$fecha2->format('H:i');
		
		if($fecha1->format('Y')==$fecha2->format('Y') and $fecha1->format('m')==$fecha2->format('m')) return 'hace '.$diff_dias.' días';
		
		$meses=array(0,'enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
		
		if($fecha1->format('Y')==$fecha2->format('Y')) return 'el 10 de '.$meses[$fecha2->format('n')];
		return $fecha2->format('d').'de '.$meses[$fecha2->format('n')].' del '.$fecha2->format('Y');
	}
	
	function _title($url,$id_foro,$pagina=0){
		$foro=new Foro($id_foro);
		return $foro->nombre.' - Foros';
	}
	
	function comentarios_key(){
		return 'learnexperimentApartadoForo';
	}
}
?>