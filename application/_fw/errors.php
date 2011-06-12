<?php
require_once 'templates/framework_template.php';

class UrlController extends FW_Template{
	function _contenido(){
		if(!isset($_SESSION['FW_id'])) redirect(base_url());
	
		$result = new vTable(array('Time','Message','File','Line'));
		
		$errors=new FW_Errors();
		foreach($errors->orderBy('time',false)->limit(30) as $error){
			$result->add(array($error->time->format('H:i d/m'),$error->message,$error->file,$error->line));
		}
		
		return $result;
	}
	
	function _title(){
		return 'Errors';
	}
}
?>