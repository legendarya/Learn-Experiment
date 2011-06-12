<?php
require_once("vStyleElement.php");

class vTable extends vStyleElement{
	private $rows = array();
	private $headers = array();

	function __construct($headers=array(),$class=''){
		parent::__construct($class);
		if(is_array($headers))
			foreach($headers as $c)
				$this->addHeader($c);
		else $this->addHeader($headers);
	}

	function add($element=null){
		if($element===null) return $this->rows[] = new vRow();
		else if($element instanceof vRow)
			$this->rows[] = $element;
		else $this->rows[] = new vRow($element);
	}

	function getRows(){
		return $this->rows;
	}

	function addHeader($element){
		if($element instanceof vContainer)
			$this->headers[] = $element;
		else{
			$container=new vContainer();
			$container->add($element);
			$this->headers[] = $container;
		}
	}

	function getHeaders(){
		return $this->headers;
	}
}

class vRow extends vStyleElement{
	private $elements=array();

	function __construct($elements=array(),$class='',$id=''){
		parent::__construct($class,$id);
		if(is_array($elements))
			foreach($elements as $c)
				$this->add($c);
		else $this->add($elements);
	}
	
	function add($element,$class='',$id=''){
		$container=new vContainer(array(),$class,$id);
		$container->add($element);
		$this->elements[] = $container;
	}

	function getElements(){
		return $this->elements;
	}
}
?>