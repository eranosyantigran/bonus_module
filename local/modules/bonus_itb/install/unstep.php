<?php
use \Bitrix\Main\Localization\Loc;
global $APPLICATION, $table, $step;

Loc::loadMessages(__FILE__);
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

$module_id = 'bonus_itb';

CModule::IncludeModule($module_id);

$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$step = 2;
if (!empty($request->getPost('delete_table'))){
    if ($request->getPost('DELETE_TABLE'))
        $table = "Y";
    else
        $table = "N";
}


?>

<form action="" method="post">

    <label for="DELETE_TABLE">
        <input type="hidden" name="delete_table" value="Y">
        <input type="radio" id="DELETE_TABLE" name="DELETE_TABLE" value="Y">
        <span>Удалить табицы</span>
    </label>

    <button class="btn" type="submit"> Удалить модуль  </button>
</form>
