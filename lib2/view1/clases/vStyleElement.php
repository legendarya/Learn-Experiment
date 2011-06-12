<?php
/**
*Clase base para el estilo de los componentes de vista
*
*De esta clase heredan todos los objetos usados para describir la vista de una aplicación. Aporta los atributos de estilo "class" e "id".
*@author Fidel Díez Díaz
*@author Jaime García Marsá <jaime@legendarya.com>
*@author Daniel Vallina Sordo <daniel_vallina@telefonica.net>
*@version 1.0
*@pakage ViewComponents
*/
abstract class vStyleElement{
	private $id;
	private $class;

	/**
	*Constructor de la clase vStyleElement
	*
	*Recibira como parametros el "class" y el "id" que se podra establecer en cualquier clase que herede de esta
	*@param string $class Class asignada al elemento
	*@param string $id Id asignado al elemento
	*/
	function __construct($class='',$id=''){
		$this->class = $class;
		$this->id = $id;
	}

	/**
	*Devuelve la clase del elemento
	*
	*@return string Nombre de la clase del elemento
	*/
	function getClass(){
		return $this->class;
	}
	
	/**
	*Establece el valor del class del elemento
	*
	*@param string $value Valor a introducir en el class del elemento para darle estilo css
	*/
	function setClass($value){
		$this->class = $value;
	}

	/**
	*Recupera el id del elemento
	*
	*@return string Identificador del elemento
	*/
	function getId(){
		return $this->id;
	}
	
	/**
	*Establece el valor del id del elemento
	*
	*@param string $value Valor a introducir en el id del elemento
	*/
	function setId($value){
		$this->id = $value;
	}
}
?>