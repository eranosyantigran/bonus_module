<?php
use \Bitrix\Main\Localization\Loc;
global $APPLICATION;

Loc::loadMessages(__FILE__);
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

$module_id = 'bonus_itb';

CModule::IncludeModule($module_id);

$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);



?>

<form action="<?echo $APPLICATION->GetCurPage()?>" method="post">

    <label for="DELETE_TABLE">
        <input type="checkbox" id="DELETE_TABLE" name="DELETE_TABLE">
        <span>Удалить табицы</span>
    </label>

    <button class="btn" type="submit"></button>
</form>

