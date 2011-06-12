<?php
require_once("vInput.php");

class vInputCheckBox extends vInput{

	function __construct($id, $label, $value=false,$class=''){
		parent::__construct($id,$label,$value,$class);
	}
}
?>