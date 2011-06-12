<?php
function error_handler($errno, $errstr, $errfile, $errline){
	
	$error= '
	<div style="padding:0.5em;border:1px solid black;">
		<div style="color:#900;font-size:1.2em">'.$errstr.'</div>
		<div>'.$errline.' - '.$errfile.'</div>
	</div>';
	
    switch ($errno) {
    case E_USER_ERROR:
    case E_USER_WARNING:
    case E_USER_NOTICE:
		errorReport($error,$errstr,$errfile,$errline);
        echo $error;
    default:
		errorReport($error,$errstr,$errfile,$errline,false);
        echo $error;
        break;
    }

    return false;
}

function exception_handler($exception){
	$lines='';
	foreach($exception->getTrace() as $line) if(isset($line['line']))
		$lines.='<li>'.$line['line'].' - '.$line['file'].'</li>';
		
	$error='
	<div style="padding:0.5em;border:1px solid black;">
		<div style="color:#900;font-size:1.2em">'.$exception->getMessage().' ('.$exception->getLine().') on '.$exception->getFile().'</div>
		<ul>
		'.$lines.'
		</ul>
	</div>';

	errorReport($error,$exception->getMessage(),$exception->getFile(),$exception->getLine());
	
	echo $error;
	exit;
}

function errorReport($info,$message,$file,$line,$end=true){
	if(getConfig('database')){
		$error= new FW_Error();
		$error->time=new DateTime();
		$error->info=$info;
		$error->message=$message;
		$error->file=$file;
		$error->line=$line;
		if(isset($_SERVER['REQUEST_URI'])) $error->REQUEST_URI=$_SERVER['REQUEST_URI'];
		if(isset($_SERVER['REMOTE_ADDR'])) $error->REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
		if(isset($_SERVER['HTTP_USER_AGENT'])) $error->HTTP_USER_AGENT=$_SERVER['HTTP_USER_AGENT'];
		$error->save();
	}

	if( $_SERVER['SERVER_NAME']!='127.0.0.1'){
		/*include_once("lib/mail/class.phpmailer.php");

		$error.=nl2br(print_r($_SERVER,true));
		
		$mail             = new PHPMailer();
		$mail->From       = "error@learnexperiment.com";
		$mail->FromName   = "Learn Experiment";
		$mail->Subject    = utf8_decode("Error Report");
		$mail->AltBody    = strip_tags ($error);
		$mail->MsgHTML($error);
		$mail->WordWrap   = 50; // set word wrap
		
		$mail->AddAddress('jaime@legendarya.com',"");

		$mail->Send();*/
		/*if($end){
			header('location:'.getConfig('url'));
			exit;
		}*/
	}
}

//set_error_handler('error_handler');
//set_exception_handler('exception_handler');
?>