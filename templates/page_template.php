<?php
require_once 'templates/process_template.php';

class PageTemplate extends ProcessTemplate{
	protected $error='';
	protected $page;
	
	function execute(){
		if(count($_POST)) $this->_callWithParams('_processForm');
		
		$this->page = new vPage($this->_callWithParams('_title').' - Learn Experiment');
		$this->page->addCss('css/index.css');
		
		$container = new vDiv('','mainContainer');
		
		$container->add(new vDiv($this->lateral(),'lateral'));
		$container->add($this->area_usuario());
		$container->add(new vDiv('','bajo_area_usuario'));
		
		$container->add(new vDiv(array(
			((!$this->usuario and isset($_SESSION['ejercicios']) and count($_SESSION['ejercicios']))?
				new vDiv(array('Has creado ',
					new vLink(base_url().'ejercicio/','ejercicios'),
					' temporalmente, ',
					new vLink(base_url().'registro/','regístrate'),
					' o ',
					new vLink(base_url().'identificacion/','identificate'),
					' si quieres que se guarden'),'infoAlert')
			:''),
			$this->_callWithParams('_contenido')
		),'contenido'));
		$container->add(new vPreformatedText('<fb:comments width="966px" xid="'.$this->comentarios_key().'"></fb:comments>'));
		
		
		$container->add($this->pie_de_pagina());
		
		global $config;
		
		$this->page->add(new vContainer(array(
			new vPreformatedText('
				<script src="http://static.ak.connect.facebook.com/connect.php/es_LA" type="text/javascript"></script>
				<script type="text/javascript">FB.init("'.$config['facebook_api'].'", "'.base_url().'xd_receiver.htm",{"reloadIfSessionStateChanged":true});</script>'),
			$container
		)));

if( $_SERVER['SERVER_NAME']!='127.0.0.1')		
		$this->page->add(new vPreformatedText('<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-5154111-3");
pageTracker._trackPageview();
} catch(err) {}</script>'));
		
		$generator=new HTMLRedirectGenerator($this->page,'UTF-8','text/html');
		echo $generator->generate();
	}
	
	function pie_de_pagina(){
		$ejercicios=new Ejercicios();
		$apartados=new Apartados();
		$usuarios=new Usuarios();
		$ultimo=$usuarios->orderBy('fecha_registro',false)->limit(1)->getFirst();
		return new vDiv(array(
			new vDiv(array(
				'Estadísticas: '.$ejercicios->count().' ejercicios, '.
				$apartados->count().' apartados teóricos, '.
				$usuarios->count().' usuarios registrados, '.
				$usuarios->ultima_vez_activoMoreThan(date('Y-m-d H:i:s', strtotime('-1 day',time())))->count().' usuarios hoy, ',
				//$ejercicios->fecha_actualizacionMoreThan(date('Y-m-d H:i:s', strtotime('-1 day',time())))->count().' ejercicios creados hoy, ',
				($ultimo?new vContainer(array('último registrado: ',new vLink(base_url().'usuario/perfil/'.$ultimo->id,$ultimo->nombre_usuario))):'')
			),'estadisticas'),
			new vDiv(new vLink('http://www.openwebdeveloper.com/aprender/',new vImage('img/miniLE_old.jpg'),'Learn Experiment 1.0.'),'banner'),
			new vDiv(new vLink('http://gengotales.com/',new vImage('img/miniGT2.jpg'),'Gengo Tales. Aprende mediante historias interactivas.'),'banner'),
			new vDiv(new vPreformatedText('<a href="http://creativecommons.org/licenses/by/3.0/" rel="license">
					<img src="http://i.creativecommons.org/l/by/3.0/88x31.png" style="border-width: 0pt;" alt="Creative Commons License">
				</a>')
			,'ccLicense')
		),'pie_de_pagina');
	}
	
	function area_usuario(){
		if($this->usuario){
			return new vDiv(array(
				'Usuario:',
				new vLink(base_url().'usuario/perfil/',$this->usuario->nombre_usuario),' |',
				//new vLink(base_url().'registro/','mensajes'),
				new vLink(base_url().'mensajes/','mensajes'.
					(($no_leidos=$this->usuario->mensajesRecibidos->leidoIs(0)->count())?' ('.$no_leidos.')':'')),' |',
				new vLink(base_url().'ejercicio/','ejercicios'),
				new vLink(base_url().'closeSession/',new vImage('img/cerrar.png','Cerrar sesión'))
			),'area_usuario');
		}
	
		$form=new vForm(array(
			new vInputText('user','Usuario:'),
			new vInputPassword('pass','Contraseña:'),
			new vInputCheckBox('recordar','recuerdame'),
			new vInputHidden('url',isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:'')
		),base_url().'identificacion/');
		
		$form->setSubmitText('Entrar');
	
		return new vDiv(array(
			$form,
			new vLink(base_url().'registro/','nuevo usuario')
		),'area_usuario');
	}
	
	function muestraNombreUsuario($mensaje){
		return ($mensaje->usuario?new vLink(base_url().'usuario/perfil/'.$mensaje->usuario->id,$mensaje->usuario->nombre_usuario):$mensaje->nombre);
	}
	
	function recortaTexto($texto,$longitud){
		$texto=trim($texto);
		if($longitud>=strlen($texto)) return $texto;
		$result=mb_substr($texto,0,$longitud);
		if($texto[$longitud-1]!=' ') $result=mb_substr($result,0,strrpos($result,' '));
		return trim($result).'...';
	}
	
	function lateral(){
		$menu=new vMenu('menu_principal');
		
		$menu->add('Portada',base_url().'.');
		$menu->add('Materias',base_url().'materias/');
		$menu->add('Crear ejercicio',base_url().'ejercicio/crear_abierto');
		$menu->add('Buscar ejercicios',base_url().'ejercicio/buscar');
		$menu->add('Foro',base_url().'foros/');
		$menu->add('Blog','http://aprenderjapones.org');
		$menu->add('Desarrollo',base_url().'utilidades/programacion');
		$menu->add('Contactar',base_url().'utilidades/contactar');
		/*$menu->add('Wiki',base_url().'wiki/');
		$menu->add('Preguntas frecuentes',base_url().'faq/');
		$menu->add('¡Participa!',base_url().'participa/');*/
		/*$menu->add('Programación',base_url().'programacion/');
		if(!$this->usuario){
			$menu->add('Identificación',base_url().'identificacion/');
			$menu->add('Registro',base_url().'registro/');
		}
		else if(!$this->usuario->facebook)
			$menu->add('Cerrar sesión '.$this->usuario->nombre_usuario,base_url().'closeSession/');*/
			
		$ultimos_mensajes=new vDiv('');
		$temas=new Temas();
		foreach($temas->orderBy('fecha',false)->limit(10) as $tema){
			$mensaje=$tema->ultimoMensaje;
			$ultimos_mensajes->add(new vLink(base_url().'foros/tema/'.str2url($tema->titulo).'/'.$tema->id.'/'.(ceil($tema->mensajes->count()/10)-1).'#ultimoMensaje',array(
				new vSpan(array(new vSpan($mensaje->usuario?$mensaje->usuario->nombre_usuario:$mensaje->nombre,'autor'),' '.$this->recortaTexto($mensaje->mensaje,70)),'texto'),
				new vSpan(array(' en ',new vSpan($this->recortaTexto($tema->titulo,30),'titulo')))
			),'','mensaje'.(($this->usuario and (!$temaLeido=$this->usuario->temasLeidos->temaIs($tema)->getFirst() or $temaLeido->fecha->format('U')<$tema->fecha->format('U')))?' temaNoLeido':'')));
		}
			
		return new vContainer(array(
			new vLink(base_url().'.',new vImage('img/logo.png'),'Learn Experiment','logo'),
			$menu,
			new vSection(array(
				new vHeader(new vLink(base_url().'foros/','Foro: últimos mensajes')),
				$ultimos_mensajes,
			),'ultimosMensajes')
			/*((!$this->usuario or !$this->usuario->facebook)?
				new vPreformatedText('<div class="fbLogin"><fb:login-button v="2"><fb:intl>Conectar con Facebook</fb:intl></fb:login-button></div>')
			:''),*/
		));
	}
	
	function comentarios_key(){
		return 'legendaryaPortada';
	}
	
	function arbolContenidos($apartado=0){
		$arbol=new vList();
		if($apartado)
			$apartados=$apartado->subapartados;
		else{
			$apartados=new Apartados();
			$apartados=$apartados->where(new EqualCondition('padre',$apartado));
		}
		
		foreach($apartados as $apartado){
			$ejercicios=new vList('ejercicios_apartado');
			
			foreach($apartado->ejercicios as $ejercicio)
				$ejercicios->add(new vLink(base_url().'aprender/'.$ejercicio->url,$ejercicio->titulo));
				
			$arbol->add(new vContainer(array(
				$apartado->nombre,
				$this->arbolContenidos($apartado),
				$ejercicios
			)));
		}
			
		return $arbol;
	}
}
?>