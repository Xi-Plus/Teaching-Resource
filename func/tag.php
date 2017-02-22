<?php

$sth = $G["db"]->prepare("SELECT `tag` FROM `plan`");
$sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$D['tag'] = array();
foreach ($row as $temp) {
	$temp = json_decode($temp['tag']);
	foreach ($temp as $tag) {
		if (!isset($D['tag'][$tag])) {
			$D['tag'][$tag]=0;
		}
		$D['tag'][$tag]++;
	}
}

?>
