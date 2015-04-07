<?
if(!defined("GOOSE")){exit();}

$usersCount = $goose->spawn->getCount(array('table'=>'users'));
$users = $goose->spawn->getItems(array(
	'table' => 'users',
	'order' => 'srl',
	'sort' => 'desc'
));
?>

<section>
	<div class="hgroup">
		<h1><a href="<?=GOOSE_ROOT?>/user/index/">사용자 목록</a></h1>
	</div>
	<?
	if ($usersCount)
	{
	?>
		<!-- index -->
		<ul class="goose-index list">
			<?
			foreach ($users as $k=>$v)
			{
				if ($goose->isAdmin || ($_SESSION['gooseEmail'] == $v['email']))
				{
			?>
					<li>
						<dl>
							<dd>
								<strong class="big"><?=$v['name']?></strong>
								<div class="inf">
									<span>E-Mail: <?=$v['email']?></span>
									<span>Date: <?=$goose->util->convertDate($v['regdate'])?></span>
									<span>Level: <?=$v['level']?></span>
								</div>
								<nav>
									<a href="<?= GOOSE_ROOT ?>/user/modify/<?= $v['srl'] ?>/">수정</a>
									<a href="<?= GOOSE_ROOT ?>/user/delete/<?= $v['srl'] ?>/">삭제</a>
								</nav>
							</dd>
						</dl>
					</li>
			<?
				}
			}
			?>
		</ul>
		<!-- // index -->
	<?
	}
	?>
	<nav class="btngroup">
		<span><a href="<?=GOOSE_ROOT?>/user/index/" class="ui-button">목록</a></span>
		<?
		if ($goose->isAdmin)
		{
		?>
			<span><a href="<?= GOOSE_ROOT ?>/user/create/" class="ui-button btn-highlight">사용자 등록</a></span>
		<?
		}
		?>
	</nav>
</section>