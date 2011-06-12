<?php
/*Clase base para los componentes de vista
*
*De esta clase heredan los objetos que funcionarancomo componentes propios
*@author Fidel Díez Díaz
*@author Jaime García Marsá <jaime@legendarya.com>
*@author Daniel Vallina Sordo <daniel_vallina@telefonica.net>
*@version 1.0
*/
require_once('vComponent.php');

class vPagination extends vComponent{

	/**
	*Constructor de la clase vPagination
	*
	*@param int $pages numero de paginas que tendra el indice
	*@param  int $selected Numero del elemento seleccionado
	*@param string $dir 
	*@param string $class Class del elemento
	*@param string $id Id del elemento
	*/
	function __construct($pages,$selected,$dir,$class='',$id=''){
		parent::__construct($list=new vMenu($class),$class,$id);
		for($i=0;$i<$pages;$i++)
			$list->add(''.($i+1),$dir.$i,$i==$selected);
	}
}
?>