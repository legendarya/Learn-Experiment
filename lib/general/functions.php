<?php

// Devuelve un parámetro recibido por GET o POST, o null si no se recibió
function getParam($key,$default_value=null){
	if(isset($_POST[$key])) return $_POST[$key];
	else if(isset($_GET[$key])) return $_GET[$key];
	else return $default_value;
}

// Devuelve el valor de una opción de configuración, o null si no está definida
function getConfig($key,$default_value=null){
	global $config;
	if(isset($config[$key])) return $config[$key];
	return $default_value;
}

// Redirecciona a otra página
function redirect($dir){
	header('Location:'.$dir);
	exit;
}

// Devuelve a la página actual con los parámetros recibidos y el parámetro error
function formError($error,$dir=''){
	$params='';
	foreach($_POST as $key=>$value)
		$params.='&'.$key.'='.$value;
	
	redirect($dir.'?error='.$error.$params);
}

function base_url(){
	if(!isset($_SERVER['REDIRECT_URL'])) $base_dir='';
	else if( $_SERVER['SERVER_NAME']=='127.0.0.1' or $_SERVER['SERVER_NAME']=='www.legendarya.com' or $_SERVER['SERVER_NAME']=='legendarya.com')
		$base_dir=str_repeat('../',substr_count($_SERVER['REDIRECT_URL'],'/')-2);
	else if(substr_count($_SERVER['REDIRECT_URL'],'/')>1) $base_dir=str_repeat('../',substr_count($_SERVER['REDIRECT_URL'],'/')-1);
	else $base_dir='';
	
	return $base_dir;
}

function absolute_base_url(){
	if(!isset($_SERVER['REDIRECT_URL'])) $base_dir='http://'.$_SERVER['SERVER_NAME'];
	else if( $_SERVER['SERVER_NAME']=='127.0.0.1' or $_SERVER['SERVER_NAME']=='www.legendarya.com' or $_SERVER['SERVER_NAME']=='legendarya.com'){
		if(isset($_SERVER['REDIRECT_URL'])) $url= $_SERVER['REDIRECT_URL'];
		else $url= $_SERVER['REQUEST_URI'];
		
		$url=explode('?',$url);
		$segments=explode('/',$url[0]);
	
		$base_dir='http://'.$_SERVER['SERVER_NAME'].'/'.$segments[1].'/';
	}
	else $base_dir='http://'.$_SERVER['SERVER_NAME'];
	
	return $base_dir;
}


// Función que convierte un campo de texto en una url amigable, quitando caracteres no permitidos
function str2url($texto){ 
	$arr_busca = array('.',' ','á','à','â','ã','ª','Á','À', 'Â','Ã','é','è','ê','É','È','Ê','í','ì','î','Í','Ì','Î','ò','ó','ô','õ','º','Ó','Ò','Ô','Õ','ú','ù','û','Ú','Ù','Û','ç','Ç','Ñ','ñ'); 
	$arr_susti = array( '','-','a','a','a','a','a','A','A', 'A','A','e','e','e','E','E','E','i','i','i','I','I','I','o','o','o','o','o','O','O','O','O','u','u','u','U','U','U','c','C','N','n');
	$texto = trim(str_replace($arr_busca, $arr_susti, $texto)); 
	
	$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
	$repl = array('', '-', '');
	
	return strtolower(preg_replace($find, $repl, strtolower($texto))); 
}


function recortaTexto($texto,$longitud){
	$texto=trim($texto);
	if($longitud>=strlen($texto)) return $texto;
	$result=mb_substr($texto,0,$longitud);
	if($texto[$longitud-1]!=' ') $result=mb_substr($result,0,strrpos($result,' '));
	return trim($result).'...';
}
?>