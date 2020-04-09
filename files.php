<!DOCTYPE html>
<?php
require_once __DIR__ . '/vendor/owasp/csrf-protector-php/libs/csrf/csrfprotector.php';
csrfProtector::init();
require('config/config.php');
require('func/filesize.php');
$admin = isset($_GET['admin']);
$pick = isset($_GET['pick']);
if ($admin) {
	$sth = $G["db"]->prepare('SELECT * FROM `file`');
} else {
	$sth = $G["db"]->prepare('SELECT * FROM `file` WHERE `inuse` = 1');
}
$sth->execute();
$filelist = $sth->fetchAll(PDO::FETCH_ASSOC);
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
require __DIR__ . '/commonhead.php';
?>
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
$showform = true;
if ($admin && !$U["islogin"]) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		此功能需要驗證帳號，請<a href="<?=$C["path"]?>/login/">登入</a>
	</div>
	<?php
	$showform = false;
}
if ($showform) {
?>
<div class="container">
	<h2>檔案<?=($admin?"管理":"查詢")?><?php if($admin){ ?> <a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/newfile/" role="button"><i class="fa fa-upload" aria-hidden="true"></i> 上傳</a><?php }?></h2>
	<div class="table-responsive">
		<table class="table">
			<th>名稱</th>
			<th>副檔名</th>
			<th>檔案大小</th>
			<th>動作</th>
			<?php
			foreach ($filelist as $file) {
			?>
			<tr>
				<td><?=htmlentities($file['name'])?></td>
				<td><?=htmlentities($file['extension'])?></td>
				<td style="white-space: nowrap"><?php
				if (file_exists("file/".$file["filename"])) {
					echo FormateFileSize(filesize(__DIR__."/file/".$file['filename']));
				} else {
					echo "檔案遺失";
				}
				?></td>
				<td>
					<?php
					if ($pick) {
					?>
					<button class="btn btn-sm btn-info" onclick="window.opener.morefile('<?=$file['id']?>');window.close();"><i class="fa fa-check" aria-hidden="true"></i> 選取</button>
					<?php
					}
					?>
					<a class="btn btn-sm btn-success" href="<?=$C["path"]?>/file/<?=$file['id']?>/" role="button"><i class="fa fa-eye" aria-hidden="true"></i> 查看</a>
					<?php
					if ($admin) {
					?>
					<a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/editfile/<?=$file['id']?>/" role="button"><i class="fa fa-pencil" aria-hidden="true"></i> 管理</a>
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
}
require("footer.php");
?>
<script type="text/javascript">
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
</script>
</body>
</html>
