<?php
/**
*Clase del componente menu
*
*Clase propia de un componente menu
*@author Fidel Díez Díaz
*@author Jaime García Marsá <jaime@legendarya.com>
*@version 1.0
*@pakage ComponentsMenu
*/
require_once('vComponent.php');

class vInputTextarea extends vComponent{
	
	/**
	*Constructor de la clase vMenu
	*
	*@param string $class Class del elemento
	*@param string $id Id del elemento
	*/
	function __construct($id, $label, $value='',$class=''){
		parent::__construct(new vInputText($id,$label,$value,$class,null));
	}

	function getMaxLength(){
		return $this->component->maxLength;
	}
	
	function setMaxLength($value){
		$this->component->maxLength = $value;
	}
	
	function setValue($value){
		return $this->component->setValue($value);
	}
	
	function setLabel($label){
		return $this->component->setLabel($label);
	}
}
?>