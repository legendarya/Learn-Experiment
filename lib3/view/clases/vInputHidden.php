<?php
require_once("vInput.php");

class vInputHidden extends vInput{

	function __construct($id,$value){
		parent::__construct($id,'',$value);
	}
}
?>