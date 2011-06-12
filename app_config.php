<?php

$app_base_config=array(
	array('var'=>'db_server','name'=>getInterface('setup_database_server'),'default'=>'127.0.0.1'),
	array('var'=>'db_user','name'=>getInterface('setup_database_user'),'default'=>'root'),
	array('var'=>'db_password','name'=>getInterface('setup_database_password'),'default'=>''),
	array('var'=>'db_database','name'=>getInterface('setup_database'),'default'=>'market'),
	array('var'=>'google_analytics_key','name'=>'Clave de Google analytics:','default'=>''),
	array('var'=>'facebook_api','name'=>'Facebook API:','default'=>''),
	array('var'=>'facebook_secret','name'=>'Facebook secret:','default'=>'')
	//array('var'=>'forvo_api','name'=>'Forvo API:','default'=>''),
	//array('var'=>'facebook_api','name'=>getInterface('setup_facebook_api'),'default'=>''),
	//array('var'=>'facebook_secret','name'=>getInterface('setup_facebook_secret'),'default'=>'')
);
?>