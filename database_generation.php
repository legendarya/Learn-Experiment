<?php		
//set_time_limit(0);
foreach(preg_split('/;[\n\r]+/',file_get_contents ("db.sql")) as $v)
	if (trim($v)!="") query($v);
?>