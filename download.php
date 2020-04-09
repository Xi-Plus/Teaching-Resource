<?php
require_once __DIR__ . '/vendor/owasp/csrf-protector-php/libs/csrf/csrfprotector.php';
csrfProtector::init();
require('config/config.php');
$fileid = $_GET['id'] ?? "";
$sth = $G["db"]->prepare("SELECT * FROM `file` WHERE `id` = :id");
$sth->bindValue(':id', $fileid);
$sth->execute();
$file = $sth->fetch(PDO::FETCH_ASSOC);
$filepath = "file/".$file["filename"];
if ($file !== false && file_exists($filepath)) {
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.$file["name"].".".$file["extension"].'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('X-Robots-Tag: noindex');
	header('Content-Length: '.filesize($filepath));
	readfile($filepath);
	exit;
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<?php
require __DIR__ . '/commonhead.php';
?>
<title><?=$C["titlename"]?>/下載檔案</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>

</head>
<body>

<?php
require("header.php");
if ($file === false) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		找不到檔案
	</div>
	<?php
} elseif (!file_exists($filepath)) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		檔案遺失
	</div>
	<?php
}
?>
<div class="container">

</div>

<?php
require("footer.php");
?>
</body>
</html>
