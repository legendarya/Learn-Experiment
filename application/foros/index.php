<?php
class UrlController extends PageTemplate{
	function _contenido(){
		$this->page->addCss('css/foro/index.css');
		
		$foros=new Foros();
		
		$table=new vTable(array('','Foro','Último mensaje','Temas'));
		$table->setClass('foro');
		
		foreach($foros->orderBy('orden') as $foro){
			$table->add($row=new vRow());
			
			$row->add(new vDiv(),
				($this->usuario and $foro->ultimoMensaje and $tema_leido=$this->usuario->temasLeidos->temaIs($foro->ultimoMensaje->tema)->getFirst() and
				$tema_leido->fecha->format('U')>$foro->ultimoMensaje->fecha->format('U'))?
				'foroNoLeido'
				:'foroLeido');
				
			$row->add(new vContainer(array(
					new vLink(base_url().'foros/foro/'.str2url($foro->nombre).'/'.$foro->id,$foro->nombre,null,'titulo'),
					$foro->descripcion
				)));
			$row->add(($foro->ultimoMensaje?new vContainer(array(
					new vDiv(array(' ',$this->fechaRelativa(new DateTime(),$foro->ultimoMensaje->fecha))),
					new vDiv(array(' en ',new vLink(base_url().'foros/tema/'.str2url($foro->ultimoMensaje->tema->titulo).'/'.$foro->ultimoMensaje->tema->id,$foro->ultimoMensaje->tema->titulo))),
					new vDiv(array('por ',$this->muestraNombreUsuario($foro->ultimoMensaje),' ',
					new vLink(base_url().'foros/tema/'.str2url($foro->ultimoMensaje->tema->titulo).'/'.$foro->ultimoMensaje->tema->id.'/'.(ceil($foro->ultimoMensaje->tema->mensajes->count()/10)-1).'#ultimoMensaje',
							new vImage('css/foro/lastPost.png'))
					))
				)):'')
				,'colUltimo');
			$row->add($foro->temas->count());
		}
	
		return $table;
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
		return $fecha2->format('d').' de '.$meses[$fecha2->format('n')].' del '.$fecha2->format('Y');
	}
	
	function _title(){
		return 'Foro';
	}
	
	function comentarios_key(){
		return 'learnexperimentApartadoForo';
	}
}
?>