<?php

$C["path"] = '/teachres';
$C["sitename"] = '公民科教學資源平台';
$C["titlename"] = '公民教學資源';

$C["DBhost"] = 'localhost';
$C["DBuser"] = 'user';
$C["DBpass"] = 'pass';
$C["DBname"] = 'dbname';

$C["FilenameReserved"] = '\/:*?"<>|';
$C["FilenamePattern"] = '[^';
foreach (str_split($C["FilenameReserved"]) as $char) {
	$C["FilenamePattern"] .= '\x'.sprintf("%x", ord($char));
}
$C["FilenamePattern"] .= ']+';
$C["FilenameTitle"] = "不可包含以下字元: ".htmlentities(implode(" ", str_split($C["FilenameReserved"])));

$G["db"] = new PDO ('mysql:host='.$C["DBhost"].';dbname='.$C["DBname"].';charset=utf8', $C["DBuser"], $C["DBpass"]);
$G["schoolyear"] = date("Y")-1911-(date("m")<=8);
$G["inuse"] = array("隱藏", "顯示");
