<!DOCTYPE html>
<?php
require('config/config.php');
require("func/tag.php");
$G["db"] = new PDO ('mysql:host='.$cfgDBhost.';dbname='.$cfgDBname.';charset=utf8', $cfgDBuser, $cfgDBpass);
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/新增教案</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>

</head>
<body>

<?php
require("header.php");
if (isset($_POST["year"])) {
	$id=substr(md5(uniqid(rand(),true)), 0, 8);
	$tag=$_POST["tag"] ?? array();
	foreach ($_POST["newtag"] as $newtag) {
		$tag[]=$newtag;
	}
	$sth = $G["db"]->prepare("INSERT INTO `plan` (`year`, `type`, `name`, `description`, `tag`, `file`, `id`) VALUES (:year, :type, :name, :description, :tag, '[]', :id)");
	$sth->bindValue(":year", $_POST["year"]);
	$sth->bindValue(":type", $_POST["type"]);
	$sth->bindValue(":name", $_POST["name"]);
	$sth->bindValue(":description", $_POST["description"]);
	$sth->bindValue(":tag", json_encode($tag));
	$sth->bindValue(":id", $id);
	$sth->execute();
	?>
	<div class="alert alert-success alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <?=$_POST["name"]?> 新增成功，<a href="<?=$C["path"]?>/view/plan/?id=<?=$id?>" target="_blank">查看</a>
	</div>
	<?php
}
?>
<div class="container">
	<h2>新增教案</h2>
	<form action="" method="post" enctype="multipart/form-data">
		<div class="form-group"><label>學年度: <input type="number" name="year" required></label></div>
		<div class="form-group"><label>類別: <select name="type">
			<?php
			$sth = $G["db"]->prepare("SELECT * FROM `plan_type` ORDER BY `id` ASC");
			$sth->execute();
			$plantypelist=$sth->fetchAll(PDO::FETCH_ASSOC);
			foreach ($plantypelist as $plantype) {
				?><option value="<?=$plantype['id']?>"><?=$plantype['name']?></option><?php
			}
			?>
		</select></label></div>
		<div class="form-group"><label>標題: <input type="text" name="name" required></label></div>
		<div class="form-group"><label>說明: <textarea name="description"></textarea></label></div>
		<div class="form-group">標籤:
			<?php
			foreach ($D['tag'] as $tag => $cnt) {
				?><label><input type="checkbox" name="tag[]" value="<?=$tag?>"><?=$tag?>(<?=$cnt?>)</label> <?php
			}
			?>
			<div id="taglist">
				<input type="text" name="newtag[]" placeholder="新標籤">
			</div>
			<button type="button" class="btn btn-default btn-sm" onclick="moretag()">更多標籤</button>
		</div>
		<button type="submit" class="btn btn-primary">新增</button>
	</form>
</div>
<script type="text/javascript">
	function moretag(){
		var temp=taglist.children[0].cloneNode(true);
		temp.value="";
		taglist.appendChild(temp);
	}
</script>

<?php
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</body>
</html>
