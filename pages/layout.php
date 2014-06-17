<?php
if(!defined("GOOSE")){exit();}
?>

<!doctype html>
<html lang="ko-kr">
<head>
<meta charset="utf-8">
<title>Goose Admin</title>
<link rel="icon" type="image/x-icon" href="/icons/redgoose_256x256x32.png">
<meta name="author" content="Goose Admin">
<meta name="generator" content="redgoose">
<meta http-equiv="Content-Language" content="kr">
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<link rel="stylesheet" type="text/css" href="<?=ROOT?>/pages/src/css/layout.css" media="screen" />
<script>function log(o){console.log(o);}</script>
</head>
<body>
<main>
	<!-- Header -->
	<header id="header">
		<h1><a href="<?=ROOT?>/">Goose</a></h1>
		<nav>
			<ul>
				<li><a href="<?=ROOT?>/module/index/">Modules</a></li>
				<li><a href="<?=ROOT?>/users/index/">Users</a></li>
				<li><a href="<?=ROOT?>/json/index/">JSON</a></li>
				<li><a href="<?=ROOT?>/api/">API</a></li>
				<li><a href="<?=ROOT?>/help/">Help</a></li>
				<?
				if ($_SESSION['gooseEmail'])
				{
					echo "<li><a href='".ROOT."/auth/logout/'>Logout</a></li>";
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
		<address>Copyright 2012 Goose Engine. All right reserved.</address>
	</footer>
	<!-- // Footer -->
</main>
</body>
</html>
