<?php
if(!defined("GOOSE")){exit();}

$module_srl = (int)$routePapameters['param0'];
if ($module_srl)
{
	$module = $spawn->getItem(array(
		'table' => $tablesName[modules],
		'where' => 'srl='.$module_srl
	));
	$categoryCount = $spawn->getCount(array(
		'table' => $tablesName[categories],
		'where' => 'module_srl='.(int)$module[srl]
	));
}
else
{
	$util->back('모듈값이 없습니다.');
	exit;
}
?>

<section>
	<div class="hgroup">
		<h1><?=$module[name]?> 분류목록</h1>
	</div>
	<form action="<?=ROOT?>/category/sort/" method="post" name="listForm" id="listForm" class="hidden">
		<input type="hidden" name="module_srl" value="<?=$module_srl?>" />
		<input type="hidden" name="srls" value=""/>
		<fieldset>
			<legend class="blind">분류목록</legend>
			<ul id="index" class="index">
				<?
				if ($categoryCount > 0)
				{
					$items = $spawn->getItems(array(
						'table' => $tablesName[categories],
						'where' => 'module_srl='.$module_srl,
						'order' => 'turn',
						'sort' => 'asc'
					));
					foreach($items as $k=>$v)
					{
						$count = $spawn->getCount(array(
							'table' => $tablesName[articles],
							'where' => 'category_srl='.(int)$v[srl]
						));
						$gets = "&table_srl=$table_srl&category_srl=$doc[srl]";
				?>
						<li srl="<?=$v[srl]?>">
							<div class="body">
								<strong><?=$v[name]?>(<?=$count?>)</strong>
								<nav>
									<a href="<?=ROOT?>/category/modify/<?=$module_srl?>/<?=$v[srl]?>/">수정</a>
									<a href="<?=ROOT?>/category/delete/<?=$module_srl?>/<?=$v[srl]?>/">삭제</a>
								</nav>
							</div>
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
			<span><a href="<?=ROOT?>/category/create/<?=$module_srl?>/" class="ui-button btn-highlight">분류추가</a></span>
			<span><a href="javascript:;" onclick="onSubmit(document.listForm)" class="ui-button">순서변경</a></span>
			<span><a href="<?=ROOT?>/article/index/<?=$module_srl?>/" class="ui-button">문서목록</a></span>
			<?
			$url = ROOT.'/module/index/';
			$url .= ($module[group_srl]) ? $module[group_srl].'/' : '';
			?>
			<span><a href="<?=$url?>" class="ui-button">모듈목록</a></span>
		</nav>
	</form>
</section>

<script src="<?=$jQueryAddress?>" type="text/javascript"></script>
<script type="text/javascript" src="<?=ROOT?>/pages/src/pkg/dragsort/jquery.dragsort-0.5.1.min.js"></script>
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
		,placeHolderTemplate : '<li class="placeHolder"></li>'
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
