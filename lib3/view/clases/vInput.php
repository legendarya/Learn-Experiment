<?php
/**
*Clase base para todos los elementos input
*
*Contiene los atributos basios que poseen todos los elementos input de html
*@author Fidel Díez Díaz
*@author Jaime García Marsá <jaime@legendarya.com>
*@version 1.0
*@pakage ComponentsMenu
*/

require_once("vStyleElement.php");

class vInput extends vStyleElement{
	private $label;
	private $value;
	
	/**
	*Constructor de la clase vInput
	*
	*@param string $class Class del elemento
	*@param string $id Id del elemento
	*@param string $label Etiqueta asociada al elemento input
	*@param string $value Valor del elemento input
	*@param string $autoload Indica si se cargará el valor recibido como parámetro por defecto (ej: al dar erro un formulario se vuelven a mostrar los valores que escribió el usuario)
	*/
	function __construct($id, $label, $value='', $class='',$autoload=true){
		parent::__construct($class, $id);
		$this->label = $label;
		
		if(!$autoload or ($this->value=getParam($id))===null) 
			$this->value = $value;
	}
	
	/**
	*Devuelve el valor del elemento input
	*
	*@return string Valor del elemento input
	*/
	function getValue(){
		return $this->value;
	}
	
	/**
	*Establece el valor del elemento input
	*
	*@param string $value Valor del elemento input
	*/
	function setValue($value){
		$this->value = $value;
	}

	/**
	*Devuelve el valor de la etiqueta del elemento input
	*
	*@return string Valor de la etiqueta del elemento input
	*/
	function getLabel(){
		return $this->label;
	}
	
	/**
	*Establece el valor del elemento input
	*
	*@param string $value Valor del elemento input
	*/
	function setLabel($value){
		$this->label = $value;
	}
}
?>