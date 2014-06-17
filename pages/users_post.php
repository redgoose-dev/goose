<?
if(!defined("GOOSE")){exit();}

$titleType = ($paramAction == 'create') ? '등록' : '';
$titleType = ($paramAction == 'modify') ? '수정' : $titleType;
$titleType = ($paramAction == 'delete') ? '삭제' : $titleType;
?>

<script type="text/javascript">
function onCheck(frm)
{
	if (!frm.id.value)
	{
		alert('모듈아이디 항목이 비었습니다.');
		frm.id.focus();
		return false;
	}
	
	if (!frm.id.value.match(/^[a-zA-Z0-9]+$/))
	{
		alert('모듈아이디는 영문과 숫자로 작성해주세요.');
		frm.id.focus();
		return false;
	}

	if (!frm.name.value)
	{
		alert('모듈이름 항목이 비었습니다.');
		frm.name.focus();
		return false;
	}
	
	if (!frm.listCount.value.match(/^[0-9]+$/))
	{
		alert('번호로 써주세요.');
		frm.listCount.focus();
		return false;
	}
	
	return true;
}
</script>

<section class="form">
	<div class="hgroup">
		<h1>사용자 <?=$titleType?></h1>
	</div>
	<form action="<?=ROOT?>/users/<?=$paramAction?>/" method="post" onsubmit="return onCheck(this);">
	
	</form>
	...
</section>