<!DOCTYPE html>
<?php
require('config/config.php');
$showform = true;
$fileid = $_GET['id'] ?? "";
$sth = $G["db"]->prepare("SELECT * FROM `file` WHERE `id` = :id");
$sth->bindValue(':id', $fileid);
$sth->execute();
$file=$sth->fetch(PDO::FETCH_ASSOC);
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/檔案資料/<?=($file===false?"找不到":$file['name'])?></title>

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
if ($file === false) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		無此檔案
	</div>
	<?php
	$showform = false;
}
if ($showform) {
?>
<div class="container">
	<h2>檔案詳情</h2>
	<div class="table-responsive">
		<table class="table">
			<tr><td><i class="fa fa-header itemicon" aria-hidden="true"></i> 名稱</td><td><?=htmlentities($file['name'])?></td></tr>
			<tr><td><i class="fa fa-file itemicon" aria-hidden="true"></i> 檔案類型</td><td>
				<?php
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				echo finfo_file($finfo, "file/".$file['filename']);
				finfo_close($finfo);
				?>
			</td></tr>
			<tr><td><i class="fa fa-eye itemicon" aria-hidden="true"></i> 狀態</td><td><?=$G["inuse"][$file['inuse']]?></td></tr>
			<tr><td><i class="fa fa-download itemicon" aria-hidden="true"></i> 下載</td><td>
				<a href="<?=$C["path"]?>/download/<?=$fileid?>/">下載</a>
			</td></tr>
			<tr><td><i class="fa fa-link itemicon" aria-hidden="true"></i> 使用</td><td>
				<?php
				$sth = $G["db"]->prepare("SELECT * FROM `plan` WHERE JSON_CONTAINS(`file`, :file)");
				$sth->bindValue(':file', json_encode([$fileid]));
				$sth->execute();
				$plans = $sth->fetchAll(PDO::FETCH_ASSOC);
				foreach ($plans as $plan) {
					?>
					<a href="<?=$C["path"]?>/plan/<?=$plan["id"]?>/"><?=$plan["name"]?></a><br>
					<?php
				}
				?>
			</td></tr>
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
