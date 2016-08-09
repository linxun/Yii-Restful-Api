<?php

$config = CMap::mergeArray(
    require(__DIR__.'/../pro/main.php'),
    array(
        // application components
        'components'=>array(
            'mongodb' => array(
                'class'            => 'EMongoDB',
                'connectionString' => 'mongodb://admin:wode485@10.10.139.169:27017/wode_pro',
                //'connectionString' => 'mongodb://127.0.0.1',
                'dbName'           => 'wode_pro',
                'fsyncFlag'        => true,
                'safeFlag'         => true,
                'useCursor'        => false,
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
        'params'=>array(
            'redis'=>array('host'=>'127.0.0.1', 'port'=>'6379', 'timeout'=>5),
        ),
    )
);

return $config;