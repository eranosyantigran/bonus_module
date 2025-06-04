<?php
namespace Itb\Bonus\Entity;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;

class BonusEventTable extends DataManager
{
    public static function getTableName()
    {
        return 'itb_balls_list';
    }

    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new Fields\IntegerField('SORT', [
                'default_value' => 100,
            ]),
            new Fields\BooleanField('ACTIVE', [
                'values' => ['N', 'Y'],
                'default_value' => 'Y',
            ]),
            new Fields\StringField('NAME'),
            new Fields\StringField('ADD_BONUS'),
            new Fields\StringField('TYPE'),
            new Fields\TextField('CONDITIONS'),
            new Fields\TextField('CONDITIONS_PRICE'),
            new Fields\IntegerField('USER'),
        ];
    }
}