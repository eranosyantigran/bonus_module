<?php
namespace Itb\Entity;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Class BonusAddTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return "itb_bonus_order";
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'column_name' => 'id',
            )),
            new Entity\IntegerField('USER', array(
                'column_name' => 'user',
            )),
            new Entity\IntegerField('ORDER_ID', array(
                'column_name' => 'order_id',
            )),
            new Entity\StringField('TYPE', array(
                'column_name' => 'type',
            )),
            new Entity\StringField('OPERATION_TYPE', array(
                'column_name' => 'operation_type',
            )),
            new Entity\FloatField('MINUS_PRICE', array(
                'column_name' => 'minus_price',
            )),
            new Entity\FloatField('AFTER_PRICE_BONUS', array(
                'column_name' => 'after_price_bonus',
            )),
            new Entity\FloatField('BEFORE_PRICE_BONUS', array(
                'column_name' => 'before_price_bonus',
            )),
            new Entity\FloatField('BONUS_PRICE', array(
                'column_name' => 'bonus_price',
            )),
        );
    }
}