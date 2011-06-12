<?php

$app_base_config=array(
	array('var'=>'db_server','name'=>interfaceText('setup_database_server'),'default'=>'127.0.0.1'),
	array('var'=>'db_user','name'=>interfaceText('setup_database_user'),'default'=>'root'),
	array('var'=>'db_password','name'=>interfaceText('setup_database_password'),'default'=>''),
	array('var'=>'db_database','name'=>interfaceText('setup_database'),'default'=>'learnexperiment'),
	array('var'=>'forvo_api','name'=>interfaceText('setup_facebook_api'),'default'=>''),
	array('var'=>'facebook_api','name'=>interfaceText('setup_facebook_secret'),'default'=>''),
	array('var'=>'facebook_secret','name'=>'Forvo API:','default'=>'')
);
?>