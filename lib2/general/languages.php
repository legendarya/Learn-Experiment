<?php
// Devuelve el texto internacionalizado de la base de datos, o lanza una excepción si hay algún error
function getInterface($key){
	global $interface;
	if(!isset($interface[$key])) throw new Exception('There is no "'.$key.'" value in interface');
	else return $interface[$key];
}
   
function getLangQval($val){
	if(preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i",$val,$matches))
		return (float)$matches[2];
	else return 1.0;
}

function sort_lang_priority($v1,$v2){
	 return getLangQval($v1)<getLangQval($v2);
}

if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
$http_accept=$_SERVER["HTTP_ACCEPT_LANGUAGE"];

if(isset($http_accept) && strlen($http_accept) > 1)  {
# Split possible languages into array
$x = explode(",",$http_accept);
usort ($x,'sort_lang_priority');

foreach ($x as $val) {
if(file_exists('language/'.substr($val,0,2).'/')){
	$lang=substr($val,0,2);
	break;
}
}
}

foreach(scandir('language/'.$lang) as $file) 
if(substr($file,-4)=='.php') require_once ('language/'.$lang.'/'.$file);

?>