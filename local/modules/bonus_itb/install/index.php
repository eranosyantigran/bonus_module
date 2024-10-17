<?php
use Bitrix\Main\ModuleManager;
use \Bitrix\Main\Localization\Loc;
use Itb\Entity\BonusEventTable;
IncludeModuleLangFile(__FILE__);

Class Bonus_Itb extends CModule
{
    var $MODULE_ID = 'bonus_itb';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__)."/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("BONUS_ITB_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("BONUS_ITB_MODULE_DESC");
    }

    function InstallDB()
    {
        global $DB;

        $DB->RunSQLBatch(__DIR__ .'/db/mysql/install.sql');

    }

    function UnInstallDB()
    {
        global $DB;

        $DB->RunSQLBatch(__DIR__ .'/db/mysql/uninstall.sql');
    }

    function DoInstall()
    {

        $this->InstallDB();

        //Добавление дополнительного поля для пользователя
        include(dirname(__FILE__)."/include/add_user_prop.php");

        //Добавление групп и свойств в ОРДЕР
        include(dirname(__FILE__)."/include/add_order_props.php");

        include(dirname(__FILE__)."/include/bonus_install.php");

        //Добавление платёжной системой
        include(dirname(__FILE__)."/include/paysystem_install.php");

        include($_SERVER['DOCUMENT_ROOT'].'/local/modules/bonus_itb/options.php');

        CopyDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
        CopyDirFiles(dirname(__FILE__)."/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/bonus_itb/", true, true);

        RegisterModuleDependences("sale","OnSaleOrderPaid","bonus_itb","SaleOrderPaid","SaleOrderPaidAddBonus");
        RegisterModuleDependences("sale","OnSaleComponentOrderResultPrepared","bonus_itb","\Itb\Bonus\Event\OrderResultPrepared","OnSaleComponentOrderResultPrepared");
        RegisterModuleDependences("sale","OnSaleOrderBeforeSaved","bonus_itb","\Itb\Bonus\Event\OnSaleOrderSaved","OnSaleOrderBeforeSaved");

        ModuleManager::RegisterModule($this->MODULE_ID);

        return true;
    }

    function DoUninstall()
    {

        $this->UnInstallDB();

        include(dirname(__FILE__)."/include/delete_user_props.php");
        include(dirname(__FILE__)."/include/delete_order_props.php");
        include(dirname(__FILE__)."/include/del_paysystem.php");
        DeleteDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

        DeleteDirFilesEx("/bitrix/js/bonus_itb");

        UnRegisterModuleDependences("sale","OnSaleOrderPaid","bonus_itb","SaleOrderPaid","SaleOrderPaidAddBonus");

        ModuleManager::UnRegisterModule($this->MODULE_ID);
        return true;
    }

}