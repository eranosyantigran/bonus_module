<?php
namespace Itb\Entity;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Class BonusEventTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return "itb_balls_list";
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'column_name' => 'id',
            )),
            new Entity\IntegerField('SORT', array(
                'column_name' => 'sort',
            )),
            new Entity\StringField('ACTIVE', array(
                'column_name' => 'active',
            )),
            new Entity\StringField('NAME', array(
                'column_name' => 'name',
            )),
            new Entity\StringField('ADD_BONUS', array(
                'column_name' => 'add_bonus',
            )),
            new Entity\StringField('TYPE', array(
                'column_name' => 'type',
            )),
            new Entity\TextField('CONDITIONS', array(
                'column_name' => 'conditions',
            )),
            new Entity\IntegerField('USER', array(
                'column_name' => 'user',
            )),
        );
    }
}