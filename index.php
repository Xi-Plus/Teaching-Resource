<!DOCTYPE html>
<?php
require("config/config.php");
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
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
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/plans/" role="button">
				<i class="fa fa-search" aria-hidden="true"></i>
				查詢教案
				<i class="fa fa-sticky-note-o" aria-hidden="true"></i>
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/files/" role="button">
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

<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/4c0a12abc0.js"></script>
</body>
</html>
