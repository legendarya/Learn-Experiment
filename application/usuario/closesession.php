<?php
class UrlController extends PageTemplate{
	function _contenido(){
			define("SESSION_TIME",60*60*24*100);
				
		$_SESSION['id']=null;
		if(isset($_COOKIE['ideasp2p_name'])){
		   setcookie("ideasp2p_name", "", time()-SESSION_TIME, "/");
		   setcookie("ideasp2p_pass", "", time()-SESSION_TIME, "/");
		}
		redirect(base_url());
		exit;
	}
	
	function _title(){
		return 'Cerrando sesión';
	}
}
?>