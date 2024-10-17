<?php
use \Bitrix\Main\Localization\Loc;
global $APPLICATION;

Loc::loadMessages(__FILE__);
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

$module_id = 'bonus_itb';

CModule::IncludeModule($module_id);

CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");



require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/'.$module_id.'/classes/module-options/version_1.php');


?>

