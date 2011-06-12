<?php
require_once("clases/clases.php");
class HTMLgenerator{
private $pagina; 
private $charset;
private $content_type;

function __construct($page,$charset='UTF-8',$content_type='application/xhtml+xml'){
	$this->pagina = $page;
	$this->charset = $charset;
	$this->content_type=$content_type;
}

function generate(){
/*if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) 
    header("Content-Type: application/xhtml+xml; charset=".$this->charset);
else header("Content-Type: text/html; charset=ISO-8859-1");*/

header('Content-Type: '.$this->content_type.'; charset='.$this->charset);
$css='';
foreach ($this->pagina->getCSS() as $value)
	$css .= '
		<link rel="StyleSheet" type="text/css" media="'.$value[1].'" href="'.base_url().$value[0].'"/>';
		
$script='';
foreach ($this->pagina->getScripts() as $value)
	$script .= '
		<script type="text/javascript" src="'.base_url().$value.'"></script>';
		
	return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml11.dtd"  >
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" xmlns:fb="http://www.facebook.com/2008/fbml"
xmlns:svg="http://www.w3.org/2000/svg">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset='.$this->charset.'" />
		<meta name="keywords" content="'.$this->pagina->getKeyWords().'"/>
		<meta name="description" content="'.$this->pagina->getMetaContent().'" />
		<meta name="robots" content="all"/>
		<!-- CSS Import -->'.$css.$script.'
		<title>'.$this->pagina->getTitle().'</title>
		<link rel="shortcut icon" href="'.base_url().'img/favicon.ico"/>
	</head>
	<body>
	'.$this->makeContent($this->pagina).'
	</body>
</html>';
}

function base_url(){
	return '';
}

function makeContent($container){
	$text = '';
	foreach ($container->getComponents() as $value)
		$text.=$this->makeObject($value);
	return $text;
}

function makeObject($element){
	$result = '';
	if(!$element instanceof vStyleElement) return htmlspecialchars($element);
	else if($element instanceof vComponent) return $this->makeObject($element->getComponent());
	else{
		$func='make_'.get_class($element);
		return $this->$func($element);
	}
}

function make_vPreformatedText($text){
	return $text->text;
}

function make_vInputCheckBox($input){
	return '
		<div class="checkbox field'.($input->getClass()?' '.$input->getClass():'').'">
			<label for="'.$input->getId().'"><input type="checkbox" id="'.$input->getId().'" name="'.$input->getId().'"'.
				($input->getValue()?' checked="on"':'').
			'/>
			'.$input->getLabel().'</label>
		</div>';
}

function make_vInputOptions($input){
	$options='';
	foreach($input->getOptions() as $key=>$value)
		$options.='<option value="'.$key.'" '.($input->getValue()==$key?'selected="selected"':'').'>'.htmlspecialchars($value).'</option>';
	return '
		<div class="field'.($input->getClass()?' '.$input->getClass():'').'">
			<label for="'.$input->getId().'">'.htmlspecialchars($input->getLabel()).'</label>
			<select id="'.$input->getId().'" name="'.$input->getId().'" >'.
				$options.
			'</select>
		</div>';
}

function make_vInputText($input){
	if($input->getMaxLength()) return '
		<div class="field'.($input->getClass()?' '.$input->getClass():'').'">
			<label for="'.$input->getId().'">'.htmlspecialchars($input->getLabel()).'</label>
			<input type="text" id="'.$input->getId().'" name="'.$input->getId().'"'.
				($input->getValue()!==false?' value="'.htmlspecialchars($input->getValue()).'"':'').
				($input->getMaxLength()?' maxlength="'.$input->getMaxLength().'"':'').
			'/>
		</div>';
	else return '
		<div class="field'.($input->getClass()?' '.$input->getClass():'').'">
			<label for="'.$input->getId().'">'.htmlspecialchars($input->getLabel()).'</label>
			<textarea id="'.$input->getId().'" name="'.$input->getId().'" >'.
				($input->getValue()!==false?htmlspecialchars($input->getValue()):'').
			'</textarea>
		</div>';
}

function make_vInputHidden($input){
	return '
		<div>
			<input type="hidden" id="'.$input->getId().'" name="'.$input->getId().
				'" value="'.htmlspecialchars($input->getValue()).'"/>
		</div>';
}

function make_vInputFile($input){
	return '
		<div class="field field_file">
			<label for="'.$input->getId().'">'.$input->getLabel().'</label>
			<input type="file" id="'.$input->getId().'" name="'.$input->getId().
			'"/>
		</div>';
}

function make_vInputPassword($input){
	return '
		<div class="field field_password">
			<label for="'.$input->getId().'">'.$input->getLabel().'</label>
			<input type="password" id="'.$input->getId().'" name="'.$input->getId().
			'"/>
		</div>';
}

function make_vForm($element){
	$buttons='';
	foreach($element->getButtons() as $key=>$button)
		$buttons.='<input type="button" id="'.$key.'" value="'.$button.'"/>';
	return '<form '.$this->setStyle($element).' action = "' .$element->getAction().'" method = "'.$element->getMethod().'"'.
		($element->getTarget()?' target="'.$element->getTarget().'"':'').
		($element->getEncType()?' enctype = "'.$element->getEncType().'"':'').'><fieldset>'.
		$this->makeContent($element).'<div class="buttons"><input type="submit" value="'.
		$element->getSubmitText().'"/>'.$buttons.'</div></fieldset></form>';
}

function make_vLink($element){
	return '<a '.$this->setStyle($element).
		' href="'.htmlspecialchars($element->getDirection()).'" title="'.$element->getTitle().'" ><span>'.
		$this->makeContent($element).'</span></a>';
}

function make_vList($element){
	$result='';
	foreach($element->getElements() as $value)
		$result .= $this->makeElement('li',$value);
		
	return '<ul'.$this->setStyle($element).'>'.$result.'</ul>';
}

function setStyle($element){
	return ($element->getClass()?' class="'.$element->getClass().'"':'').
		($element->getId()?' id="'.$element->getId().'"':'').
		($element->getName()?' name="'.$element->getName().'"':'');
}

function make_vImage($element){
	list($width,$height)=getimagesize($element->getFile());
	return '<img'.$this->setStyle($element).' width="'.$width.'" height="'.$height.'" alt="'.$element->getAltText().'" src="'.$element->getFile().'" />
	';
}

function make_vContainer($element){
return $this->makeContent($element);
}

function make_vTable($table){
	$t='';
	if(count($table->getHeaders())){
		$t = '<tr>';
		foreach($table->getHeaders() as $value)
			$t .= '<th>' .$this->makeObject($value). '</th>';
		$t .= '</tr>';
	}
	
	foreach($table->getRows() as $row){
		$t .= '<tr'.($row->getClass()?' class="'.$row->getClass().'"':'').'>';
		foreach($row->getElements() as $value)
			$t .= '<td'.$this->setStyle($value).'>'.$this->makeContent($value).'</td>';
		$t .= '</tr>';
	}
	
	return '
	<table '.$this->setStyle($table).'><tbody>'.$t.'
	</tbody></table>';
}

	private $header_level=1;

function make_vSection($component){
	$this->header_level++;
	$result=$this->makeElement('div',$component);
	$this->header_level--;
	return $result;
}

function make_vHeader($component){
	return $this->makeElement('h'.$this->header_level,$component);
}

function make_vDiv($component){
	return $this->makeElement('div',$component);
}

function make_vSpan($component){
	return $this->makeElement('span',$component);
}

function make_vParagraph($component){
	return $this->makeElement('p',$component);
}

function makeElement($tag,$element){
	return '<'.$tag.
		$this->setStyle($element).
		'>'.$this->makeContent($element).'</'.$tag.'>';
}
}
?>
