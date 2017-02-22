<?php

$sth = $G["db"]->prepare('SELECT * FROM `account` ORDER BY `account`');
$sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$D['account'] = array();
foreach ($row as $temp) {
	$D['account'][$temp['account']] = $temp;
}

?>
