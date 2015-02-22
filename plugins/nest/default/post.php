<?php
if(!defined("GOOSE")){exit();}


$url = GOOSE_ROOT.'/nest/'.$paramAction.'/';
$url .= ($nest_srl) ? $nest_srl.'/' : '';
$listCount = (isset($nest['listCount'])) ? $nest['listCount'] : 12;
$nest['json']['thumnail']['size'] = (isset($nest['json']['thumnail']['size'])) ? $nest['json']['thumnail']['size'] : array(100,100);
$nowSkin = $nest['json']['skin'];
$nowSkin = ($_GET['skin']) ? $_GET['skin'] : $nowSkin;
?>

<section class="goose-form">
	<div class="hgroup">
		<h1>둥지<?=$titleType?></h1>
	</div>
	<form action="<?=GOOSE_ROOT?>/nest/<?=$paramAction?>/" method="post" id="regsterForm">
		<input type="hidden" name="srl" value="<?=$nest_srl?>" />
		<input type="hidden" name="json" />

		<fieldset>
			<legend class="blind">둥지<?=$titleType?></legend>
			<dl class="table">
				<dt><label for="skin">Skin</label></dt>
				<dd>
					<select name="skin" id="skin">
						<?
						$tree = $goose->util->readDir(PWD.'/plugins/nest/');
						echo (!count($tree)) ? '<option>Empty skin</option>' : '';
						foreach($tree as $k=>$v)
						{
							$selected = ($v == $nowSkin) ? ' selected' : '';
							$selected = (!$nowSkin && $v=='basic') ? ' selected' : $selected;
							echo "<option value=\"$v\"$selected>$v</option>";
						}
						?>
					</select>
					<?
					if ($paramAction == "modify")
					{
						echo "<p>스킨이 바뀌면 변경되거나 없어지는 설정이 있으니 주의 바랍니다.</p>";
					}
					?>
				</dd>
			</dl>
			<?
			$group = $goose->spawn->getItems(array(
				'table' => 'nestGroups',
				'order' => 'srl',
				'sort' => 'asc'
			));
			if (count($group))
			{
			?>
				<dl class="table">
					<dt><label for="group_srl">둥지그룹</label></dt>
					<dd>
						<select name="group_srl" id="group_srl">
							<option value="0">선택하세요.</option>
							<?
							foreach ($group as $k=>$v)
							{
								$selected = ($v['srl'] == $nest['group_srl']) ? ' selected="selected"' : '';
								$selected = (!$selected && ($v['srl'] == $_SESSION['group_srl'])) ? ' selected="selected"' : $selected;
								echo "<option value=\"$v[srl]\"$selected>$v[name]</option>";
							}
							?>
						</select>
					</dd>
				</dl>
			<?
			}
			?>
			<dl class="table">
				<?
				$attr = (isset($nest['id'])) ? ' value="'.$nest['id'].'" readonly' : '';
				?>
				<dt><label for="id">아이디</label></dt>
				<dd>
					<input type="text" name="id" id="id" maxlength="20" size="22" placeholder="영문과 숫자 입력가능"<?=$attr?>/>
					<p>이 항목은 한번정하면 변경할 수 없습니다.</p>
				</dd>
			</dl>
			<dl class="table">
				<dt><label for="name">둥지이름</label></dt>
				<dd><input type="text" name="name" id="name" maxlength="100" size="22" value="<?=(isset($nest['name']))?$nest['name']:''?>"/></dd>
			</dl>
			<dl class="table">
				<dt><label for="thumWidth">썸네일사이즈</label></dt>
				<dd>
					<p style="margin:0 0 5px">
						<label>가로 : <input type="text" name="thumWidth" id="thumWidth" maxlength="4" size="5" value="<?=$nest['json']['thumnail']['size'][0]?>"/></label>
					</p>
					<p style="margin:5px 0 0">
						<label>세로 : <input type="text" name="thumHeight" id="thumHeight" maxlength="4" size="5" value="<?=$nest['json']['thumnail']['size'][1]?>"/></label>
					</p>
				</dd>
			</dl>
			<dl class="table">
				<dt><label>썸네일 축소방식</label></dt>
				<dd>
					<?
					$thumType1 = $thumType2 = $thumType3 = $thumType4 = null;
					if ($paramAction == "modify")
					{
						$thumType1 = ($nest['json']['thumnail']['type'] == "crop") ? ' checked = "checked"' : '';
						$thumType2 = ($nest['json']['thumnail']['type'] == "resize") ? ' checked = "checked"' : '';
						$thumType3 = ($nest['json']['thumnail']['type'] == "resizeWidth") ? ' checked = "checked"' : '';
						$thumType4 = ($nest['json']['thumnail']['type'] == "resizeHeight") ? ' checked = "checked"' : '';
					}
					else
					{
						$thumType1 = ' checked = "checked"';
					}
					?>
					<label><input type="radio" name="thumType" id="thumType1" value="crop"<?=$thumType1?>/> 자르기</label>
					<label><input type="radio" name="thumType" id="thumType2" value="resize"<?=$thumType2?>/> 리사이즈</label>
					<label><input type="radio" name="thumType" id="thumType3" value="resizeWidth"<?=$thumType3?>/> 리사이즈(가로기준)</label>
					<label><input type="radio" name="thumType" id="thumType4" value="resizeHeight"<?=$thumType4?>/> 리사이즈(세로기준)</label>
				</dd>
			</dl>
			<dl class="table">
				<dt><label for="listCount">목록수</label></dt>
				<dd>
					<input type="tel" name="listCount" id="listCount" maxlength="3" size="4" value="<?=$listCount?>"/>
					<p>한페이지에 출력되는 글 갯수입니다.</p>
				</dd>
			</dl>
			<dl class="table">
				<?
				$nest['useCategory'] = (isset($nest['useCategory'])) ? $nest['useCategory'] : null;
				$useCategoryYes = ($nest['useCategory'] == 1) ? ' checked = "checked"' : '';
				$useCategoryNo = ($nest['useCategory'] != 1) ? ' checked = "checked"' : '';
				?>
				<dt><label for="useCategory">분류사용</label></dt>
				<dd>
					<label><input type="radio" name="useCategory" id="useCategory" value="0" <?=$useCategoryNo?>/> 사용안함</label>
					<label><input type="radio" name="useCategory" value="1" <?=$useCategoryYes?>/> 사용</label>
				</dd>
			</dl>
			<dl class="table">
				<dt><label for="articleSkin">Article skin</label></dt>
				<dd>
					<select name="articleSkin" id="articleSkin">
						<?
						$tree = $goose->util->readDir(PWD.'/plugins/article/');
						echo (!count($tree)) ? '<option>ArticleSkin 없음</option>' : '';
						foreach($tree as $k=>$v)
						{
							$selected = ($v == $nest['json']['articleSkin']) ? ' selected' : '';
							$selected = (!$nest['json']['articleSkin'] && $v=='basic') ? ' selected' : $selected;
							echo "<option value=\"$v\"$selected>$v</option>";
						}
						?>
					</select>
				</dd>
			</dl>
			<dl class="table">
				<dt><label for="listType">List type</label></dt>
				<dd>
					<select name="listType" id="listType">
						<?
						foreach($listTypes as $k=>$v)
						{
							$selected = ($v == $nest['json']['listType']) ? ' selected' : '';
							$selected = (!$nest['json']['listType'] && $v=='card') ? ' selected' : $selected;
							echo "<option value=\"$v\"$selected>$v</option>";
						}
						?>
					</select>
				</dd>
			</dl>
		</fieldset>

		<nav class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight"><?=$titleType?></button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">뒤로가기</button></span>
		</nav>
	</form>
