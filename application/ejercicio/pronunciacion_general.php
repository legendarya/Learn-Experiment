<?php
class UrlController extends ProcessTemplate{
	
	function execute($idioma,$palabra=null){
		$idioma=new Idioma($idioma);
		// Crear un nuevo recurso cURL
		$ch = curl_init();
		
		// Establecer URL y otras opciones apropiadas
		curl_setopt($ch, CURLOPT_URL, 'http://apifree.forvo.com/key/'.getConfig('forvo_api').'/format/xml/action/word-pronunciations/word/'.
				$palabra.'/language/'.$idioma->codigo.'/order/rate-desc');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 11);
		
		header('Content-Type: text/xml;');
		if(!$val=curl_exec($ch)){
			echo '<?xml version="1.0" encoding="UTF-8"?>
				<sound palabra="'.$palabra.'" mp3="" ogg="" />';
			exit;
		}

		// Capturar la URL y pasarla al navegador
		$doc = new SimpleXmlElement($val, LIBXML_NOCDATA);
		
		// Cerrar el recurso cURL y liberar recursos del sistema
		curl_close($ch);
		
		echo '<?xml version="1.0" encoding="UTF-8"?>
		<sound palabra="'.$palabra.'" mp3="'.$doc->item->pathmp3[0].'" ogg="'.$doc->item->pathogg[0].'" />';
	}
}
?>