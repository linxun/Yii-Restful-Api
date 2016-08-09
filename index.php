<?php
ini_set('error_reporting', E_ALL & ~E_NOTICE); 
ini_set('display_errors', 'On');
define('YII_DEBUG', @$_SERVER['HTTP_DEBUG']?true:false);
//require 'diyism_trace.php';declare(ticks=1);register_tick_function('tick_handler');


define("RUN_MODE","dev");

if (RUN_MODE === 'dev') {
    define('YII_DEBUG', true);
    define('YII_TRACE_LEVEL', 3);
    error_reporting(-1);
    ini_set("display_errors",1);
} elseif (RUN_MODE === 'test') {

} else {

}

// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii/yii.php';
$config=dirname(__FILE__).'/protected/config/'.RUN_MODE.'/main.php';

require_once($yii);
Yii::createWebApplication($config)->run();
