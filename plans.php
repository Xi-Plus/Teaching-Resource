<!DOCTYPE html>
<?php
require('config/config.php');
require('func/plantype.php');
require('func/tag.php');
$admin = isset($_GET['admin']);
if ($admin) {
	$sthyear = $G["db"]->prepare("SELECT MIN(`year`) AS 'minyear',MAX(`year`) AS 'maxyear' FROM `plan`");
	$sth = $G["db"]->prepare("SELECT * FROM `plan` ORDER BY `year` DESC, `type` ASC, `name` ASC");
} else {
	$sthyear = $G["db"]->prepare("SELECT MIN(`year`) AS 'minyear',MAX(`year`) AS 'maxyear' FROM `plan` WHERE `inuse` = 1");
	$sth = $G["db"]->prepare("SELECT * FROM `plan` WHERE `inuse` = 1 ORDER BY `year` DESC, `type` ASC, `name` ASC");
}
$sthyear->execute();
$row = $sthyear->fetch(PDO::FETCH_ASSOC);
$minyear = $row["minyear"];
$maxyear = $row["maxyear"];
$sth->execute();
$planlist = $sth->fetchAll(PDO::FETCH_ASSOC);
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/<?=($admin?"管理":"查詢")?>教案</title>

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
<script type="text/javascript">
function alltag(){
	for (var i = 0; i < filter_tag.length; i++) {
		filter_tag[i].checked = true;
	}
	filter_notag.checked = true;
}
function notag(){
	for (var i = 0; i < filter_tag.length; i++) {
		filter_tag[i].checked = false;
	}
	filter_notag.checked = false;
}
function changeandor(){
	if (tagand.checked) {
		filter_notag.checked = false;
		filter_notag.disabled = true;
		filter_notag.classList.add("disabled");
	} else {
		filter_notag.disabled = false;
		filter_notag.classList.remove("disabled");
	}
}
function filter(){
	console.log("filter");
	var year1 = parseInt(filter_year1.value);
	var year2 = parseInt(filter_year2.value);
	for (var i = 0; i < plantable.children.length; i++) {
		plantable.children[i].hidden = false;
	}
	if (!isNaN(year1)) {
		for (var i = 0; i < plantable.children.length; i++) {
			if (parseInt(plantable.children[i].children[0].innerText) < year1) {
				plantable.children[i].hidden = true;
			}
		}
	}
	if (!isNaN(year2)) {
		for (var i = 0; i < plantable.children.length; i++) {
			if (parseInt(plantable.children[i].children[0].innerText) > year2) {
				plantable.children[i].hidden = true;
			}
		}
	}
	var plantype = [];
	for (var i = 0; i < filter_plantype.length; i++) {
		plantype[filter_plantype[i].value] = filter_plantype[i].checked;
	}
	for (var i = 0; i < plantable.children.length; i++) {
		if (!plantype[plantable.children[i].children[1].innerText]) {
			plantable.children[i].hidden = true;
		}
	}
	if (filter_name.value != "") {
		for (var i = 0; i < plantable.children.length; i++) {
			if (plantable.children[i].children[2].innerText.search(filter_name.value) == -1) {
				plantable.children[i].hidden = true;
			}
		}
	}
	var tag = [];
	var tagisand = tagand.checked;
	var tagno = filter_notag.checked;
	for (var i = 0; i < filter_tag.length; i++) {
		if (filter_tag[i].checked) {
			tag.push(filter_tag[i].value);
		}
	}
	for (var i = 0; i < plantable.children.length; i++) {
		var tagtemp = [];
		for (var j = 0; j < plantable.children[i].children[3].children.length; j++) {
			tagtemp.push(plantable.children[i].children[3].children[j].innerText);
		}
		var show;
		if (tagtemp.length == 0 && tagno) {
			show = true;
		} else if (tagisand) {
			show = true;
			for (var j = 0; j < tag.length; j++) {
				if (tagtemp.indexOf(tag[j]) == -1) {
					show = false;
				}
			}
		} else {
			show = false;
			for (var j = 0; j < tag.length; j++) {
				if (tagtemp.indexOf(tag[j]) != -1) {
					show = true;
				}
			}
		}
		if (!show) {
			plantable.children[i].hidden = true;
		}
	}
}
</script>
<div class="container">
	<h2>教案<?=($admin?"管理":"查詢")?><?php if($admin){ ?> <a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/newplan/" role="button">新增</a><?php }?></h2>
	<div class="row">
		<label class="col-sm-2 form-control-label">學年度</label>
		<div class="col-sm-10 form-inline">
			<input type="number" class="form-control" placeholder="起始" id="filter_year1" value="<?=$minyear?>" onkeyup="filter()" style="max-width: 45%;">
			<span class="form-control-static">至</span>
			<input type="number" class="form-control" placeholder="結束" id="filter_year2" value="<?=$maxyear?>" onkeyup="filter()" style="max-width: 45%;">
		</div>
	</div>
	<div class="row">
		<label class="col-sm-2 form-control-label">分類</label>
		<div class="col-sm-10">
			<div class="checkbox">
			<?php
			$sth = $G["db"]->prepare("SELECT * FROM `plan_type` ORDER BY `id` ASC");
			$sth->execute();
			$plantypelist=$sth->fetchAll(PDO::FETCH_ASSOC);
			foreach ($plantypelist as $plantype) {
				?><label class="checkbox-inline" onclick="filter()">
					<input type="checkbox" id="filter_plantype" value="<?=$plantype['name']?>" checked><?=$plantype['name']?>
				</label> <?php
			}
			?>
			</div>
		</div>
	</div>
	<div class="row">
		<label class="col-sm-2 form-control-label" for="filter_name">標題</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="filter_name" onkeyup="filter()">
		</div>
	</div>
	<div class="row">
		<label class="col-sm-2 form-control-label">標籤</label>
		<div class="col-sm-10">
			<div class="checkbox">
				<label class="checkbox-inline" onclick="changeandor();filter();">
					<input type="radio" name="tagandor" id="tagand" value="and">AND
				</label>
				<label class="checkbox-inline" onclick="changeandor();filter();">
					<input type="radio" name="tagandor" id="tagor" value="or" checked>OR
				</label>
				<?php
				foreach ($D['tag'] as $tag => $cnt) {
					?><label class="checkbox-inline" onclick="filter()">
						<input type="checkbox" id="filter_tag" value="<?=$tag?>" checked><mark><?=$tag?></mark>
					</label> <?php
				}
				?>
				<label class="checkbox-inline" onclick="filter()" data-toggle="tooltip" data-placement="bottom" title="僅在OR模式作用">
					<input type="checkbox" id="filter_notag" checked>無標籤
				</label>
				<button type="button" class="btn btn-primary btn-sm" onclick="alltag();filter();">全選</button> 
				<button type="button" class="btn btn-primary btn-sm" onclick="notag();filter();">全不選</button>
			</div>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table">
			<thead>
				<th>學年度</th>
				<th>分類</th>
				<th>標題</th>
				<th>標籤</th>
				<th>動作</th>
			</thead>
			<tbody id="plantable">
			<?php
			foreach ($planlist as $plan) {
			?>
			<tr>
				<td><?=$plan['year']?></td>
				<td><?=$D['plantype'][$plan['type']]?></td>
				<td><?=$plan['name']?></td>
				<td><?php
					$plan['tag'] = json_decode($plan['tag'], true);
					foreach ($plan['tag'] as $key => $tag) {
						echo ($key?"、":"")."<mark>$tag</mark>";
					}
				?></td>
				<td>
				<a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/plan/<?=$plan['id']?>/" role="button">查看</a>
				<?php
				if ($admin) {
				?>
				<a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/editplan/<?=$plan['id']?>/" role="button">編輯</a>
				<?php
				}
				?>
				</td>
			</tr>
			<?php
			}
			?>
			</tbody>
		</table>
	</div>
</div>

<?php
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script type="text/javascript">
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
</script>
</body>
</html>
