<?php

$config = CMap::mergeArray(
    require(__DIR__.'/../pro/console.php'),
    array()

);
return $config;