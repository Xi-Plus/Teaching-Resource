<!DOCTYPE html>
<?php
require('config/config.php');
$admin = false;
if (isset($_GET['admin'])) {
	$admin = true;
}
$sth = $G["db"]->prepare('SELECT * FROM `file`');
$sth->execute();
$filelist=$sth->fetchAll(PDO::FETCH_ASSOC);
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/<?=($admin?"管理":"查詢")?>檔案</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>

</head>
<body>

<?php
require("header.php");
?>
<div class="container">
	<h2>檔案<?=($admin?"管理":"查詢")?><?php if($admin){ ?> <a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/newfile/" role="button">新增</a><?php }?></h2>
	<div class="table-responsive">
		<table class="table">
			<th>編號</th>
			<th>名稱</th>
			<th>詳情</th>
			<?php
			foreach ($filelist as $file) {
			?>
			<tr>
				<td><?=$file['id']?></td>
				<td><?=$file['name']?></td>
				<td>
					<a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/file/<?=$file['id']?>/" role="button">詳情</a>
					<?php
					if ($admin) {
					?>
					<a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/editfile/<?=$file['id']?>/" role="button">管理</a>
					<?php
					}
					?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
	</div>
</div>


<?php
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</body>
</html>
