<?php
require_once('vComponent.php');

class vTreeMenu extends vComponent{
	function __construct($class='',$id=''){
		parent::__construct(new vList($class,$id));
	}
	
	function add($component,$dir,$selected=false){
		$node = new vTreeMenu();
		$this->component->add(new vContainer(array(
				new vLink($dir,$component),
				$node
			),($selected?'selected':'')));
		return $node;
	}
}
?>