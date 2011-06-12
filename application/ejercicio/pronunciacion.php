<?php
class UrlController extends ProcessTemplate{
	
	function execute($idioma,$palabra=null){
		$idioma=new Idioma($idioma);
		header('Content-Type: text/xml');
// Crear un nuevo recurso cURL
$ch = curl_init();

// Establecer URL y otras opciones apropiadas
curl_setopt($ch, CURLOPT_URL, 'http://apifree.forvo.com/key/6e520862bf973bee43d5ddd50f3d79bc/format/xml/action/word-pronunciations/word/'.$palabra.'/language/'+$idioma->codigo);
curl_setopt($ch, CURLOPT_HEADER, 0);

// Capturar la URL y pasarla al navegador
curl_exec($ch);

// Cerrar el recurso cURL y liberar recursos del sistema
curl_close($ch);
	}
}
?>