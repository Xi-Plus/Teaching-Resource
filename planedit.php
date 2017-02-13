<!DOCTYPE html>
<?php
require('config/config.php');
require('func/plantype.php');
$showform = true;
switch ($_GET["type"]) {
	case 'add':
		$type = "add";
		$typename = "新增";
		break;
	case 'edit':
		$type = "edit";
		$typename = "編輯";
		break;
	default :
		$showform = false;
		break;
}
$action = $_POST["action"] ?? "view";
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
if ($action == "edit") {
	if ($type == "add") {
		$planid = substr(md5(uniqid(rand(),true)), 0, 8);
		$sth = $G["db"]->prepare("INSERT INTO `plan` (`year`, `type`, `name`, `description`, `tag`, `file`, `inuse`, `id`) VALUES (:year, :type, :name, :description, :tag, :file, :inuse, :id)");
	} else if ($type == "edit") {
		$sth = $G["db"]->prepare("UPDATE `plan` SET `year` = :year, type = :type, `name` = :name, `description` = :description, `tag` = :tag, `file` = :file, `inuse` = :inuse WHERE `id` = :id");
	}
	$tag = $_POST["tag"] ?? array();
	foreach ($_POST["newtag"] as $newtag) {
		$newtag = trim($newtag);
		$newtag = preg_replace("/[[:cntrl:]]/", "", $newtag);
		if ($newtag != "" && !in_array($newtag, $tag)) {
			$tag[] = $newtag;
		}
	}
	$file = $_POST["file"] ?? array();
	foreach ($_POST["newfile"] as $newfile) {
		$newfile = trim($newfile);
		$newfile = preg_replace("/[[:cntrl:]]/", "", $newfile);
		if ($newfile != "" && !in_array($newfile, $file)) {
			$sthfile = $G["db"]->prepare("SELECT * FROM `file` WHERE `id` = :id");
			$sthfile->bindValue(":id", $newfile);
			$sthfile->execute();
			$D["file"][$newfile] = $sthfile->fetch(PDO::FETCH_ASSOC);
			if ($D["file"][$newfile] === false) {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					附加檔案失敗，找不到檔案編號 <?=$newfile?>，<a href="<?=$C["path"]?>/managefiles/" target="_blank">查看檔案列表</a>
				</div>
				<?php
			} else {
				$file[] = $newfile;
				?>
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					附加檔案成功 <?=$D["file"][$newfile]["name"]?>，<a href="<?=$C["path"]?>/file/<?=$newfile?>/" target="_blank">查看此檔案</a>
				</div>
				<?php
			}
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
		$sth->bindValue(":file", json_encode($file));
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
} else if ($action == "del") {
	if (isset($_POST["del"])) {
		$sth = $G["db"]->prepare("DELETE FROM `plan` WHERE `id` = :id");
		$sth->bindValue(":id", $planid);
		$sth->execute();
		$showform = false;
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已刪除，<a href="<?=$C["path"]?>/manageplans/">回到教案列表</a>
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
if ($showform && $type == "add") {
	$D["plan"] = array("year"=>$G["schoolyear"], "type"=>"0", "name"=>"", "description"=>"", "tag"=>array(), "file"=>array(), "inuse"=>"1");
} else if ($showform && $type == "edit") {
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
	} else {
		$D["plan"]["tag"] = json_decode($D["plan"]["tag"], true);
		$D["plan"]["file"] = json_decode($D["plan"]["file"], true);
		foreach ($D["plan"]["file"] as $file) {
			if (!isset($D["file"][$file])) {
				$sthfile = $G["db"]->prepare("SELECT * FROM `file` WHERE `id` = :id");
				$sthfile->bindValue(":id", $file);
				$sthfile->execute();
				$D["file"][$file] = $sthfile->fetch(PDO::FETCH_ASSOC);
			}
		}
	}
}
require("func/tag.php");
if ($showform) {
?>
<div class="container">
	<h2><?=$typename?>教案 <?php if($type=="edit"){ ?><a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/plan/<?=$planid?>/" role="button" target="_blank">查看</a><?php } ?></h2>
	<form action="" method="post">
		<div class="row">
			<label class="col-sm-2 form-control-label">學年度</label>
			<div class="col-sm-10">
				<input class="form-control" type="number" name="year" value="<?=$D["plan"]["year"]?>" min="0" max="99999999" required>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label">分類</label>
			<div class="col-sm-10">
				<select name="type" class="form-control">
				<?php
				foreach ($D['plantype'] as $id => $plantype) {
					?><option value="<?=$id?>" <?=($D["plan"]["type"] == $id?"selected":"")?>><?=$plantype?></option><?php
				}
				?>
				</select>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label">標題</label>
			<div class="col-sm-10">
				<input class="form-control" type="text" name="name" value="<?=$D["plan"]["name"]?>" required>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label">說明</label>
			<div class="col-sm-10">
				<textarea class="form-control" name="description"><?=$D["plan"]["description"]?></textarea>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label">標籤</label>
			<div class="col-sm-10">
				<div class="checkbox">
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
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label">檔案</label>
			<div class="col-sm-10">
				<div class="checkbox">
					<?php
					foreach ($D["plan"]["file"] as $file) {
						?>
						<div id="file_<?=$file?>">
							<a href="<?=$C["path"]?>/file/<?=$file?>/" target="_blank"><?=$D["file"][$file]["name"]?></a>
							<input type="hidden" name="file[]" value="<?=$file?>">
							<button type="button" class="btn btn-danger btn-sm" onclick="removefile('<?=$file?>')">移除</button>
						</div>
						<?php
					}
					?>
					<div id="filelist">
						<input type="text" name="newfile[]" placeholder="新檔案">
					</div>
					<button type="button" class="btn btn-default btn-sm" onclick="morefile()">更多檔案</button>
					取得<strong>檔案編號</strong>再填至這裡：<a href="<?=$C["path"]?>/managefiles/" target="_blank">查詢舊檔案</a>、<a href="<?=$C["path"]?>/newfile/" target="_blank">上傳新檔案</a>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label">狀態</label>
			<div class="col-sm-10">
				<label>
					<input type="radio" name="inuse" value="1" <?=($D["plan"]["inuse"] == 1?"checked":"")?>><?=$G["inuse"][1]?>
				</label>
				<label>
					<input type="radio" name="inuse" value="0" <?=($D["plan"]["inuse"] == 0?"checked":"")?>><?=$G["inuse"][0]?>
				</label>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" name="action" value="edit" class="btn btn-primary"><?=$typename?></button>
			</div>
		</div>
		<?php
		if ($type == "edit") {
		?>
		<div class="row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" name="action" value="del" class="btn btn-danger">刪除</button>
				<label>
					<input type="checkbox" name="del">確認刪除
				</label>
			</div>
		</div>
		<?php
		}
		?>
	</form>
</div>
<script type="text/javascript">
	function moretag(){
		var temp=taglist.children[0].cloneNode(true);
		temp.value="";
		taglist.appendChild(temp);
	}
	function morefile(){
		var temp=filelist.children[0].cloneNode(true);
		temp.value="";
		filelist.appendChild(temp);
	}
	function removefile(id){
		document.all["file_"+id].remove();
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
