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

class vMenu extends vComponent{
	
	/**
	*Constructor de la clase vMenu
	*
	*@param string $class Class del elemento
	*@param string $id Id del elemento
	*/
	function __construct($class='',$id=''){
		parent::__construct(new vList($class,$id));
	}
	
	/**
	*permite añadir elementos al menu
	*
	*@param $component
	*@param $dir
	*@param $selected
	*/
	function add($component,$dir,$selected=false){
		$this->component->add(new vContainer(new vLink($dir,$component),
			($selected?'selected':'').
			(count($this->component->getElements())?'':' first')
		));
	}
}
?>