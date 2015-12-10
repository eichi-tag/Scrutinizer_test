<?php

require ("./RedisManager.php");

define('__CONF_PATH__', "./");

ini_set( 'display_errors', 1 );

global $__config;

$__config = parse_ini_file("./config.inc.php", true);
var_dump($__config);

$redisManager = new RedisManager();

$ret = $redisManager->hset("bbtv_100000" , array('all_time'=> 10001 , 'all_update' => date('YmdHis')) , '');
var_dump($ret);
$ret = $redisManager->hget("bbtv_100000" ,'all_time');
var_dump($ret);
$ret = $redisManager->hincrby("bbtv_100000" ,array('all_time'=> -100) );
var_dump($ret);
$ret = $redisManager->hgetall("bbtv_100000" );
var_dump($ret);
$ret = $redisManager->hdel("bbtv_100000" , '' );
var_dump($ret);
$ret = $redisManager->hgetall("bbtv_100000" );
var_dump($ret);
exit;
