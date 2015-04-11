<?php
// 필드수정
$add_field_tempFiles = $goose->spawn->action("
	ALTER TABLE `".$goose->tablesName['tempFiles']."`
		ADD `type` VARCHAR(50) NULL DEFAULT NULL AFTER `name`,
		ADD `size` BIGINT(11) NULL DEFAULT NULL AFTER `type`
");

if($add_field_tempFiles)
{
	echo "<p>tempFiles - complete fix<p>";
}


$add_field_files = $goose->spawn->action("
	ALTER TABLE `".$goose->tablesName['files']."`
		ADD `type` VARCHAR(50) NULL DEFAULT NULL AFTER `loc`,
		ADD `size` BIGINT(11) NULL DEFAULT NULL AFTER `type`,
		ADD `date` VARCHAR(14) NULL DEFAULT NULL AFTER `size`
");

if($add_field_files)
{
	echo "<p>files - complete fix<p>";
}