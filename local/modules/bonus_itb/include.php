<?php


$autoloadPath = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

\Bitrix\Main\Loader::registerAutoLoadClasses('bonus_itb', array(
        'Itb\Bonus\Event\OrderResultPrepared' => "lib/Event/OrderResultPrepared.php",
        'Itb\Bonus\Ajax\SaleOrderAjax' => "lib/Ajax/SaleOrderAjax.php",
        'Itb\Bonus\Helper\ItbHelpers' => "lib/Helper/ItbHelpers.php",
        'Itb\Bonus\Class\CalculateBonus' => "lib/Class/CalculateBonus.php",
        'Itb\Bonus\Entity\BonusEventTable' => "lib/Entity/BonusEventTable.php",
    )
);