<?php
//$arClasses=array(
//    'BonusEventTable'=>'lib/entity/bonusentity.php'
//);
//
//CModule::AddAutoloadClasses("bonus_itb",$arClasses);

\Bitrix\Main\Loader::registerAutoLoadClasses('bonus_itb', array(
        'Itb\Entity\BonusEventTable' => "/lib/entity/bonusentity.php",
        'Itb\Bonus\Conditions\OrderBonus' => "/lib/conditions/order_bonus.php",
        'Itb\Bonus\Condition\ConditionBonus' => "/lib/conditions/order_conditions_save.php",
        'Itb\Bonus\ItbHelpers' => "/lib/itb_helpers.php",
    )
);