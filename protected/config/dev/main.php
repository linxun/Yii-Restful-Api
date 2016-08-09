<?php
/**
 * Created by PhpStorm.
 * User: linxun
 * Date: 15/8/5
 * Time: 下午5:09
 */

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

ini_set("mongo.native_long",1);    //don't set this on 32bit machine, or else long number ouput show error

return array(
    'basePath'=>dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'..',
    'name'=>'rest api',
    'language'=>'en_us',//'zh_cn',
    'timeZone' => 'Asia/Shanghai',
    // preloading 'log' component
    'preload'=>array('log'),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',       #"components" first
        'application.models.common.*',    #"common" second

        'ext.bootstrap.components.Bootstrap',
    ),

    'modules'=>array(
        // uncomment the following to enable the Gii tool

        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'1111',

            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters'=>array('127.0.0.1','::1'),
            'generatorPaths'=>array(
                'ext.YiiMongoDbSuite.gii',
                'ext.dwz.gii'
            ),
        ),
    ),

    // application components
    'components'=>array(
        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>false,
        ),
        'errorHandler' => array(
            'errorAction'=>'error/error',
        ),
        'urlManager'=>array(
            'urlFormat'=>'path',
            "showScriptName"=>false,
            'rules'=>array(
                '/v1/<module:(feeds|users)>'=>'<module>/<module>/rest',
                '/v1/<module:(feeds|users)>/<id:[0-9a-f]+>'=>'<module>/<module>/rest',
            ),
        ),
        'mongodb' => array(
            'class'            => 'EMongoDB',
            'connectionString' => 'mongodb://127.0.0.1',
            'dbName'           => 'mongodb_pro',
            'fsyncFlag'        => true,
            'safeFlag'         => true,
            'useCursor'        => false,
        ),
        'queue' =>  array(
            'class'     =>  'RedisQueue',
            'servers' => array("s1"=>'127.0.0.1:6379',)
        ),
        'bootstrap' => array(
            'class' => 'Bootstrap',
        ),
        /*'db'=>array(
            'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
        ),*/
        // uncomment the following to use a MySQL database

        /*'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=xl',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ),*/

        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),
                // uncomment the following to show log messages on web pages
                /*
                array(
                    'class'=>'CWebLogRoute',
                ),
                */
            ),
        ),
        'memcache' => array(
            'class'        => 'CMemCache',
            'useMemcached' => true,
            'servers'      => array(
                array(
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 60,
                ),
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
    ),
);

