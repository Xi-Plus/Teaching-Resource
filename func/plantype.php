<?php

$sth = $G["db"]->prepare('SELECT * FROM `plan_type` ORDER BY `id` ASC');
$sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$D['plantype'] = array();
foreach ($row as $temp) {
	$D['plantype'][$temp['id']] = $temp['name'];
}

?>
