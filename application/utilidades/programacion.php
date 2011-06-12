<?php
class UrlController extends PageTemplate{
	function _contenido(){
		$this->page->addScript('js/behaviour.js');
		$this->page->addScript('js/ejercicio.js');
		$this->page->addScript('js/ejercicios_adicionales.js');
		$this->page->addScript('js/interfaz.js');
		$this->page->addScript('js/utilidades.js');
		
		$result = new vContainer();
		
		$result->add(new vHeader('Programación'));
		$result->add($contenido = new vSection());
		
		$contenido->add(new vDiv('Learn Experiment es un proyecto de código libre. Si estás interesado en interveir en el proceso de desarrollo de la web te indicamos los pasos necesarios para que puedas comenzar a hacerlo.','adminInfo'));
		$contenido->add(new vHeader(array(new vSpan('1.'),'Configuración del servidor')));
		$contenido->add(new vParagraph('Para poder correr la aplicación en tu ordenador necesitas tener instalado un servidor web, un motor de bases de datos y las librerias del lenguaje que utiliza la página, en este caso php'));
		$contenido->add(new vPreformatedText('<p>Para una instalación sencilla de todo lo anteriormente expuesto existen paquetes todo en uno (como puede ser el caso de <a href="http://www.wampserver.com/dl.php">WAMP</a> o <a href="http://www.easyphp.org/download.php">EasyPHP</a>) que instalan en un solo proceso MySql, php y Apache, es decir, todo lo necesario para la programación web con php.</p>'));
		$contenido->add(new vPreformatedText('<p>Es necesario un pequeño cambio en el archivo httpd.conf de apache. Se debe descomentar la linea <pre>#LoadModule rewrite_module modules/mod_rewrite.so</pre> quitando la almohadilla de delante.<pre>LoadModule rewrite_module modules/mod_rewrite.so</pre></p>'));
		$contenido->add(new vHeader(array(new vSpan('2.'),'Descargar el código fuente')));
		$contenido->add(new vParagraph('Una vez que está todo en marcha debes descargar el fichero zip que contiene el codigo fuente. En su interior hay una carpeta llamada "learnexperiment" que debes descomprimir en el directorio "www" de tu servidor web, que es donde por defecto las diferentes aplicaciones.'));
		$contenido->add(new vLink(base_url().'res/learnexperiment.zip','Descargar código fuente','','codigoFuente'));
		$contenido->add(new vHeader(array(new vSpan('3.'),'Instalar Learn Experiment')));
		$contenido->add(new vParagraph('Este proceso es muy sencillo, tan solo tienes que abrir tu navegador favorito y escribir en la barra de direcciones "http://127.0.0.1/learnexperiment/" o "http://localhost/learnexperiment/".'));
		$contenido->add(new vParagraph('Acto seguido se mostrara un pequeño formulario en el que se pedíra la configuración basica.'));
		$contenido->add(new vImage('img/configScreenShot.png'));
		
		
		return $result;
	}
	
	function _title(){
		return 'Programación';
	}
	
	function comentarios_key(){
		return 'learnexperimentApartadoProgramacion';
	}
}
?>