<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    Itb\Entity\BonusEventTable;
Loc::loadMessages(__FILE__);

global $APPLICATION;

$arTable = array("TABLE_NAME" => "bonus_list");
$editlink = 'bonus_edit.php';

$module_id='bonus_itb';
\Bitrix\Main\Loader::includeModule($module_id);

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$oSort = new CAdminSorting($arTable["TABLE_NAME"], "ID", "desc");
$lAdmin = new CAdminList($arTable["TABLE_NAME"], $oSort);

$rsData =  BonusEventTable::getList();

$rsData = new CAdminResult($rsData, $arTable["TABLE_NAME"]);
$rsData->NavStart();

$lAdmin->AddHeaders(array(
    array(  "id"    =>"id",
        "content"  =>"ID",
        "sort"     =>"id",
        "default"  =>true,
    ),
    array(  "id"    =>"type",
        "content"  =>GetMessage("ITB_BONUS_OPERATION_TYPE"),
        "sort"     =>"type",
        "default"  =>true,
    ),
    array(  "id"    =>"name",
        "content"  => GetMessage("ITB_BONUS_OPERATION_NAME"),
        "sort"     =>"name",
        "default"  =>true,
    ),
    array(  "id"    =>"status",
        "content"  => GetMessage("ITB_BONUS_OPERATION_STATUS"),
        "sort"     =>"status",
        "default"  =>true,
    ),
    array(  "id"    =>"user",
        "content"  => GetMessage("ITB_BONUS_USER_ID"),
        "sort"     =>"user",
        "default"  =>true,
    )
));


while ($aritem = $rsData->Fetch()){

    $rows = $lAdmin->AddRow($aritem['ID'], $aritem);

    $rows->AddViewField("id", '<a href="./'.$editlink.'?lang='.SITE_ID.'&id='.$aritem['ID'].'">'.$aritem['ID'].'</a>');

    $rows->AddViewField("type", $aritem['TYPE']);
    $rows->AddViewField("name", $aritem['NAME']);
    $rows->AddViewField("status", $aritem['ACTIVE']);

    $rsUser = \CUser::GetByID($aritem["USER"]);
    $arUser = $rsUser->Fetch();
    $rows->AddViewField("user", '[<a target="_blank" href="/bitrix/admin/user_edit.php?ID='.$arUser["ID"].'&lang='.LANGUAGE_ID.'">'.$arUser["ID"].'</a>] ('.$arUser["LOGIN"].') '.$arUser["NAME"].' '.$arUser["LAST_NAME"]);

    $arActions = Array();
    $arActions[] = array(
        "ICON"=>"edit",
        "DEFAULT"=>true,
        "TEXT"=>GetMessage("ITB_BONUS_PROFILE_EDIT"),
        "ACTION"=>$lAdmin->ActionRedirect("./".$editlink."?id=".$aritem['ID'])
    );

    $rows->AddActions($arActions);

}

$lAdmin->AddFooter(
    array(
        array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
        array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
    )
);
$BonusTypes = array(
    "order" => GetMessage("ITB_BONUS_PROFILE_ORDER"),
);

foreach($BonusTypes as $keyBonusType => $NameBonusType):
    $BonusTypesMenu[] = array(
        "TEXT" => $NameBonusType,
        "ACTION" => $lAdmin->ActionRedirect("./".$editlink."?id=new&type=".$keyBonusType)
    );
endforeach;

$aContext = array(
    array(
        "TEXT" => GetMessage("ITB_BONUS_PROFILES_ADD_ACTION"),
        "LINK" => $editlink."?lang=".LANGUAGE_ID."&id=new",
        "TITLE" => GetMessage("ITB_BONUS_PROFILES_ADD_ACTION"),
        "ICON" => "btn_new",
        "MENU" => $BonusTypesMenu
    )
);
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("ITB_BONUS_EXIT_LIST"));

$lAdmin->DisplayList();

