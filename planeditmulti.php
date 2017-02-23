<!DOCTYPE html>
<?php
require('config/config.php');
require('func/plantype.php');
$showform = true;
$action = $_POST["action"] ?? "view";
$planids = explode(",", $_GET["ids"] ?? "");
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/編輯多筆教案</title>

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
$showform = true;
if (!$U["islogin"]) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		此功能需要驗證帳號，請<a href="<?=$C["path"]?>/login/">登入</a>
	</div>
	<?php
	$showform = false;
}
if ($showform) {
	$D["plans"]["file"] = array();
	foreach ($planids as $planid) {
		$sth = $G["db"]->prepare("SELECT * FROM `plan` WHERE `id` = :id");
		$sth->bindValue(":id", $planid);
		$sth->execute();
		$D["plan"][$planid] = $sth->fetch(PDO::FETCH_ASSOC);
		if ($D["plan"][$planid] === false) {
			?>
			<div class="alert alert-danger" role="alert">
				找不到教案 <?=$planid?>，<a href="<?=$C["path"]?>/manageplans/">請回到列表重新選擇</a>
			</div>
			<?php
			$showform = false;
		} else {
			$D["plan"][$planid]["tag"] = json_decode($D["plan"][$planid]["tag"], true);
			$D["plan"][$planid]["file"] = json_decode($D["plan"][$planid]["file"], true);
			foreach ($D["plan"][$planid]["file"] as $file) {
				if (!in_array($file, $D["plans"]["file"])) {
					$D["plans"]["file"][]= $file;
				}
			}
			foreach ($D["plan"][$planid]["file"] as $file) {
				if (!isset($D["file"][$planid][$file])) {
					$sthfile = $G["db"]->prepare("SELECT * FROM `file` WHERE `id` = :id");
					$sthfile->bindValue(":id", $file);
					$sthfile->execute();
					$D["file"][$file] = $sthfile->fetch(PDO::FETCH_ASSOC);
				}
			}
		}
	}
}
if ($showform && $action == "edit") {
	if ($_POST["edityear"] == 1) {
		$sth = $G["db"]->prepare("UPDATE `plan` SET `year` = :year WHERE `id` = :id");
		foreach ($planids as $planid) {
			$sth->bindValue(":year", $_POST["year"]);
			$sth->bindValue(":id", $planid);
			$sth->execute();
		}
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已修改學年度
		</div>
		<?php
	}
	if ($_POST["type"] !== "") {
		$sth = $G["db"]->prepare("UPDATE `plan` SET `type` = :type WHERE `id` = :id");
		foreach ($planids as $planid) {
			$sth->bindValue(":type", $_POST["type"]);
			$sth->bindValue(":id", $planid);
			$sth->execute();
		}
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已修改分類
		</div>
		<?php
	}
	if ($_POST["editdescription"] == "1") {
		$sth =  $G["db"]->prepare("UPDATE `plan` SET `description` = :description WHERE `id` = :id");
		foreach ($planids as $planid) {
			$sth->bindValue(":description", $_POST["description"]);
			$sth->bindValue(":id", $planid);
			$sth->execute();
		}
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已修改說明
		</div>
		<?php
	}
	$addtag = array();
	$deltag = array();
	if (isset($_POST["tag"])) {
		foreach ($_POST["tag"] as $tag => $status) {
			if ($status == 1) {
				$addtag[]= $tag;
			} else if ($status == 0) {
				$deltag[]= $tag;
			} 
		}
	}
	foreach ($_POST["newtag"] as $newtag) {
		$newtag = trim($newtag);
		$newtag = preg_replace("/[[:cntrl:]]/", "", $newtag);
		if ($newtag != "" && !in_array($newtag, $addtag)) {
			$addtag[] = $newtag;
		}
	}
	$sth = $G["db"]->prepare("UPDATE `plan` SET `tag` = :tag WHERE `id` = :id");
	foreach ($planids as $planid) {
		$tag = array_diff($D["plan"][$planid]["tag"], $deltag);
		$tag = array_merge($tag, $addtag);
		$tag = array_unique($tag);
		if ($tag != $D["plan"][$planid]["tag"]) {
			$sth->bindValue(":tag", json_encode($tag));
			$sth->bindValue(":id", $planid);
			$sth->execute();
			?>
			<div class="alert alert-info alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				已修改標籤 <?=$D["plan"][$planid]["name"]?>
			</div>
			<?php
		}
	}
	if (count($addtag)) {
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已為所有教案加上這些標籤：<?=implode("、", $addtag)?>
		</div>
		<?php
	}
	if (count($deltag)) {
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已從所有教案移除這些標籤：<?=implode("、", $deltag)?>
		</div>
		<?php
	}
	$addfile = array();
	$delfile = array();
	if (isset($_POST["file"])) {
		foreach ($_POST["file"] as $file => $status) {
			if ($status == 1) {
				$addfile[]= $file;
			} else if ($status == 0) {
				$delfile[]= $file;
			} 
		}
	}
	foreach ($_POST["newfile"] as $newfile) {
		$newfile = trim($newfile);
		$newfile = preg_replace("/[[:cntrl:]]/", "", $newfile);
		if ($newfile != "" && !in_array($newfile, $addfile)) {
			$addfile[] = $newfile;
		}
	}
	$sth = $G["db"]->prepare("UPDATE `plan` SET `file` = :file WHERE `id` = :id");
	foreach ($planids as $planid) {
		$file = array_diff($D["plan"][$planid]["file"], $delfile);
		$file = array_merge($file, $addfile);
		$file = array_unique($file);
		if ($file != $D["plan"][$planid]["file"]) {
			$sth->bindValue(":file", json_encode($file));
			$sth->bindValue(":id", $planid);
			$sth->execute();
			?>
			<div class="alert alert-info alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				已修改檔案 <?=$D["plan"][$planid]["name"]?>
			</div>
			<?php
		}
	}
	if (count($addfile)) {
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已為所有教案加上這些檔案：<?=implode("、", $addfile)?>
		</div>
		<?php
	}
	if (count($delfile)) {
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已從所有教案移除這些檔案：<?=implode("、", $delfile)?>
		</div>
		<?php
	}
	if ($_POST["inuse"] != -1) {
		$sth = $G["db"]->prepare("UPDATE `plan` SET `inuse` = :inuse WHERE `id` = :id");
		foreach ($planids as $planid) {
			$sth->bindValue(":inuse", $_POST["inuse"]);
			$sth->bindValue(":id", $planid);
			$sth->execute();
		}
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已修改狀態
		</div>
		<?php
	}
} else if ($showform && $action == "del") {
	if (isset($_POST["del"])) {
		foreach ($planids as $planid) {
			$sth = $G["db"]->prepare("DELETE FROM `plan` WHERE `id` = :id");
			$sth->bindValue(":id", $planid);
			$sth->execute();
			?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				已刪除 <?=$D["plan"][$planid]["name"]?>
			</div>
			<?php
		}
		?>
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<a href="<?=$C["path"]?>/manageplans/">回到教案列表</a>
		</div>
		<?php
		$showform = false;
	} else {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			請勾選「確認刪除」
		</div>
		<?php
	}
}
require("func/tag.php");
if ($showform) {
?>
<div class="container">
	<h2>編輯多筆教案 <a class="btn btn-sm btn-info" href="<?=$C["path"]?>/plan/<?=($_GET["ids"] ?? "")?>/" role="button" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i> 查看</a></h2>
	<form action="" method="post">
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-header itemicon" aria-hidden="true"></i> 標題</label>
			<div class="col-sm-9 col-md-10">
				<?php
				foreach ($D["plan"] as $planid => $plan) {
					echo htmlentities($plan["name"])."<br>";
				}
				?>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-calendar itemicon" aria-hidden="true"></i> 學年度</label>
			<div class="col-sm-9 col-md-10 form-inline">
				<label class="form-control-label">
					<input class="form-control" type="radio" name="edityear" value="0" checked> 不修改
				</label> 
				<label class="form-control-label">
					<input class="form-control" type="radio" name="edityear" id="edityear1" value="1"> 修改為
				</label>
				<input class="form-control" type="number" name="year" onfocus="edityear1.checked=true;" min="0" max="99999999">
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-bookmark itemicon" aria-hidden="true"></i> 分類</label>
			<div class="col-sm-9 col-md-10">
				<select name="type" class="form-control">
					<option value="">不修改</option>
				<?php
				foreach ($D['plantype'] as $id => $plantype) {
					?><option value="<?=$id?>"><?=$plantype?></option><?php
				}
				?>
				</select>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-info itemicon" aria-hidden="true"></i> 說明</label>
			<div class="col-sm-9 col-md-10">
				<div class="row">
					<div class="col-12 form-inline">
						<label class="form-control-label">
							<input class="form-control" type="radio" name="editdescription" value="0" checked> 不修改
						</label> 
						<label class="form-control-label">
							<input class="form-control" type="radio" name="editdescription" id="editdescription1" value="1"> 修改為
						</label>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<textarea class="form-control" name="description" onfocus="editdescription1.checked=true;"></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-tags itemicon" aria-hidden="true"></i> 標籤</label>
			<div class="col-sm-9 col-md-10">
				<div class="checkbox">
					<?php
					foreach ($D['tag'] as $tag => $cnt) {
						?>
						<label class="form-control-label"><input type="radio" name="tag[<?=$tag?>]" value="-1" checked> 不修改</label>
						<label class="form-control-label"><input type="radio" name="tag[<?=$tag?>]" value="1"> 加上</label>
						<label class="form-control-label"><input type="radio" name="tag[<?=$tag?>]" value="0"> 移除</label>
						<mark><?=htmlentities($tag)?> (<?=$cnt?>)</mark><br>
						<?php
					}
					?>
					<div id="taglist">
						<input type="text" name="newtag[]" placeholder="新標籤" maxlength="15">
					</div>
					<button type="button" class="btn btn-default btn-sm" onclick="moretag()"><i class="fa fa-tag" aria-hidden="true"></i> 更多標籤</button>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-files-o itemicon" aria-hidden="true"></i> 檔案</label>
			<div class="col-sm-9 col-md-10">
				<div class="checkbox">
					<?php
					foreach ($D["plans"]["file"] as $file) {
						?>
						<label class="form-control-label"><input type="radio" name="file[<?=$file?>]" value="-1" checked> 不修改</label>
						<label class="form-control-label"><input type="radio" name="file[<?=$file?>]" value="1"> 加上</label>
						<label class="form-control-label"><input type="radio" name="file[<?=$file?>]" value="0"> 移除</label>
						<a href="<?=$C["path"]?>/file/<?=$file?>/" target="_blank"><?=htmlentities($D["file"][$file]["name"])?></a>
						<?php
					}
					?>
					<div id="filelist">
						<input type="text" name="newfile[]" placeholder="新檔案" hidden>
					</div>
					<button type="button" class="btn btn-default btn-sm" onclick="pickfile()"><i class="fa fa-file" aria-hidden="true"></i> 更多檔案</button>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-eye itemicon" aria-hidden="true"></i> 狀態</label>
			<div class="col-sm-9 col-md-10">
				<label>
					<input type="radio" name="inuse" value="-1" checked> 不修改
				</label>
				<label>
					<input type="radio" name="inuse" value="1"> <i class="fa fa-eye" aria-hidden="true"></i> <?=$G["inuse"][1]?>
				</label>
				<label>
					<input type="radio" name="inuse" value="0"> <i class="fa fa-eye-slash" aria-hidden="true"></i> <?=$G["inuse"][0]?>
				</label>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-9 col-md-10 offset-sm-2">
				<button type="submit" name="action" value="edit" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i> 編輯多筆</button>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-9 col-md-10 offset-sm-2">
				<button type="submit" name="action" value="del" class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i> 刪除</button>
				<label>
					<input type="checkbox" name="del">確認刪除
				</label>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	function moretag(){
		var temp=taglist.children[0].cloneNode(true);
		temp.value="";
		taglist.appendChild(temp);
	}
	function morefile(id=""){
		var temp=filelist.children[0].cloneNode(true);
		temp.value=id;
		temp.hidden=false;
		filelist.appendChild(temp);
	}
	function removefile(id){
		document.all["file_"+id].remove();
	}
	function pickfile(id){
		window.open("<?=$C["path"]?>/pickfile/", "_blank");
	}
</script>

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
