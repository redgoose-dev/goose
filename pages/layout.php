<?php
if(!defined("GOOSE")){exit();}
?>

<!doctype html>
<html lang="ko-kr">
<head>
<meta charset="utf-8">
<title>Goose Admin</title>
<meta name="author" content="Goose Admin">
<meta name="generator" content="redgoose">
<meta http-equiv="Content-Language" content="kr">
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<link rel="stylesheet" type="text/css" href="<?=GOOSE_ROOT?>/pages/src/css/layout.css" media="screen" />
</head>
<body>
<main>
	<!-- Header -->
	<header id="header">
		<h1><a href="<?=GOOSE_ROOT?>/">Goose</a></h1>
		<nav>
			<ul>
				<li><a href="<?=GOOSE_ROOT?>/nest/index/">Nests</a></li>
				<li><a href="<?=GOOSE_ROOT?>/user/index/">Users</a></li>
				<li><a href="<?=GOOSE_ROOT?>/json/index/">JSON</a></li>
				<li><a href="<?=GOOSE_ROOT?>/api/">API</a></li>
				<li><a href="<?=GOOSE_ROOT?>/help/">Help</a></li>
				<?
				if ($_SESSION['gooseEmail'])
				{
					echo "<li><a href='".GOOSE_ROOT."/auth/logout/'>Logout</a></li>";
				}
				?>
			</ul>
		</nav>
	</header>
	<!-- // Header -->

	<!-- Container -->
	<div id="container">
		<?
		require($containerDirectory);
		?>
	</div>
	<!-- // Container -->

	<!-- Footer -->
	<footer id="footer">
		<address>Copyright <?=date("Y")?> Goose Engine. All right reserved.</address>
	</footer>
	<!-- // Footer -->
</main>
</body>
</html>
