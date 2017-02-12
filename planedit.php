<!DOCTYPE html>
<?php
require('config/config.php');
switch ($_GET["type"]) {
	case 'add':
		$type = "add";
		$typename = "新增";
		break;
	case 'edit':
		$type = "edit";
		$typename = "編輯";
		break;
}
$planid = $_GET["id"] ?? "";
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/<?=$typename?>教案</title>

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
if (isset($_POST["year"])) {
	if ($type == "add") {
		$planid = substr(md5(uniqid(rand(),true)), 0, 8);
		$sth = $G["db"]->prepare("INSERT INTO `plan` (`year`, `type`, `name`, `description`, `tag`, `file`, `inuse`, `id`) VALUES (:year, :type, :name, :description, :tag, '[]', :inuse, :id)");
	} else if ($type == "edit") {
		$sth = $G["db"]->prepare("UPDATE `plan` SET `year` = :year, type = :type, `name` = :name, `description` = :description, `tag` = :tag, `inuse` = :inuse WHERE `id` = :id");
	}
	$tag = $_POST["tag"] ?? array();
	foreach ($_POST["newtag"] as $newtag) {
		$newtag = trim($newtag);
		$newtag = preg_replace("/[[:cntrl:]]/", "", $newtag);
		if ($newtag != "" && !in_array($newtag, $tag)) {
			$tag[] = $newtag;
		}
	}
	$_POST["name"] = trim($_POST["name"]);
	$_POST["name"] = preg_replace("/[[:cntrl:]]/", "", $_POST["name"]);
	if ($_POST["name"] == "") {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		標題為空，沒有進行任何動作
	</div>
	<?php
	} else {
		$sth->bindValue(":year", $_POST["year"]);
		$sth->bindValue(":type", $_POST["type"]);
		$sth->bindValue(":name", $_POST["name"]);
		$sth->bindValue(":description", $_POST["description"]);
		$sth->bindValue(":tag", json_encode($tag));
		$sth->bindValue(":inuse", $_POST["inuse"]);
		$sth->bindValue(":id", $planid);
		$sth->execute();
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?=$_POST["name"]?> <?=$typename?>成功，<a href="<?=$C["path"]?>/plan/<?=$planid?>/" target="_blank">查看</a>
		</div>
		<?php
	}
}
if ($type == "add") {
	$D["plan"] = array("year"=>$G["schoolyear"], "type"=>"0", "name"=>"", "description"=>"", "inuse"=>"1", "tag"=>array());
} else if ($type == "edit") {
	$sth = $G["db"]->prepare("SELECT * FROM `plan` WHERE `id` = :id");
	$sth->bindValue(":id", $planid);
	$sth->execute();
	$D["plan"] = $sth->fetch(PDO::FETCH_ASSOC);
	if ($D["plan"] === false) {
		?>
		<div class="alert alert-danger" role="alert">
			找不到教案，<a href="<?=$C["path"]?>/manageplans/">請回到列表重新選擇</a>
		</div>
		<?php
		$showform = false;
	}
	$D["plan"]["tag"] = json_decode($D["plan"]["tag"], true);
}
require("func/tag.php");
if ($showform) {
?>
<div class="container">
	<h2><?=$typename?>教案</h2>
	<form action="" method="post">
		<div class="form-group">
			<label>學年度：<input type="number" name="year" value="<?=$D["plan"]["year"]?>" min="0" max="99999999" required></label>
		</div>
		<div class="form-group">
			<label>
				類別：
				<select name="type">
				<?php
				$sth = $G["db"]->prepare("SELECT * FROM `plan_type` ORDER BY `id` ASC");
				$sth->execute();
				$plantypelist=$sth->fetchAll(PDO::FETCH_ASSOC);
				foreach ($plantypelist as $plantype) {
					?><option value="<?=$plantype['id']?>" <?=($D["plan"]["type"] == $plantype['id']?"selected":"")?>><?=$plantype['name']?></option><?php
				}
				?>
				</select>
			</label>
		</div>
		<div class="form-group">
			<label>標題：<input type="text" name="name" value="<?=$D["plan"]["name"]?>" required></label>
		</div>
		<div class="form-group">
			<label>說明：<textarea name="description"><?=$D["plan"]["description"]?></textarea></label>
		</div>
		<div class="form-group">
			狀態：
			<label>
				<input type="radio" name="inuse" value="1" <?=($D["plan"]["inuse"] == 1?"checked":"")?>>顯示
			</label>
			<label>
				<input type="radio" name="inuse" value="0" <?=($D["plan"]["inuse"] == 0?"checked":"")?>>隱藏
			</label>
		</div>
		<div class="form-group">標籤:
			<?php
			foreach ($D['tag'] as $tag => $cnt) {
				?><label><input type="checkbox" name="tag[]" value="<?=$tag?>" <?=(in_array($tag, $D["plan"]["tag"])?"checked":"")?>><?=$tag?>(<?=$cnt?>)</label> <?php
			}
			?>
			<div id="taglist">
				<input type="text" name="newtag[]" placeholder="新標籤" maxlength="15">
			</div>
			<button type="button" class="btn btn-default btn-sm" onclick="moretag()">更多標籤</button>
		</div>
		<button type="submit" class="btn btn-primary"><?=$typename?></button>
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
}
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</body>
</html>
