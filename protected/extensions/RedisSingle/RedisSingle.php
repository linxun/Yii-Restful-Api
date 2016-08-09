<?php
/**
 * Redis connection singleton
 * Author: kexianbing
 * Date: 2014-04-04
 */

class RedisSingle
{
    public $server;
    private static $_connection;
    private function __construct() {}
    public static function connection()
                           {if (self::$_connection===null)
                               {self::$_connection=new Redis();
                                $conf=Yii::app()->getParams()->redis;
                                self::$_connection->connect($conf['host'], $conf['port'], $conf['timeout']);
                               }
                            return self::$_connection;
                           }
}