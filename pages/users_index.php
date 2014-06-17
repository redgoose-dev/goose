<?
if(!defined("GOOSE")){exit();}

$usersCount = $spawn->getCount(array('table'=>$tablesName[users]));
$users = $spawn->getItems(array(
	'table' => $tablesName[users],
	'order' => 'srl',
	'sort' => 'desc'
));
?>

<section>
	<div class="hgroup">
		<h1><a href="<?=ROOT?>/users/index/">사용자 목록</a></h1>
	</div>

	<?
	if ($usersCount)
	{
	?>
		<!-- index -->
		<ul class="index">
			<?
			foreach ($users as $k=>$v)
			{
				$util->console($v);
			?>
				<li>
					<div class="body">
						<a><strong><?=$v['name']?></strong></a>
						<div class="inf">
							<span>이메일:<?=$v['email']?></span>
							<span>등록날짜:<?=$util->convertDate($v['regdate'])?></span>
							<span>Level:<?=$v['level']?></span>
						</div>
						<nav>
							<a href="<?=ROOT?>/users/modify/<?=$v['srl']?>/">수정</a>
							<a href="<?=ROOT?>/users/delete/<?=$v['srl']?>/">삭제</a>
						</nav>
					</div>
				</li>
			<?
			}
			?>
		</ul>
		<!-- // index -->
	<?
	}
	?>
	<nav class="btngroup">
		<span><a href="<?=ROOT?>/users/index/" class="ui-button">목록</a></span>
		<span><a href="<?=ROOT?>/users/create/" class="ui-button btn-highlight">사용자 등록</a></span>
	</nav>
</section>