<?php
global $DBType;

$arClasses=array(
    'SaleOrderPaid'=>'classes/SaleOrderPaid.php'
);

CModule::AddAutoloadClasses("bonus_itb",$arClasses);

\Bitrix\Main\Loader::registerAutoLoadClasses('bonus_itb', array(
        'Itb\Entity\BonusEventTable' => "/lib/entity/bonusentity.php",
        'Itb\Entity\BonusAddTable' => "/lib/entity/bonususer.php",
        'Itb\Bonus\Conditions\OrderBonus' => "/lib/conditions/order_bonus.php",
        'Itb\Bonus\Condition\ConditionBonus' => "/lib/conditions/order_conditions_save.php",
        'Itb\Bonus\ItbHelpers' => "/lib/itb_helpers.php",
        'Itb\Bonus\Event\BonusOrder' => "/lib/event/bonus_order.php",
    )
);