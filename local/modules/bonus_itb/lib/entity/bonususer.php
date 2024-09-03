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
            new Entity\IntegerField('BONUS_ID', array(
                'column_name' => 'bonus_id',
            )),
            new Entity\FloatField('BONUS_PRICE', array(
                'column_name' => 'bonus_price',
            )),
        );
    }
}