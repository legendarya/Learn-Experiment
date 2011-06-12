<?php
require_once("vStyleElement.php");
/**
* Clase base para nuevos componentes definidos por el usuario a partir de otros
*
* Permite definir componentes nuevos a partir de los ya existentes, 
* de manera que no deban ser generados con c�digo espec�fico en "HTMLGenerator".
* Todo lo que requieren es insertar una jerarqu�a de componentes en la propiedad "component".
* Esta jerarqu�a ser� representada por el generador con los m�todos ya existentes.
*
* @author Fidel D�ez D�az
* @author Jaime Garc�a Mars� <jaime@legendarya.com>
* @author Daniel Vallina Sordo <daniel_vallina@telefonica.net>
* @version 1.0
* @package com.legendarya-id.view.components 
*/

abstract class vComponent extends vStyleElement{
	protected $component;

	/**
	* 
	*
	* Recibe como parametros el componente, el class y el id
	* @param vStyleElement $component Componente o jerarqu�a de componentes que representan al actual
	* @param string $class Clase por defecto
	* @param string $id Identificador por defecto
	*/
	function __construct(vStyleElement $component,$class='',$id=''){
		$this->component=$component;
		parent::__construct($class, $id);
	}
	
	/*__set ($var,$value){
		$metodo="set".uppercase($var[0]).substring($var,1)
		si existe metodo $metodo
			$this->$metodo($value)
	}*/
	
	/**
	*Devuelve el componente
	*
	*@return mixed $component
	*/
	function getComponent(){
		return $this->component;
	}
}
?>