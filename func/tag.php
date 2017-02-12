<?php

$sth = $G["db"]->prepare("SELECT `tag` FROM `plan`");
$sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$taglist = array();
foreach ($row as $temp) {
	$temp = json_decode($temp['tag']);
	foreach ($temp as $tag) {
		if (!isset($taglist[$tag])) {
			$taglist[$tag]=0;
		}
		$taglist[$tag]++;
	}
}
$D['tag'] = $taglist;
unset($taglist);

?>
