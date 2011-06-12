<?php
class vForm extends vContainer{
	private $action;
	private $method;
	private $enctype;
	private $submitText='Ok';
	private $buttons=array();

	//inserta por defecto un boton submit, por lo tanto solo tienes que introducir los componentes del formulario
	function __construct($content=array(),$action='', $method='POST', $enctype=''){
		parent::__construct($content);
		$this->action = $action;
		$this->method = $method;
		$this->enctype = $enctype;
	}
	
	function addButton($id,$text){
		$this->buttons[$id]=$text;
	}

	function getButtons(){
		return $this->buttons;
	}

	function getSubmitText(){
		return $this->submitText;
	}
	
	function setSubmitText($value){
		$this->submitText = $value;
	}

	function getAction(){
		return $this->action;
	}
	
	function setAction($value){
		$this->action = $value;
	}

	function getMethod(){
		return $this->method;
	}
	
	function setMethod($value){
		$this->method = $value;
	}
	
	function getEncType(){
		return $this->enctype;
	}
	
	function setEncType($value){
		$this->enctype = $value;
	}
}