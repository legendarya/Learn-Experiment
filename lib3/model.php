<?php
require_once 'model.php';

$entity_fw_usuario =new entity ('_framework_user');
$entity_fw_usuario->addField(new TextField('name'));
$entity_fw_usuario->addField(new PasswordField('password'));
$entity_fw_usuario->addField(new IntegerField('level'));

class FW_User extends Persistent{
	function __construct($condition=null){
		parent::__construct('_framework_user',$condition);
	}
}

class FW_Users extends PersistentList{
	function __construct(){
		$this->table='_framework_user';
	}
}

global $entity_usuario;
$entity_fw_error=new entity ('_framework_error');
$entity_fw_error->addField(new DateField('time'));
$entity_fw_error->addField(new TextField('info'));
$entity_fw_error->addField(new TextField('message'));
$entity_fw_error->addField(new TextField('file'));
$entity_fw_error->addField(new IntegerField('line'));
$entity_fw_error->addField(new TextField('REQUEST_URI'));
$entity_fw_error->addField(new TextField('REMOTE_ADDR'));
$entity_fw_error->addField(new TextField('HTTP_USER_AGENT'));

class FW_Error extends Persistent{
	function __construct($condition=null){
		parent::__construct('_framework_error',$condition);
	}
}

class FW_Errors extends PersistentList{
	function __construct(){
		$this->table='_framework_error';
	}
}

$entity_fw_css_version=new entity ('_framework_css_version');
$entity_fw_css_version->addField(new TextField('name'));
$entity_fw_css_version->addField(new IntegerField('state'));
$entity_fw_css_version->addField(new DateField('date'));

$entity_fw_css_version->addSet('subversions',$entity_fw_css_version,'parent');

class FW_CssVersion extends Persistent{
	function __construct($condition=null){
		parent::__construct('_framework_css_version',$condition);
	}
}

class FW_CssVersions extends PersistentList{
	function __construct(){
		$this->table='_framework_css_version';
	}
}

$entity_fw_css_group=new entity ('_framework_css_group');
$entity_fw_css_group->addField(new TextField('name'));

$entity_fw_css_group->addList('groups',$entity_fw_css_group,'parent');
$entity_fw_css_version->addSet('groups',$entity_fw_css_group,'version');

class FW_CssGroup extends Persistent{
	function __construct($condition=null){
		parent::__construct('_framework_css_group',$condition);
	}
}

class FW_CssGroups extends PersistentList{
	function __construct(){
		$this->table='_framework_css_group';
	}
}
?>