<?php
require_once("vInput.php");

class vInputOptions extends vInput{
	private $options=array();

	function add($value,$text){
		return $this->options[$value]=$text;
	}
	
	function getOptions(){
		return $this->options;
	}
}
?>