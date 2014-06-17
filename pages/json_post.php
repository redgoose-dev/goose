<?php
if(!defined("GOOSE")){exit();}

$json_srl = ($routePapameters['param0']) ? (int)$routePapameters['param0'] : null;

if ($paramAction !== "create" and $json_srl)
{
	$json = $spawn->getItem(array(
		'table' => $tablesName[jsons],
		'where' => 'srl='.$json_srl
	));
}

$titleType = ($paramAction == 'create') ? '만들기' : '';
$titleType = ($paramAction == 'modify') ? '수정' : $titleType;
$titleType = ($paramAction == 'delete') ? '삭제' : $titleType;
?>

<link rel="stylesheet" type="text/css" href="<?=ROOT?>/pages/src/pkg/jsonEditor/JSONManager.css" media="screen" />

<section class="form JSONPost">
	<div class="hgroup">
		<h1>JSON <?=$titleType?></h1>
	</div>
	<form action="<?=ROOT?>/json/<?=$paramAction?>/" method="post" name="post" onsubmit="return onCheck(this);">
		<input type="hidden" name="srl" value="<?=$json[srl]?>" />
		<?
		if ($paramAction == "delete")
		{
		?>
			<script type="text/javascript">
			function onCheck(frm)
			{
				return true;
			}
			</script>
			<fieldset>
				<legend class="blind">JSON<?=$titleType?></legend>
				<p class="message">"<?=$json[name]?>" JSON 데이터를 삭제하시겠습니까? 삭제된 JSON 데이터는 복구할 수 없습니다.</p>
			</fieldset>
		<?
		}
		else
		{
		?>
			<script type="text/javascript">
			function onCheck(frm)
			{
				if (!frm.name.value)
				{
					alert('이름 항목이 비었습니다.');
					frm.name.focus();
					return false;
				}

				return true;
			}
			</script>
			<input type="hidden" name="json" />
			<fieldset>
				<legend class="blind">JSON<?=$titleType?></legend>
				<dl class="table">
					<dt><label for="name">이름</label></dt>
					<dd><input type="text" name="name" id="name" size="22" maxlength="20" value="<?=$json[name]?>"/></dd>
				</dl>
				<dl>
					<dt><label>JSON DATA</label></dt>
					<dd id="jsonIndex">
						<ul>
							<li type="Object" loc="root" class="on">
								<dl>
									<dt>
										<button type="button" role="control">control</button>
										<button type="button" role="toggle">toggle</button>
										<em class="no">0</em><strong data-ph="Object"></strong><span class="type"></span><em class="count">0</em>
									</dt>
								</dl>
								<ul></ul>
							</li>
						</ul>
						<nav id="context">
							<ul>
								<li role="Type">
									<div>
										<ul>
											<li role="Object">
												<button type="button">Object</button>
											</li>
											<li role="Array">
												<button type="button">Array</button>
											</li>
											<li role="String">
												<button type="button">String</button>
											</li>
										</ul>
									</div>
									<button type="button">Type</button>
								</li>
								<li role="Insert">
									<div>
										<ul>
											<li role="Object">
												<button type="button">Object</button>
											</li>
											<li role="Array">
												<button type="button">Array</button>
											</li>
											<li role="String">
												<button type="button">String</button>
											</li>
										</ul>
									</div>
									<button type="button">Insert</button>
								</li>
								<li role="Duplicate">
									<button type="button">Duplicate</button>
								</li>
								<li role="Remove">
									<button type="button">Remove</button>
								</li>
							</ul>
						</nav>
					</dd>
				</dl>
			</fieldset>
		<?
		}
		?>
		<nav class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight"><?=$titleType?></button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">뒤로가기</button></span>
		</nav>
	</form>
</section>

<?
if ($paramAction != "delete")
{
?>
	<script>
	var jsonData = '<?=$json[json]?>';
	</script>
	<script src="<?=$jQueryAddress?>"></script>
	<script src="<?=ROOT?>/pages/src/pkg/jsonEditor/jquery-sortable.min.js"></script>
	<script src="<?=ROOT?>/pages/src/pkg/jsonEditor/JSONManager.min.js"></script>
<?
}
?>