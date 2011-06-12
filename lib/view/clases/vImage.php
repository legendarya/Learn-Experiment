<?php
require_once("vStyleElement.php");

class vImage extends vStyleElement{
	private $file;
	private $altText;
	private $width;
	private $height;
	
	function __construct($file, $altText='', $class='',$id=''){
		parent::__construct($class, $id);
		$this->file = $file;
		$this->altText = $altText;
	}

	function getFile(){
		return $this->file;
	}

	function setFile($value){
		$this->file = $value;
	}

	function getAltText(){
		return $this->altText;
	}

	function setAltText($value){
		$this->altText = $value;
	}

	function getWidth(){
		return $this->width;
	}

	function setWidth($value){
		$this->width = $value;
	}

	function getHeight(){
		return $this->height;
	}

	function setHeight($value){
		$this->height = $value;
	}
}
?>