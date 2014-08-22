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
		<ul class="goose-index">
			<?
			foreach ($users as $k=>$v)
			{
				$level = ($v['level'] == 1) ? '관리자' : '일반';
			?>
				<li>
					<div class="body">
						<a><strong><?=$v['name']?></strong></a>
						<div class="inf">
							<span>이메일:<?=$v['email']?></span>
							<span>등록날짜:<?=$goose->util->convertDate($v['regdate'])?></span>
							<span>권한:<?=$level?></span>
						</div>
						<nav>
							<a href="<?=GOOSE_ROOT?>/user/modify/<?=$v['srl']?>/">수정</a>
							<a href="<?=GOOSE_ROOT?>/user/delete/<?=$v['srl']?>/">삭제</a>
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
		<span><a href="<?=GOOSE_ROOT?>/user/index/" class="ui-button">목록</a></span>
		<span><a href="<?=GOOSE_ROOT?>/user/create/" class="ui-button btn-highlight">사용자 등록</a></span>
	</nav>
</section>