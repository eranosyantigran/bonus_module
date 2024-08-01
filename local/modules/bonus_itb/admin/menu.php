<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

CModule::IncludeModule("bonus_itb");

$aMenu = [
    [
        'parent_menu' => 'global_menu_marketing',
        'sort' => 0,
        'text' => GetMessage("BONUS_ITB_ADMIN_MENU"),
        'title' => GetMessage("BONUS_ITB_ADMIN_MENU"),
        'url' => '',
        'items_id' => 'bonus_itb',
        "items" => [
            [
                "url" => "/bitrix/admin/bonus_list.php?lang=".LANGUAGE_ID,
                "title" => GetMessage("BONUS_ITB_MENU_POINT"),
                "text" => GetMessage("BONUS_ITB_MENU_POINT")
            ],
        ]
    ]
];

return $aMenu;