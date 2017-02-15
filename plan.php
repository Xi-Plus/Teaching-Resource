<!DOCTYPE html>
<?php
require('config/config.php');
require('func/plantype.php');
$planid = $_GET['id'] ?? "";
$sth = $G["db"]->prepare("SELECT * FROM `plan` WHERE `id` = :id");
$sth->bindValue(':id', $planid);
$sth->execute();
$plan=$sth->fetch(PDO::FETCH_ASSOC);
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/教案資料/<?=($plan===false?"找不到":$plan['name'])?></title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
.itemicon {
	width: 18px;
	text-align: center;
}
</style>

</head>
<body>

<?php
require("header.php");
?>
<div class="container">
	<h2>教案詳情</span></h2>
	<?php
	if ($plan===false) {
		echo "找不到";
	} else {
	?>
	<div class="table-responsive">
		<table class="table">
			<tr><td><i class="fa fa-calendar itemicon" aria-hidden="true"></i> 學年度</td><td><?=$plan['year']?></td></tr>
			<tr><td><i class="fa fa-bookmark itemicon" aria-hidden="true"></i> 分類</td><td><?=$D['plantype'][$plan['type']]?></td></tr>
			<tr><td><i class="fa fa-header itemicon" aria-hidden="true"></i> 標題</td><td><?=htmlentities($plan['name'])?></td></tr>
			<tr><td><i class="fa fa-info itemicon" aria-hidden="true"></i> 說明</td><td><?=str_replace("\n", "<br>", htmlentities($plan['description']))?></td></tr>
			<tr><td><i class="fa fa-tags itemicon" aria-hidden="true"></i> 標籤</td><td><?php
					$plan['tag'] = json_decode($plan['tag'], true);
					foreach ($plan['tag'] as $key => $tag) {
						echo ($key?"、":"")."<mark>".htmlentities($tag)."</mark>";
					}
				?></td></tr>
			<tr><td><i class="fa fa-files-o itemicon" aria-hidden="true"></i> 檔案</td><td><?php
					$plan['file'] = json_decode($plan['file'], true);
					foreach ($plan['file'] as $file) {
						$sthfile = $G["db"]->prepare("SELECT * FROM `file` WHERE `id` = :id");
						$sthfile->bindValue(":id", $file);
						$sthfile->execute();
						$D["file"][$file] = $sthfile->fetch(PDO::FETCH_ASSOC);
						?>
						<a href="<?=$C["path"]?>/file/<?=$file?>/"><?=htmlentities($D["file"][$file]["name"])?></a><br>
						<?php
					}
				?></td></tr>
			<tr><td><i class="fa fa-eye itemicon" aria-hidden="true"></i> 狀態</td><td><?php
				if ($plan["inuse"]) {
				 	?><i class="fa fa-eye" aria-hidden="true"></i><?php
				 } else {
				 	?><i class="fa fa-eye-slash" aria-hidden="true"></i><?php
				 }
				?> <?=$G["inuse"][$plan['inuse']]?></td></tr>
		</table>
	</div>
	<?php
	}
	?>
</div>


<?php
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/4c0a12abc0.js"></script>
</body>
</html>
