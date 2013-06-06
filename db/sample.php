<?php
require_once('mysql.php');
$link = new Mysql('test');
//$link = $mysql->connect('test');

$array = array();
$array[] = '1';

$hello = $link->que("SELECT * from ?_tb", $array);
$result = mysql_fetch_assoc($hello);
//var_dump($result);

var_dump($result);

/*
$a = microtime();
$world1 = $link->push('100_app', array('serial' => '893t892jg2', 'app_id' => '0', 'reg' => date("Y-m-d H:i:s"), 'up' => date("Y-m-d H:i:s"), 'delete_f' => '0'));
print(microtime() - $a);
var_dump($world1);
 */

$a = microtime();
$world3 = $link->push('100_app', array('app_id' => '5'), 3);
print(microtime() - $a);
var_dump($world3);

$a = microtime();
$world2 = $link->pull('100_app', "*", array(1,2,3,4));
print(microtime() - $a);
$world2 = mysql_fetch_assoc($world2);
var_dump($world2);

/*
$a = microtime();
$world4 = $link->pull('100_app', array('a' => 'AAA', 'b' => 'BBB'), 0);
print(microtime() - $a);
var_dump($world4);
*/
