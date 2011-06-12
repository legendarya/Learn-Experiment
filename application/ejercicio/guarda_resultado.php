<?php
class UrlController extends ProcessTemplate{
	
	function execute($url_ejercicio=null,$id_ejercicio=null){
		$ejercicio=new Ejercicio($id_ejercicio);
		
		// Si el usuario está identificado
		if($this->usuario){
			$estado=$this->usuario->ejercicios->add();
			$estado->leccion=$ejercicio->id;
			$estado->ultimo_fallos=getParam('fallos');
			$estado->ultimo_porcentaje=100;
			$estado->ultimo_tiempo=getParam('tiempos');
			$estado->repaso_anterior=new DateTime();
			$estado->save();
		}
		/*else{ // Si es un usuario no identificado
			if(!isset($_SESSION['estados'])) $_SESSION['estados']=array();
			$_SESSION['estados'][$ejercicio->id]=array('preguntas'=>$preguntas,'fecha'=>new DateTime(),
				'fallos'=>$_GET['fallos'],'porcentaje'=>$_GET['porcentaje'],'tiempo'=>$_GET['tiempo']);
		}*/
	}
}
?>