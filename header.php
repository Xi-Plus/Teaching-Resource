<nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
	<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<a class="navbar-brand" href="<?=$C["path"]?>/"><?=$C["sitename"]?></a>
	<div class="collapse navbar-collapse" id="navbarCollapse">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="<?=$C["path"]?>/"><i class="fa fa-home" aria-hidden="true"></i> 首頁</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="search" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-search" aria-hidden="true"></i> 查詢</a>
				<div class="dropdown-menu" aria-labelledby="search">
					<a class="dropdown-item" href="<?=$C["path"]?>/plans/"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> 教案</a>
					<a class="dropdown-item" href="<?=$C["path"]?>/files/"><i class="fa fa-file" aria-hidden="true"></i> 檔案</a>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="manage" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-pencil" aria-hidden="true"></i> 管理</a>
				<div class="dropdown-menu" aria-labelledby="manage">
					<a class="dropdown-item" href="<?=$C["path"]?>/manageplans/"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> 教案</a>
					<a class="dropdown-item" href="<?=$C["path"]?>/managefiles/"><i class="fa fa-file" aria-hidden="true"></i> 檔案</a>
					<a class="dropdown-item" href="<?=$C["path"]?>/account/"><i class="fa fa-user" aria-hidden="true"></i> 帳號</a>
				</div>
			</li>
		</ul>
		<ul class="navbar-nav mt-2 mt-md-0">
			<li class="nav-item">
				<?php
				if ($U["islogin"]) {
					?>
					<a class="nav-link" href="<?=$C["path"]?>/logout/"><?=$U["account"]?> / <?=$U["name"]?> <i class="fa fa-sign-out" aria-hidden="true"></i> 登出</a>
					<?php
				} else {
					?>
					<a class="nav-link" href="<?=$C["path"]?>/login/"><i class="fa fa-sign-in" aria-hidden="true"></i> 登入</a>
					<?php
				}
				?>
			</li>
		</ul>
	</div>
</nav>
