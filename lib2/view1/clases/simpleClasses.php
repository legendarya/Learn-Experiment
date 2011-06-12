<?php
require_once("vContainer.php");

class vParagraph extends vContainer{}
class vHeader extends vContainer{}
class vSection extends vContainer{}
class vDiv extends vContainer{}
class vSpan extends vContainer{}

class vPreformatedText extends vStyleElement{
	public $text='';

	function __construct($text){
		$this->text=$text;
	}
}
?>