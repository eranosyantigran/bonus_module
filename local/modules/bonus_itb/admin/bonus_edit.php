<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

use \Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    Itb\Entity\BonusEventTable,
    Itb\Bonus\Conditions;

$module_id='bonus_itb';
\Bitrix\Main\Loader::includeModule($module_id);

$request = \Bitrix\Main\Context::getCurrent()->getRequest();

if($request['id'] == 'new'){
    $bonus_id = 'new';
    if($request['type'])
        $bonus_type = $request['type'];
    else
        $bonus_type = 'order';
}else{
    $bonus_id = (int)$request['id'];

    $rs =  BonusEventTable::getList([
        "filter" => ['ID' => $bonus_id],
    ])->Fetch();
    $bonus_type = $rs["type"];

    if($request['action'] == 'copy')
        $bonus_id = 'new';
}

if (is_dir($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/bonus_itb/'))
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bonus_itb/admin/bonus_type/".$bonus_type.".php");
else
    require($_SERVER["DOCUMENT_ROOT"]."/local/modules/bonus_itb/admin/bonus_type/".$bonus_type.".php");


