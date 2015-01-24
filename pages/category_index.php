<?php
if(!defined("GOOSE")){exit();}

$nest_srl = (int)$routePapameters['param0'];
if ($nest_srl)
{
	$nest = $goose->spawn->getItem(array(
		'table' => 'nests',
		'where' => 'srl='.$nest_srl
	));
	$categoryCount = $goose->spawn->getCount(array(
		'table' => 'categories',
		'where' => 'nest_srl='.(int)$nest['srl']
	));
}
else
{
	$goose->util->back('둥지값이 없습니다.');
	exit;
}
?>

<section>
	<div class="hgroup">
		<h1><?=$nest['name']?> 분류목록</h1>
	</div>
	<form action="<?=GOOSE_ROOT?>/category/sort/" method="post" name="listForm" id="listForm">
		<input type="hidden" name="nest_srl" value="<?=$nest_srl?>" />
		<input type="hidden" name="srls" value=""/>
		<fieldset>
			<legend class="blind">분류목록</legend>
			<ul id="index" class="goose-index list">
				<?
				if ($categoryCount > 0)
				{
					$items = $goose->spawn->getItems(array(
						'table' => 'categories',
						'where' => 'nest_srl='.$nest_srl,
						'order' => 'turn',
						'sort' => 'asc'
					));
					foreach($items as $k=>$v)
					{
						$count = $goose->spawn->getCount(array(
							'table' => 'articles',
							'where' => 'category_srl='.(int)$v['srl']
						));
				?>
						<li srl="<?=$v['srl']?>">
							<dl>
								<dd>
									<strong class="big"><?=$v['srl']?>- <?=$v['name']?>(<?=$count?>)</strong>
									<nav>
										<a href="<?=GOOSE_ROOT?>/category/modify/<?=$nest_srl?>/<?=$v['srl']?>/">수정</a>
										<a href="<?=GOOSE_ROOT?>/category/delete/<?=$nest_srl?>/<?=$v['srl']?>/">삭제</a>
									</nav>
								</dd>
							</dl>
						</li>
				<?
					}
				}
				else
				{
				?>
					<li class="empty">데이터가 없습니다.</li>
				<?
				}
				?>
			</ul>
		</fieldset>
		<nav class="btngroup">
			<span><a href="<?=GOOSE_ROOT?>/category/create/<?=$nest_srl?>/" class="ui-button btn-highlight">분류추가</a></span>
			<span><a href="javascript:;" onclick="onSubmit(document.listForm)" class="ui-button">순서변경</a></span>
			<span><a href="<?=GOOSE_ROOT?>/article/index/<?=$nest_srl?>/" class="ui-button">문서목록</a></span>
			<?
			$url = GOOSE_ROOT.'/nest/index/';
			$url .= ($_SESSION['group_srl']) ? $_SESSION['group_srl'].'/' : '';
			?>
			<span><a href="<?=$url?>" class="ui-button">둥지목록</a></span>
		</nav>
	</form>
</section>

<script src="<?=$jQueryAddress?>" type="text/javascript"></script>
<script type="text/javascript" src="<?=GOOSE_ROOT?>/libs/ext/dragsort/jquery.dragsort-0.5.1.min.js"></script>
<script type="text/javascript">
jQuery(function($){
	var objs = new Object();
	objs.lst = $('#index');
	objs.form = $('#listForm');
	
	objs.lst.dragsort({
		dragSelector : 'li:not(".empty")'
		,dragBetween : false
		,dragSelectorExclude : 'a'
		,dragEnd : function()
		{
			var srls = objs.lst.children('li').map(function(){
				return $(this).attr('srl')
			}).get().join(',');
			objs.form.children('input[name=srls]').val(srls);
		}
		,placeHolderTemplate : '<li class="placeHolder"><dl></dl></li>'
	});
});

function onSubmit(frm)
{
	if (confirm('순서를 바꾸시겠습니까?'))
	{
		frm.submit();
	}
	else
	{
		return false;
	}
}
</script>
