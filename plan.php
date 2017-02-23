<!DOCTYPE html>
<?php
require('config/config.php');
require('func/plantype.php');
$showform = true;
$planids = explode(",", $_GET["ids"] ?? "");
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/教案資料</title>

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
$D["plans"]["file"] = array();
foreach ($planids as $planid) {
	$sth = $G["db"]->prepare("SELECT * FROM `plan` WHERE `id` = :id");
	$sth->bindValue(":id", $planid);
	$sth->execute();
	$D["plan"][$planid] = $sth->fetch(PDO::FETCH_ASSOC);
	if ($D["plan"][$planid] === false) {
		?>
		<div class="alert alert-danger" role="alert">
			找不到教案 <?=$planid?>，<a href="<?=$C["path"]?>/manageplans/">請回到列表重新選擇</a>
		</div>
		<?php
		$showform = false;
	} else {
		$D["plan"][$planid]["tag"] = json_decode($D["plan"][$planid]["tag"], true);
		$D["plan"][$planid]["file"] = json_decode($D["plan"][$planid]["file"], true);
		foreach ($D["plan"][$planid]["file"] as $file) {
			if (!in_array($file, $D["plans"]["file"])) {
				$D["plans"]["file"][]= $file;
			}
		}
		foreach ($D["plan"][$planid]["file"] as $file) {
			if (!isset($D["file"][$planid][$file])) {
				$sthfile = $G["db"]->prepare("SELECT * FROM `file` WHERE `id` = :id");
				$sthfile->bindValue(":id", $file);
				$sthfile->execute();
				$D["file"][$file] = $sthfile->fetch(PDO::FETCH_ASSOC);
			}
		}
	}
}
if ($showform) {
?>
<div class="container">
	<h2>教案詳情</span></h2>
	<div class="table-responsive">
		<table class="table">
			<tr><td><i class="fa fa-calendar itemicon" aria-hidden="true"></i> 學年度</td>
				<?php
				foreach ($D["plan"] as $plan) {
					?><td><?=$plan['year']?></td><?php
				}
				?>
			</tr>
			<tr><td><i class="fa fa-bookmark itemicon" aria-hidden="true"></i> 分類</td>
				<?php
				foreach ($D["plan"] as $plan) {
					?><td><?=$D['plantype'][$plan['type']]?></td><?php
				}
				?>
			</tr>
			<tr><td><i class="fa fa-header itemicon" aria-hidden="true"></i> 標題</td>
				<?php
				foreach ($D["plan"] as $plan) {
					?><td><?=htmlentities($plan['name'])?></td><?php
				}
				?>
			</tr>
			<tr><td><i class="fa fa-info itemicon" aria-hidden="true"></i> 說明</td>
				<?php
				foreach ($D["plan"] as $plan) {
					?><td><?=str_replace("\n", "<br>", htmlentities($plan['description']))?></td><?php
				}
				?>
			</tr>
			<tr><td><i class="fa fa-tags itemicon" aria-hidden="true"></i> 標籤</td>
				<?php
				foreach ($D["plan"] as $plan) {
					?><td><?php
						foreach ($plan['tag'] as $key => $tag) {
							echo ($key?"、":"")."<mark>".htmlentities($tag)."</mark>";
						}
					?></td><?php
				}
				?>
			</tr>
			<tr><td><i class="fa fa-files-o itemicon" aria-hidden="true"></i> 檔案</td>
				<?php
				foreach ($D["plan"] as $plan) {
					?><td><?php
						foreach ($plan['file'] as $file) {
							$sthfile = $G["db"]->prepare("SELECT * FROM `file` WHERE `id` = :id");
							$sthfile->bindValue(":id", $file);
							$sthfile->execute();
							$D["file"][$file] = $sthfile->fetch(PDO::FETCH_ASSOC);
							?>
							<a href="<?=$C["path"]?>/file/<?=$file?>/"><?=htmlentities($D["file"][$file]["name"])?></a> <a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/download/<?=$file?>/" role="button">下載</a><br>
							<?php
						}
					?></td><?php
				}
				?>
			</tr>
			<tr><td><i class="fa fa-eye itemicon" aria-hidden="true"></i> 狀態</td>
				<?php
				foreach ($D["plan"] as $plan) {
					?><td><?php
					if ($plan["inuse"]) {
					 	?><i class="fa fa-eye" aria-hidden="true"></i><?php
					} else {
					 	?><i class="fa fa-eye-slash" aria-hidden="true"></i><?php
					}
					echo $G["inuse"][$plan['inuse']];
					?></td><?php
				}
				?>
			</tr>
		</table>
	</div>
</div>


<?php
}
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/4c0a12abc0.js"></script>
</body>
</html>
