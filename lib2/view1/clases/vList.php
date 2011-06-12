<?php
require_once("vContainer.php");

class vList extends vStyleElement{
	private $elements=array();

	function add($element){
		if(!is_object($element) or get_class($element) != 'vContainer'){
			$container=new vContainer();
			$container->add($element);
			$this->elements[] = $container;
		}
		else $this->elements[] = $element;
	}

	function getElements(){
		return $this->elements;
	}
}
?>