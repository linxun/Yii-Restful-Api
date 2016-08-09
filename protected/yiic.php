<?php
define("RUN_MODE","dev");
if (RUN_MODE === 'dev') {
    define('YII_DEBUG', true);
    define('YII_TRACE_LEVEL', 3);
} elseif (RUN_MODE === 'test') {

} else {

}

// change the following paths if necessary
$yiic=dirname(__FILE__).'/../../yii/yiic.php';
$config=dirname(__FILE__).'/config/'.RUN_MODE.'/console.php';

require_once($yiic);
//Yii::createWebApplication($config)->run();
