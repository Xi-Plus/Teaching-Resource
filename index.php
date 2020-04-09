<!DOCTYPE html>
<?php
require("config/config.php");
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
require __DIR__ . '/commonhead.php';
?>
<title><?=$C["titlename"]?></title>

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
	<div class="jumbotron">
		<h1><?=$C["sitename"]?></h1>
		<p class="lead"></p>
		<p>
			<a class="btn btn-lg btn-success" href="<?=$C["path"]?>/plans/" role="button">
				<i class="fa fa-search" aria-hidden="true"></i>
				查詢教案
				<i class="fa fa-sticky-note-o" aria-hidden="true"></i>
			</a>
			<a class="btn btn-lg btn-success" href="<?=$C["path"]?>/files/" role="button">
				<i class="fa fa-search" aria-hidden="true"></i>
				查詢檔案
				<i class="fa fa-file" aria-hidden="true"></i>
			</a>
		</p>
		<p>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manageplans/" role="button">
				<i class="fa fa-pencil" aria-hidden="true"></i>
				管理教案
				<i class="fa fa-sticky-note-o" aria-hidden="true"></i>
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/managefiles/" role="button">
				<i class="fa fa-pencil" aria-hidden="true"></i>
				管理檔案
				<i class="fa fa-file" aria-hidden="true"></i>
			</a>
		</p>
	</div>
</div>

</body>
</html>
