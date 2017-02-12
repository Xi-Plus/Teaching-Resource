<!DOCTYPE html>
<?php
require('config/config.php');
require('func/plantype.php');
require('func/tag.php');
$admin = false;
if (isset($_GET['admin'])) {
	$admin = true;
}
$sth = $G["db"]->prepare('SELECT * FROM `plan` ORDER BY `year` DESC, `type` ASC, `name` ASC');
$sth->execute();
$planlist=$sth->fetchAll(PDO::FETCH_ASSOC);
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
}
function notag(){
	for (var i = 0; i < filter_tag.length; i++) {
		filter_tag[i].checked = false;
	}
}
window.onload=function(){
	var f,plantype,tag;
	f=Array.prototype.forEach;
	console.log("init");
	plantype = {};
	f.call(filter_plantype,(b,i)=>plantype[b.value]=i);
	tag = {};
	f.call(filter_tag,(b,i)=>tag[b.value]=i);
	f.call(plantable.children,b=>{
		c=b.children;
		c[0].i=+c[0].innerText;
		c[1].i=plantype[c[1].innerText];
		c[3].i={};
		c[3].l=c[3].children.length;
		f.call(c[3].children,d=>c[3].i[tag[d.innerText]]=!0)
	})
};
function filter(){
	var a,s,f,f0,f1,f2,year1,year2,plantype,tag;
	a=Array.prototype;
	f0=a.forEach;
	f1=c=>c.l==tag.length&&a.every.call(tag,e=>c.i[e]);
	f2=c=>a.some.call(tag,e=>c.i[e]);
	console.log("filter");
	year1 = +(filter_year1.value||0);
	year2 = +(filter_year2.value||0xffff);
	s=filter_name.value;
	f = tagand.checked?f1:f2;
	plantype = {};
	f0.call(filter_plantype,(b,i)=>plantype[i]=b.checked);
	tag = [];
	f0.call(filter_tag,(b,i)=>{if(b.checked)tag.push(i)});
	f0.call(plantable.children,b=>{
		var c=b.children;
		b.hidden=!(
			f(c[3])&&
			plantype[c[1].i]&&
			c[0].i>=year1&&
			c[0].i<=year2&&
			(!c[2].innerText||c[2].innerText.search(s) > -1));
	})
}
</script>
<div class="container">
	<h2>教案<?=($admin?"管理":"查詢")?><?php if($admin){ ?> <a class="btn btn-md btn-primary" href="<?=$C["path"]?>/newplan/" role="button">新增</a><?php }?></h2>
	<div class="row">
		<label class="col-sm-2 form-control-label">學年度</label>
		<div class="col-sm-10 form-inline">
			<input type="number" class="form-control" placeholder="起始" id="filter_year1" oninput="filter()" style="max-width: 45%;">
			<span class="form-control-static">至</span>
			<input type="number" class="form-control" placeholder="結束" id="filter_year2" oninput="filter()" style="max-width: 45%;">
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
				?><label class="checkbox-inline" onchange="filter()">
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
			<input type="text" class="form-control" id="filter_name" oninput="filter()">
		</div>
	</div>
	<div class="row">
		<label class="col-sm-2 form-control-label">標籤</label>
		<div class="col-sm-10">
			<div class="checkbox">
				<label class="checkbox-inline" onchange="filter()">
					<input type="radio" name="tagandor" id="tagand" value="and">AND
				</label>
				<label class="checkbox-inline" onchange="filter()">
					<input type="radio" name="tagandor" id="tagor" value="or" checked>OR
				</label>
				<?php
				foreach ($D['tag'] as $tag => $cnt) {
					?><label class="checkbox-inline" onchange="filter()">
						<input type="checkbox" id="filter_tag" value="<?=$tag?>" checked><?=$tag?>
					</label> <?php
				}
				?>
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
				<th><?=($admin?"管理":"詳情")?></th>
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
				<?php
				if ($admin) {
				?>
				<a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/manage/plan/edit/?id=<?=$plan['id']?>" role="button">管理</a>
				<?php
				} else {
				?>
				<a class="btn btn-sm btn-primary" href="<?=$C["path"]?>/view/plan/?id=<?=$plan['id']?>" role="button">詳情</a>
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
</body>
</html>
