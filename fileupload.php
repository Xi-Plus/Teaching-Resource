<!DOCTYPE html>
<?php
require('config/config.php');
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/上傳檔案</title>

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
if (!$U["islogin"]) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		此功能需要驗證帳號，請<a href="<?=$C["path"]?>/login/">登入</a>
	</div>
	<?php
	$showform = false;
} else if (isset($_POST["filename"]) && isset($_FILES["file"])) {
	for ($i=0; $i < count($_FILES["file"]["error"]); $i++) {
		if ($_FILES["file"]["error"][$i]==4) {

		} else if ($_FILES["file"]["error"][$i]==0) {
			$filehash=md5(file_get_contents($_FILES["file"]["tmp_name"][$i]));
			$realname=($_POST["filename"][$i]!=""?$_POST["filename"][$i]:$_FILES["file"]["name"][$i]);
			$sth = $G["db"]->prepare("SELECT * FROM `file` WHERE `filehash` = :filehash");
			$sth->bindValue(":filehash", $filehash);
			$sth->execute();
			$file=$sth->fetch(PDO::FETCH_ASSOC);
			if ($file===false) {
				$fileid=substr(md5(uniqid(rand(),true)), 0, 8);
				$filename=md5(uniqid(rand(),true));
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$fileext = finfo_file($finfo, $_FILES["file"]["tmp_name"][$i]);
				finfo_close($finfo);
				$sth = $G["db"]->prepare("INSERT INTO `file` (`name`, `extension`, `MIME`, `MIME2`, `filename`, `filehash`, `id`) VALUES (:name, :extension, :MIME, :MIME2, :filename, :filehash, :id);");
				$sth->bindValue(":name", $realname);
				$sth->bindValue(":extension", pathinfo($_FILES["file"]["name"][$i], PATHINFO_EXTENSION));
				$sth->bindValue(":MIME", $_FILES["file"]["type"][$i]);
				$sth->bindValue(":MIME2", $fileext);
				$sth->bindValue(":filename", $filename);
				$sth->bindValue(":filehash", $filehash);
				$sth->bindValue(":id", $fileid);
				$res1 = $sth->execute();
				$res2 = move_uploaded_file($_FILES["file"]["tmp_name"][$i], "file/".$filename);
				if ($res1 && $res2) {
				?>
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<?=$_FILES['file']['name'][$i]?> (<?=htmlentities($realname)?>) 上傳成功，<a href="<?=$C["path"]?>/file/<?=$fileid?>/" target="_blank">查看</a>、<a href="<?=$C["path"]?>/editfile/<?=$fileid?>/" target="_blank">編輯</a>
				</div>
				<?php
				} else {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					上傳失敗，錯誤訊息：<?php
					if ($res1 === false) {
						echo " ".$sth->errorInfo()[2];
					}
					if ($res2 === false) {
						echo " 移動檔案發生錯誤";
					}
					?>
				</div>
				<?php
				}
			} else {
			?>
			<div class="alert alert-warning alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?=$_FILES['file']['name'][$i]?> (<?=htmlentities($realname)?>) 已經上傳過了，<a href="<?=$C["path"]?>/file/<?=$file['id']?>/" target="_blank">查看</a>、<a href="<?=$C["path"]?>/editfile/<?=$file['id']?>/" target="_blank">編輯</a>
			</div>
			<?php
			}
		} else {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?=$_FILES['file']['name'][$i]?>(<?=$_POST['filename'][$i]?>)上傳失敗，錯誤代碼：<?=$_FILES["file"]["error"][$i]?>
		</div>
		<?php
		}
	}
	
}
if ($showform) {
?>
<div class="container">
	<h2>上傳檔案</h2>
	單一檔案大小限制: <?php echo ini_get("upload_max_filesize");?>
	<form action="" method="post" enctype="multipart/form-data">
		<div id="filelist">
			<div class="form-group" id="file1">
				<label>選擇檔案: <input type="file" name="file[]" accept="<?=$C["Filetype"]?>" onchange="getfilename(this)"></label>
				<label>檔名(不含副檔名): <input type="text" name="filename[]" size="30" pattern="<?=$C["FilenamePattern"]?>" title="<?=$C["FilenameTitle"]?>" ></label>
			</div>
		</div>
		<button id="morefilebtn" type="button" class="btn btn-default" onclick="morefile()"><i class="fa fa-file" aria-hidden="true"></i> 更多檔案</button>
		<button id="uploadbtn" type="submit" class="btn btn-success" onclick="upload()"><i class="fa fa-upload" aria-hidden="true"></i> <span id="uploadtext">上傳</span></button>
	</form>
</div>
<script type="text/javascript">
	var filecnt=1;
	function getfilename(e){
		var filename = e.files[0].name.split(".");
		filename.pop();
		e.parentNode.parentNode.children[1].children[0].value = filename.join(".");
	}
	function morefile(){
		var temp=filelist.children[0].cloneNode(true);
		filecnt++;
		temp.id="file"+filecnt;
		temp.children[0].children[0].value="";
		temp.children[1].children[0].value="";
		filelist.appendChild(temp);
	}
	function upload(){
		uploadtext.innerText = "上傳中...";
		setTimeout(function(){
			uploadbtn.disabled = true;
			morefilebtn.disabled = true;
		}, 100);
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
