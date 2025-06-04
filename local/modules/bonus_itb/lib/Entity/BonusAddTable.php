<?php
namespace Itb\Bonus\Entity;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;

Class BonusAddTable extends DataManager
{

    public static function getTableName()
    {
        return 'itb_bonus_order';
    }

    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new Fields\IntegerField('USER'),
            new Fields\IntegerField('ORDER_ID'),
            new Fields\StringField('TYPE'),
            new Fields\StringField('OPERATION_TYPE'),
            new Fields\FloatField('MINUS_PRICE'),
            new Fields\FloatField('AFTER_PRICE_BONUS'),
            new Fields\FloatField('BEFORE_PRICE_BONUS'),
            new Fields\FloatField('BONUS_PRICE'),
        ];
    }
}