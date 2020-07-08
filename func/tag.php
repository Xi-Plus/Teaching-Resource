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

// 重大議題，將D[tag]分成兩個陣列(無重要議題,重要議題)

$D['tagNoImportant'] = array();
$D['tagImportant'] = array("性別平等", "人權", "環境", "海洋", "品德", "生命", "法治", "科技", "資訊", "能源", "安全", "防災", "家庭教育", "生涯規劃", "多元文化", "閱讀素養", "戶外教育", "國際教育", "原住民教育");

foreach ($D['tag'] as $tag => $cnt) {
    if (!in_array($tag, $D['tagImportant'])) {
        $D['tagNoImportant'][$tag] = $cnt;
    }
}
