<?php

$sth = $G["db"]->prepare('SELECT * FROM `plan_type`');
$sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$plantype = array();
foreach ($row as $temp) {
	$plantype[$temp['id']] = $temp['name'];
}
$D['plantype'] = $plantype;
unset($plantype);

?>
