<?php
require_once 'lib/general/error_control.php';
require_once 'lib/general/functions.php';
require_once 'lib/general/languages.php';
require_once 'lib/view/HTMLRedirectGenerator.php';
require_once 'language/es/general.php';
require_once 'lib/dao/persistent.php';
require_once 'lib/model.php';
require_once 'model/general.php';


date_default_timezone_set('Europe/Madrid');

session_start();

// Si  no existe el archivo de configuración, lo creamos
if(!file_exists('config.php')) require_once 'lib/general/setup_config.php';

// Conectamos con la base de datos
require_once 'config.php';
if (!($link=mysql_connect($config['db_server'],$config['db_user'],$config['db_password'])))
  trigger_error('Error conectando a la base de datos.');
if (!mysql_select_db($config['db_database'])) trigger_error('Error seleccionando la base de datos.',E_USER_ERROR);


mysql_query("SET NAMES 'utf8'");

require_once 'redirections.php';

// Obtenemos los segmentos de la URL
if(isset($_SERVER['REDIRECT_URL'])) $url= $_SERVER['REDIRECT_URL'];
else $url= $_SERVER['REQUEST_URI'];

$url=explode('?',$url);
$segments=explode('/',$url[0]);

if( $_SERVER['SERVER_NAME']=='127.0.0.1') $segment_pos=2;
else $segment_pos=1;
$route='application/';
$notfile=1;
$className='';

// Buscamos el archivo al que corresponde la URL
while($segment_pos<count($segments)){
	if(!$segments[$segment_pos]) $segments[$segment_pos]='index';
	if(file_exists($route.$segments[$segment_pos].'.php')){
		$route.=$segments[$segment_pos].'.php';
		$notfile=0;
		$segment_pos++;
		break;
	}
	else if(file_exists($route.$segments[$segment_pos])){
		$route.=$segments[$segment_pos].'/';
	}
	else trigger_error('no existe el archivo o directorio "'.$route.$segments[$segment_pos].'"',E_USER_ERROR);
	$segment_pos++;
}

if($notfile){
	$route.='index.php';
	if(!file_exists($route)) trigger_error('no existe el archivo o directorio "'.$route.'"',E_USER_ERROR);
}

require_once 'templates/page_template.php';
require_once $route;

// Llamamos al método
$params=array_splice($segments,$segment_pos);
call_user_func_array(array(new UrlController($params), 'execute'), $params);
?>