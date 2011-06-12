<?php
require_once 'templates/process_template.php';

class FW_Template extends ProcessTemplate{
	protected $error='';
	protected $page;
	
	function execute(){
		if(count($_POST)) $this->_callWithParams('_processForm');
		
		$this->page = new vPage($this->_callWithParams('_title').' - Framework');
		$this->page->addCss('css/index.css');
		$this->page->addScript('js/jquery.js');
		$this->page->addScript('js/utilidades.js');
		
		$this->page->add($this->_callWithParams('_contenido'));
		
		$this->page->add(new vPreformatedText('<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-5154111-14");
pageTracker._trackPageview();
} catch(err) {}</script>'));
		
		$generator=new HTMLRedirectGenerator($this->page);
		
		echo $generator->generate();
	}
}
?>