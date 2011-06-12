<?php
require_once("vStyleElement.php");
/**
* Clase base para nuevos componentes definidos por el usuario a partir de otros
*
* Permite definir componentes nuevos a partir de los ya existentes, 
* de manera que no deban ser generados con código específico en "HTMLGenerator".
* Todo lo que requieren es insertar una jerarquía de componentes en la propiedad "component".
* Esta jerarquía será representada por el generador con los métodos ya existentes.
*
* @author Fidel Díez Díaz
* @author Jaime García Marsá <jaime@legendarya.com>
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
	* @param vStyleElement $component Componente o jerarquía de componentes que representan al actual
	* @param string $class Clase por defecto
	* @param string $id Identificador por defecto
	*/
	function __construct(vStyleElement $component,$class='',$id=''){
		$this->component=$component;
		parent::__construct($class, $id);
	}
	
	
	function setClass($class){
		return $this->component->setClass($class);
	}
	
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