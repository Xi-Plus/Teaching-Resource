<!DOCTYPE html>
<?php
require('config/config.php');
$sth = $G["db"]->prepare("SELECT * FROM `plan` WHERE `id` = :id");
$sth->bindValue(':id', $_GET['id']);
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
			<tr><td>學年度</td><td><?=$plan['year']?></td></tr>
			<tr><td>類別</td><td><?=$plan['type']?></td></tr>
			<tr><td>標題</td><td><?=$plan['name']?></td></tr>
			<tr><td>說明</td><td><?=str_replace("\n", "<br>", $plan['description'])?></td></tr>
			<tr><td>標籤</td><td><?php
					$plan['tag'] = json_decode($plan['tag'], true);
					foreach ($plan['tag'] as $key => $tag) {
						echo ($key?"、":"")."<mark>$tag</mark>";
					}
				?></td></tr>
			<tr><td>附件</td><?php
					$plan['file'] = json_decode($plan['file'], true);
					foreach ($plan['file'] as $file) {
						echo "$file<br>";
					}
				?></td></tr>
			<tr><td>顯示</td><td><?=$plan['inuse']?></td></tr>
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
</body>
</html>
