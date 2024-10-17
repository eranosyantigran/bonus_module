<?php
global $DBType;

$arClasses=array(
    'SaleOrderPaid'          =>  'classes/SaleOrderPaid.php',
    'CModuleOptionsITBBonus' =>  'classes/module-options/CModuleOptions.php',
);

CModule::AddAutoloadClasses("bonus_itb", $arClasses);

\Bitrix\Main\Loader::registerAutoLoadClasses('bonus_itb', array(
        'Itb\Entity\BonusEventTable' => "/lib/entity/bonusentity.php",
        'Itb\Entity\BonusAddTable' => "/lib/entity/bonususer.php",
        'Itb\Bonus\Conditions\OrderBonus' => "/lib/conditions/order_bonus.php",
        'Itb\Bonus\Condition\ConditionBonus' => "/lib/conditions/order_conditions_save.php",
        'Itb\Bonus\ItbHelpers' => "/lib/itb_helpers.php",
        'Itb\Bonus\Event\BonusOrder' => "/lib/event/bonus_order.php",
        'Itb\Bonus\Event\OrderResultPrepared' => "/lib/event/salecomponentorderresultprepared.php",
        'Itb\Bonus\Event\OnSaleOrderSaved' => "/lib/event/saleOrderSaved.php",
        'Itb\Bonus\CalculateBonus' => "/lib/calculate.php",
        'ITB\Bonus\Ajax\SaleOrderAjax' => "/lib/ajax/saleOrderAjax.php",
    )
);