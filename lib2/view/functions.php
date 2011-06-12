<?php
// Devuelve el texto internacionalizado de la base de datos, o lanza una excepción si hay algún error
function interfaceText($key){
	global $interface;
	if(!isset($interface[$key])) throw new Exception('There is no "'.$key.'" value in interface');
	else return $interface[$key];
}

// Devuelve un parámetro recibido por GET o POST, o null si no se recibió
function getParam($key,$default_value=null){
	if(isset($_POST[$key])) return $_POST[$key];
	else if(isset($_GET[$key])) return $_GET[$key];
	else return $default_value;
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
	else if( $_SERVER['SERVER_NAME']=='127.0.0.1')
		$base_dir=str_repeat('../',substr_count($_SERVER['REDIRECT_URL'],'/')-2);
	else if(substr_count($_SERVER['REDIRECT_URL'],'/')>2) $base_dir=str_repeat('../',substr_count($_SERVER['REDIRECT_URL'],'/')-2);
	else $base_dir='';
	
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
?>