<?php

$C["path"] = '/teachres';
$C["sitename"] = '公民科教學資源平台';
$C["titlename"] = '公民教學資源';

$C["DBhost"] = 'localhost';
$C["DBuser"] = 'user';
$C["DBpass"] = 'pass';
$C["DBname"] = 'dbname';

$G["db"] = new PDO ('mysql:host='.$C["DBhost"].';dbname='.$C["DBname"].';charset=utf8', $C["DBuser"], $C["DBpass"]);
$G["schoolyear"] = date("Y")-1911-(date("m")<=8);
$G["inuse"] = array("隱藏", "顯示");
