<?php
	if(isset($_GET['path'])){
		if(substr($_GET['path'],0,9)=='es/pag409' or substr($_GET['path'],0,9)=='es/pag406' or substr($_GET['path'],0,9)=='es/pag408'){
			if(isset($_GET['tema'])){
				$tema=new Tema($_GET['tema']);
				header("HTTP/1.1 301 Moved Permanently");
				redirect(base_url().'foros/tema/'.str2url($tema->titulo).'/'.$tema->id);
			}
			else{
				header("HTTP/1.1 301 Moved Permanently");
				redirect(base_url().'foros/');
			}
		}
		if(substr($_GET['path'],0,9)=='es/pag422'){
			header("HTTP/1.1 301 Moved Permanently");
			redirect(base_url().'programacion/');
		}
		if(substr($_GET['path'],0,9)=='es/pag455'  and isset($_GET['usuario'])){
			header("HTTP/1.1 301 Moved Permanently");
			redirect(base_url().'usuario/perfil/'.$_GET['usuario']);
		}
		if((substr($_GET['path'],0,9)=='aprender/' or substr($_GET['path'],0,9)=='es/pag424' or substr($_GET['path'],0,9)=='es/pag666') and isset($_GET['leccion'])){
			$ejercicio=new Ejercicio($_GET['leccion']);
			header("HTTP/1.1 301 Moved Permanently");
			redirect(base_url().'aprender/'.str2url($ejercicio->titulo).'/'.$ejercicio->id);
		}
		if(substr($_GET['path'],0,15)=='aprender/curso/' and isset($_GET['apartado'])){
			$apartado=new Apartado($_GET['apartado']);
			header("HTTP/1.1 301 Moved Permanently");
			redirect(base_url().'cursos/'.str2url($apartado->nombre).'/'.$apartado->id);
		}
		if(substr($_GET['path'],0,21)=='learnexperiment/wiki/' or substr($_GET['path'],0,11)=='componente/' or substr($_GET['path'],0,9)=='es/pag405'){
			header("HTTP/1.1 301 Moved Permanently");
			redirect('http://www.learnexperiment.com/');
		}
	}
?>