<?php
require_once("vContainer.php");

class vPage extends vContainer{

	private $title;
	private $scriptList;
	private $cssList = array();
	private $keywords;
	private $metaContent;

	function __construct($newTitle){
		$this->title = $newTitle;
		$this->cssList = array();
		$this->scriptList = array();
		$this->keywords = array();
	}
	

	function getTitle(){
		return $this->title;
	}

	function setTitle($newTitle){
		$this->title = $newTitle;
	}

	function addScript($script){
		$this->scriptList[]=$script;
	}

	function addCSS($css,$type='all'){
		$this->cssList[]=array($css,$type);
	}

	function getCSS(){
		return $this->cssList;
	}

	function getScripts(){
		return $this->scriptList;
	}

	function getKeyWords(){
		return implode(',',$this->keywords);
	}

	function setKeyWords($newTitle){
		$this->keywords[] = $newTitle;
	}

	function getMetaContent(){
		return $this->metaContent;
	}

	function setMetaContent($newContent){
		$this->metaContent = $newContent;
	}

}
?>
