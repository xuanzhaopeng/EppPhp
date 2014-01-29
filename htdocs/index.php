<?php
ini_set('session.use_trans_sid', 0);
define('APP_NAME', 'App');
define('APP_PATH', './App/');
define('APP_DEBUG',False);

include("Mobile_Detect.php");
$detect = new Mobile_Detect();

if ($detect->isMobile()) {
	define('GROUP_NAME','Home');
	define('TMPL_PATH', APP_PATH.'/Mtpl');
}

require( "./App/ThinkPHP/ThinkPHP.php");
