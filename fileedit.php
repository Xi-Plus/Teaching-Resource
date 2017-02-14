<!DOCTYPE html>
<?php
require('config/config.php');
$showform = true;
$fileid = $_GET["id"] ?? "";
$action = $_POST["action"] ?? "view";
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/編輯檔案</title>

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
$sth = $G["db"]->prepare("SELECT * FROM `file` WHERE `id` = :id");
$sth->bindValue(":id", $fileid);
$sth->execute();
$D["file"] = $sth->fetch(PDO::FETCH_ASSOC);
$sth = $G["db"]->prepare("SELECT * FROM `plan` WHERE JSON_CONTAINS(`file`, :file)");
$sth->bindValue(':file', json_encode([$fileid]));
$sth->execute();
$D["plans"] = $sth->fetchAll(PDO::FETCH_ASSOC);
if ($action == "edit") {
	$sth = $G["db"]->prepare("UPDATE `file` SET `name` = :name, `inuse` = :inuse WHERE `id` = :id");
	$_POST["name"] = trim($_POST["name"]);
	$_POST["name"] = preg_replace("/[[:cntrl:]]/", "", $_POST["name"]);
	if ($_POST["name"] == "") {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		名稱為空，沒有進行任何動作
	</div>
	<?php
	} else {
		$sth->bindValue(":name", $_POST["name"]);
		$sth->bindValue(":inuse", $_POST["inuse"]);
		$sth->bindValue(":id", $fileid);
		$sth->execute();
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?=$_POST["name"]?> 編輯成功，<a href="<?=$C["path"]?>/file/<?=$fileid?>/" target="_blank">查看</a>
		</div>
		<?php
	}
} else if ($action == "del") {
	if (count($D["plans"])) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			尚有教案使用此檔案，需先從教案移除此檔案方可刪除
		</div>
		<?php
	} else if (isset($_POST["del"])) {
		$sth = $G["db"]->prepare("DELETE FROM `file` WHERE `id` = :id");
		$sth->bindValue(":id", $fileid);
		$sth->execute();
		unlink("file/".$D["file"]["filename"]);
		$showform = false;
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已刪除，<a href="<?=$C["path"]?>/managefiles/">回到檔案列表</a>
		</div>
		<?php
	} else {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			請勾選「確認刪除」
		</div>
		<?php
	}
}
$sth = $G["db"]->prepare("SELECT * FROM `file` WHERE `id` = :id");
$sth->bindValue(":id", $fileid);
$sth->execute();
$D["file"] = $sth->fetch(PDO::FETCH_ASSOC);
if ($showform) {
	if ($D["file"] === false) {
		?>
		<div class="alert alert-danger" role="alert">
			找不到檔案，<a href="<?=$C["path"]?>/managefiles/">請回到列表重新選擇</a>
		</div>
		<?php
		$showform = false;
	}
}
if ($showform) {
?>
<div class="container">
	<h2>編輯檔案 <a class="btn btn-sm btn-info" href="<?=$C["path"]?>/file/<?=$fileid?>/" role="button" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i> 查看</a></h2>
	<form action="" method="post">
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-header filtericon" aria-hidden="true"></i> 名稱</label>
			<div class="col-sm-10">
				<input class="form-control" type="text" name="name" value="<?=$D["file"]["name"]?>" required>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-eye itemicon" aria-hidden="true"></i> 狀態</label>
			<div class="col-sm-10">
				<label>
					<input type="radio" name="inuse" value="1" <?=($D["file"]["inuse"] == 1?"checked":"")?>> <i class="fa fa-eye" aria-hidden="true"></i> <?=$G["inuse"][1]?>
				</label>
				<label>
					<input type="radio" name="inuse" value="0" <?=($D["file"]["inuse"] == 0?"checked":"")?>> <i class="fa fa-eye-slash" aria-hidden="true"></i> <?=$G["inuse"][0]?>
				</label>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-link itemicon" aria-hidden="true"></i> 使用</label>
			<div class="col-sm-10">
				<?php
				foreach ($D["plans"] as $plan) {
					?>
					<a href="<?=$C["path"]?>/plan/<?=$plan["id"]?>/"><?=$plan["name"]?></a>
					<a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/editplan/<?=$plan['id']?>/" role="button"><i class="fa fa-pencil" aria-hidden="true"></i> 編輯</a>
					<br>
					<?php
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" name="action" value="edit" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i> 編輯</button>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-10 offset-sm-2">
				<?php
				if (count($D["plans"])) {
					?>
					<button type="button" name="action" value="del" class="btn btn-danger disabled" data-toggle="tooltip" data-placement="bottom" title="尚有教案使用此檔案，需先從教案移除此檔案方可刪除"><i class="fa fa-trash-o" aria-hidden="true"></i> 刪除</button>
					<label class="disabled" data-toggle="tooltip" data-placement="bottom" title="尚有教案使用此檔案，需先從教案移除此檔案方可刪除">
						<input type="checkbox" name="del" disabled>確認刪除
					</label>
					<?php
				} else {
					?>
					<button type="submit" name="action" value="del" class="btn btn-danger">刪除</button>
					<label>
						<input type="checkbox" name="del">確認刪除
					</label>
					<?php
				}
				?>
			</div>
		</div>
	</form>
</div>

<?php
}
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/4c0a12abc0.js"></script>
<script type="text/javascript">
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
</script>
</body>
</html>