</section>

<script src="<?=$jQueryAddress?>"></script>
<script src="<?=GOOSE_ROOT?>/libs/ext/validation/jquery.validate.min.js"></script>
<script src="<?=GOOSE_ROOT?>/libs/ext/validation/localization/messages_ko.js"></script>
<script>
jQuery(function($){

	// change skin
	$('#skin').on('change', function(){
		location.href = '<?=$url?>?skin=' + $(this).val();
	});

	$.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
	});
	$('#regsterForm').validate({
		rules : {
			id : {required : true, minlength : 3, alphanumeric : true}
			,name : {required: true, minlength: 2}
			,thumWidth : {required: true, minlength: 2, number: true}
			,thumHeight : {required: true, minlength: 2, number: true}
			,listCount : {required: true, number: true}
		}
		,messages : {
			id : {alphanumeric: '알파벳과 숫자만 사용가능합니다.'}
		}
		,submitHandler : function(form) {
			var json = {
				skin : form.skin.value
				,thumnail : {
					size : [form.thumWidth.value, form.thumHeight.value]
					,type : $(form.thumType).filter(':checked').val()
				}
				,articleSkin : form.articleSkin.value
				,listType : form.listType.value
			};

			form.json.value = encodeURIComponent(JSON.stringify(json));

			form.submit();
			return false;
		}
	});
});
</script>