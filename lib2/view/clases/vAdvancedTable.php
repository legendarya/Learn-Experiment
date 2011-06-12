<?php
require_once('vComponent.php');

class vAdvancedTable extends vComponent{
	private $columnClasses;

	function __construct(){
		parent::__construct(new vTable());
	}
	
	function addHeader($title,$columnClass=''){
		$this->component->addHeader(new vSpan($title));
		$this->columnClasses[]=$columnClass;
	}
	
	function add($cells,$class=''){
		$row=$this->component->add();
		$row->setClass($class);
	
		foreach($cells as $i=>$cell)
			$row->add(new vSpan($cell),$this->columnClasses[$i]);
	}
}
?>