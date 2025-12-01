<?php


$autoloadPath = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

\Bitrix\Main\Loader::registerAutoLoadClasses('bonus_itb', array(
        'Itb\Bonus\Ajax\SaleOrderAjax' => "lib/Ajax/SaleOrderAjax.php",
        'Itb\Bonus\Helper\ItbHelpers' => "lib/Helper/ItbHelpers.php",
        'Itb\Bonus\Class\CalculateBonus' => "lib/Class/CalculateBonus.php",
        'Itb\Bonus\Class\SaleOrderPaid' => "lib/Class/SaleOrderPaid.php",
        'Itb\Bonus\Entity\BonusEventTable' => "lib/Entity/BonusEventTable.php",
        'Itb\Bonus\Entity\BonusAddTable' => "lib/Entity/BonusAddTable.php",
        'Itb\Bonus\Event\BonusOrder' => "lib/Event/BonusOrder.php",
        'Itb\Bonus\Event\OnSaleOrderSaved' => "lib/Event/OnSaleOrderSaved.php",
        'Itb\Bonus\Event\OrderResultPrepared' => "lib/Event/OrderResultPrepared.php",
        'Itb\Bonus\Install\OrderResultPrepared' => "lib/Install/BonusInstallFirst.php",
        'Itb\Bonus\Install\OrderProps' => "lib/Install/OrderProps.php",
        'Itb\Bonus\Install\UserProps' => "lib/Install/UserProps.php",
    )
);