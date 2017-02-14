<nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
	<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<a class="navbar-brand" href="<?=$C["path"]?>/"><?=$C["sitename"]?></a>
	<div class="collapse navbar-collapse" id="navbarCollapse">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="<?=$C["path"]?>/">首頁</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="search" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">查詢</a>
				<div class="dropdown-menu" aria-labelledby="search">
					<a class="dropdown-item" href="<?=$C["path"]?>/plans/">教案</a>
					<a class="dropdown-item" href="<?=$C["path"]?>/files/">檔案</a>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="manage" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">管理</a>
				<div class="dropdown-menu" aria-labelledby="manage">
					<a class="dropdown-item" href="<?=$C["path"]?>/manageplans/">教案</a>
					<a class="dropdown-item" href="<?=$C["path"]?>/managefiles/">檔案</a>
				</div>
			</li>
		</ul>
	</div>
</nav>
