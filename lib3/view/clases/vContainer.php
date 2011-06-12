<?php
require_once("vStyleElement.php");

class vContainer extends vStyleElement{
	private $componentList = array();

	function __construct($contents=array(),$class='',$id=''){
		parent::__construct($class,$id);
		if(is_array($contents))
			foreach($contents as $c)
				$this->add($c);
		else $this->add($contents);
	}
	
	function getComponents(){
		return $this->componentList;
	}

	function add($component,$pos=null){
		if(is_array($component) or (is_object($component) and !$component instanceof vStyleElement))
			throw new Exception('The component must be a text or an vStyleElement');
		
		if($pos===null)
			$this->componentList[] = $component;
		else array_splice  ($this->componentList,  $pos,  0, array($component));
	}
}
?>