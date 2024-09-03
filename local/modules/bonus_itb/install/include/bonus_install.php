<?php

include(dirname(__FILE__)."/../../lib/entity/bonusentity.php");

$arSaveFields = array();
$arSaveFields['NAME'] = GetMessage("ITB_PROFILE_BONUS_FROM_ORDER");
$arSaveFields['ACTIVE'] = 'Y';
$arSaveFields['SORT'] = 100;
$arSaveFields['TYPE'] = 'order';
$arSaveFields['USER'] = 1;
$arSaveFields['ADD_BONUS'] = 0;

$saveProductConditions = array(
    'id' => '0',
    'controlId' => 'CondGroup',
    'children' => array(
        array(
            'id' => '0',
            'controlId' => 'conditionGroup',
            'values' => array(
                'bonus' => '5',
                'bonus_type' => 'percent',
                'round' => 'C',
                "All" => 'OR',
                "True" => 'True'
            ),
            "children" => array()
        )
    )
);

$arSaveFields['CONDITIONS'] = serialize($saveProductConditions);


\Itb\Entity\BonusEventTable::add($arSaveFields);
