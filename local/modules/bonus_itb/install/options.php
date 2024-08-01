<?php
$module_id = 'bonus_itb';

if (is_dir($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/'.$module_id.'/'))
    require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/include.php');
else
    require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/'.$module_id.'/include.php');

IncludeModuleLangFile(__FILE__);


?>
