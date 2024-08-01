<?php
//$arClasses=array(
//    'BonusEventTable'=>'lib/entity/bonusentity.php'
//);
//
//CModule::AddAutoloadClasses("bonus_itb",$arClasses);

\Bitrix\Main\Loader::registerAutoLoadClasses('bonus_itb', array(
        'Itb\Entity\BonusEventTable' => "/lib/entity/bonusentity.php",
    )
);