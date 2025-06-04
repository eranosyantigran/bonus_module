<?php
use Bitrix\Main\ModuleManager;
use \Bitrix\Main\Localization\Loc;
use Itb\Bonus\Entity\BonusEventTable;
use Itb\Bonus\Entity\BonusAddTable;
use Bitrix\Main\Application;
use Itb\Bonus\Install\BonusInstallFirst;
use Itb\Bonus\Install\UserProps;
use Itb\Bonus\Install\OrderProps;
IncludeModuleLangFile(__FILE__);

Class Bonus_Itb extends CModule
{
    public $MODULE_ID = 'bonus_itb';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__)."/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("BONUS_ITB_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("BONUS_ITB_MODULE_DESC");
    }

    public function InstallDB()
    {
        $connection = Application::getConnection();
        if (!$connection->isTableExists(BonusEventTable::getTableName()) && !$connection->isTableExists(BonusAddTable::getTableName())) {
            BonusEventTable::getEntity()->createDbTable();
            BonusAddTable::getEntity()->createDbTable();
        }
    }

    public function UnInstallDB()
    {
        $connection = Application::getConnection();

        if ($connection->isTableExists(BonusEventTable::getTableName()) && $connection->isTableExists(BonusAddTable::getTableName())) {
            $connection->dropTable(BonusEventTable::getTableName());
            $connection->dropTable(BonusAddTable::getTableName());
        }
    }

    public function DoInstall()
    {
        $this->runComposerInstall();

        $autoloadPath = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        }

        $this->InstallDB();

        //Добавление дополнительного поля для пользователя
        UserProps::AddProps();

        //Добавление групп и свойств в ОРДЕР
        OrderProps::AddOrderProps();

        BonusInstallFirst::BonusInstall();
        //Добавление платёжной системой
        OrderProps::InstallPaySystem();

        include($_SERVER['DOCUMENT_ROOT'].'/local/modules/bonus_itb/options.php');

        CopyDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
        CopyDirFiles(dirname(__FILE__)."/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/bonus_itb/", true, true);

        RegisterModuleDependences("sale","OnSaleOrderPaid","bonus_itb","SaleOrderPaid","SaleOrderPaidAddBonus");
        RegisterModuleDependences(
            "sale",
            "OnSaleComponentOrderResultPrepared",
            "bonus_itb",
            "Itb\\Bonus\\Event\\OrderResultPrepared",
            "OnSaleComponentOrderResultPrepared"
        );
        RegisterModuleDependences("sale","OnSaleOrderBeforeSaved","bonus_itb","Itb\Bonus\Event\OnSaleOrderSaved","OnSaleOrderBeforeSaved");
        RegisterModuleDependences("sale","OnSaleOrderSaved","bonus_itb","Itb\Bonus\Event\OnSaleOrderSaved","OrderAfterSaved", 1);
        ModuleManager::RegisterModule($this->MODULE_ID);

        return true;
    }

    public function DoUninstall()
    {
        $autoloadPath = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        }

        $this->UnInstallDB();

        UserProps::DeleteProps();
        OrderProps::DeleteOrderProps();
        OrderProps::DeletePaySystem();
        DeleteDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

        DeleteDirFilesEx("/bitrix/js/bonus_itb");

        UnRegisterModuleDependences("sale","OnSaleOrderPaid","bonus_itb","SaleOrderPaid","SaleOrderPaidAddBonus");

        ModuleManager::UnRegisterModule($this->MODULE_ID);
        return true;
    }

    private function runComposerInstall()
    {
        $moduleDir = realpath(__DIR__ . '/..');
        $composer = $moduleDir . '/composer.phar';

        $cmd = 'HOME=/tmp php ' . escapeshellarg($composer) . ' install --no-interaction --no-dev --working-dir=' . escapeshellarg($moduleDir) . ' 2>&1';
        exec($cmd, $output, $resultCode);

        if ($resultCode !== 0) {
            echo '<div style="color:red">⚠️ Composer install завершился с ошибкой:</div>';
            echo '<pre>' . htmlspecialchars(implode("\n", $output)) . '</pre>';
        } else {
            echo '<div style="color:green">✅ Composer зависимости успешно установлены.</div>';
        }
    }

}