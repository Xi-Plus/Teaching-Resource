<?php

if (!isset($_COOKIE[$C["cookiename"]])) {
	$U["islogin"] = false;
} else {
	$sth = $G["db"]->prepare('SELECT * FROM `login_session` WHERE `cookie` = :cookie');
	$sth->bindValue(":cookie", $_COOKIE[$C["cookiename"]]);
	$sth->execute();
	$cookie = $sth->fetch(PDO::FETCH_ASSOC);
	if ($cookie === false) {
		$U["islogin"] = false;
	} else {
		$sth = $G["db"]->prepare('SELECT * FROM `account` WHERE `account` = :account');
		$sth->bindValue(":account", $cookie["account"]);
		$sth->execute();
		$U = $sth->fetch(PDO::FETCH_ASSOC);
		$U["islogin"] = true;
	}
}

?>
