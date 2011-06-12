<?php
require_once 'app_config.php';

if(!count($_POST)){

	// Mostramos el formulario de configuración
	$pagina = new vPage(getInterface('setup_title'));
	$pagina->addCSS('css/setup.css');
	
	$contenido = new vDiv('','setupPage');
	
	
	$contenido->add(new vHeader(getInterface('setup_title')));
	$contenido->add($form=new vForm(array(
		(getParam('error')?new vDiv(getInterface(getParam('error')),'error'):'')
	)));
	
	foreach($app_base_config as $field)
		$form->add(new vInputText($field['var'],$field['name'],$field['default']));
	
	$form->setSubmitText(getInterface('setup_form_button'));
	
	$pagina->add($contenido);
	
	$generador = new HTMLRedirectGenerator($pagina);
	echo $generador->generate();
	exit;
}
else{
	$link=@mysql_connect(getParam('db_server'),getParam('db_user'),getParam('db_password')) or formError('setup_error_connect');
	
	// Comprobamos
	if(!getParam('db_database')) formError('setup_error_database_required');
	
	query('CREATE DATABASE IF NOT EXISTS '.getParam('db_database').' CHARACTER SET utf8 COLLATE utf8_general_ci;');
   
	if (!mysql_select_db(getParam('db_database'))) formError('setup_error_database');
	
	require_once 'database_generation.php';

	$config_info='';
	foreach($app_base_config as $field)
		$config_info.='$'."config['".$field['var']."']='".getParam($field['var'])."';\n";
		
	$fp = fopen('config.php', 'w');
	fwrite($fp, '<?php
'.$config_info.'
?>');
	fclose($fp);
	redirect('.');
}
exit;
?>