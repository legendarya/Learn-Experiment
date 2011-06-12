<?php
class vLink extends vContainer{

	private $direction;
	private $title;

	function __construct($direction, $content, $title='',$class=''){
	
		parent::__construct($content,$class);
		$this->direction = $direction;
		$this->title = $title;
	}

	function getDirection(){
		return $this->direction;
	}

	function setDirection($value){
		$this->direction = $value;
	}

	function getTitle(){
		return $this->title;
	}

	function setTitle($value){
		$this->title = $value;
	}
}
?>