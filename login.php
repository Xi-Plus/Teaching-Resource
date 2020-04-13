<!DOCTYPE html>
<?php
require_once __DIR__ . '/vendor/owasp/csrf-protector-php/libs/csrf/csrfprotector.php';
csrfProtector::init();
require('config/config.php');
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
require __DIR__ . '/commonhead.php';
?>
<title><?=$C["titlename"]?>/管理帳號</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
</head>
<body>
<?php
$action = $_GET['action'] ?? '';
$showform = true;
if ($action === "login") {
	if ($U["islogin"]) {
		?>
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已經登入了
		</div>
		<?php
		$showform = false;
	} else if (isset($_POST["account"])) {
		$sth = $G["db"]->prepare('SELECT * FROM `account` WHERE `account` = :account');
		$sth->bindValue(":account", $_POST["account"]);
		$sth->execute();
		$account = $sth->fetch(PDO::FETCH_ASSOC);
		if ($account !== false && password_verify($_POST["password"], $account["password"])) {
			$cookie = md5(uniqid(rand(),true));
			$sth = $G["db"]->prepare('INSERT INTO `login_session` (`account`, `cookie`) VALUES (:account, :cookie)');
			$sth->bindValue(":account", $_POST["account"]);
			$sth->bindValue(":cookie", $cookie);
			$sth->execute();
			setcookie($C["cookiename"], $cookie, time()+$C["cookieexpire"], $C["path"]);
			?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				登入成功
			</div>
			<?php
			$U = $account;
			$U["islogin"] = true;
			$showform = false;
		} else {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				登入失敗
			</div>
			<?php
		}
	}
} else if ($action === "logout") {
	if ($U["islogin"]) {
		$sth = $G["db"]->prepare('DELETE FROM `login_session` WHERE `cookie` = :cookie');
		$sth->bindValue(":cookie", $_COOKIE[$C["cookiename"]]);
		$sth->execute();
		setcookie($C["cookiename"], "", time(), $C["path"]);
	}
	?>
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		已登出
	</div>
	<?php
	$U["islogin"] = false;
	$showform = false;
}
require("header.php");
if ($showform) {
?>
<div class="container">
	<h2>登入</h2>
	<form action="" method="post">
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-user" aria-hidden="true"></i> 帳號</label>
			<div class="col-sm-10">
				<input class="form-control" type="text" name="account" required>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-hashtag" aria-hidden="true"></i> 密碼</label>
			<div class="col-sm-10">
				<input class="form-control" type="password" name="password" required>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" class="btn btn-success" name="action" value="new"><i class="fa fa-sign-in" aria-hidden="true"></i> 登入</button>
			</div>
		</div>
	</form>
</div>

<?php
}
require("footer.php");
?>
<script type="text/javascript">
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
</script>
</body>
</html>
