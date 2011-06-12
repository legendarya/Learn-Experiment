<?php
require_once("vContainer.php");

class vList extends vStyleElement{
	private $elements=array();
	
	function __construct($list='',$class='',$id=''){
		//  Itroduzco un primer parámetro "list" para precargar elementos, pero como antes no lo había, si no se usa un array como primer parámetro, mantenemos el comportamiento anterior
		if(is_array($list)){
			parent::__construct($class,$id);
			foreach($list as $item) $this->add($item);
		}
		else parent::__construct($list,$class);
	}

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