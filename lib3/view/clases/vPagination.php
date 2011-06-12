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
		parent::__construct($list=new vList($class),$class,$id);
		if(!$selected) $selected=0;
		if($selected>4){
			$this->addPages($list,$selected,$dir,0,1);
			$list->add(new vSpan('...','ellipsis'));
			if($selected<$pages-5){
				$this->addPages($list,$selected,$dir,$selected-2,$selected+2);
				$list->add(new vSpan('...','ellipsis'));
				$this->addPages($list,$selected,$dir,$pages-2,$pages-1);
			}
			else $this->addPages($list,$selected,$dir,$selected-2,$pages-1);
		}
		else if($selected<$pages-5){
			$this->addPages($list,$selected,$dir,0,$selected+2);
			$list->add(new vSpan('...','ellipsis'));
			$this->addPages($list,$selected,$dir,$pages-2,$pages-1);
		}
		else $this->addPages($list,$selected,$dir,0,$pages-1);
	}
	
	function addPages($list,$selected,$dir,$start,$end){
		for($i=$start;$i<=$end;$i++)
			$list->add(new vContainer(new vLink($dir.$i,''.($i+1)),$i==$selected?'selected':''));
	}
}
?>