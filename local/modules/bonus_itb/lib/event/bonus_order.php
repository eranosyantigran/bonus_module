<?php

namespace Itb\Bonus\Event;

use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Itb\Entity\BonusEventTable;
use Itb\Entity\BonusAddTable;
use \Bitrix\Main\UserTable;

Loader::includeModule('sale');
Loader::includeModule('bonus_itb');
Loader::includeModule('main');

class BonusOrder
{

    public static function onOrderSave($order)
    {

        $fields = $order->GetFields();
        $values = $fields->GetValues();

        $total_sum = $values['SUM_PAID'];
        $order_id = $values['ID'];
        $user_id = $values['USER_ID'];


        if($values['PAYED'] == "Y"){

            $rs =  BonusEventTable::getList([
                "filter" => ['ACTIVE' => "Y", "TYPE" => 'order'],
            ])->Fetch();

            $arrCondition = unserialize($rs['CONDITIONS']);

            $condition = self::GetConditionArray('order', $arrCondition, $values);

            if (!empty($condition) && count($condition) >= 1){

                $arrbonususer = ['USER' => $user_id , "ORDER_ID" => $order_id, "TYPE" => $rs['TYPE'], "BONUS_ID" => $rs['ID'], "BONUS_PRICE" => $condition['BONUS_PRICE'] ];

                self::AddBonusFromUser($user_id, $arrbonususer , $condition);

            }

        }else{

            self::MinusBonusUser($user_id, $order_id);
        }

    }

    public static function GetConditionArray($mode, $arrCondition, $arrorder)
    {
        $bonus = 0;
        $bonus_price = 0;
        $bonus_type = '';

        if ($mode == 'order'){

            foreach ($arrCondition['children'] as $item){
                $bonus = (int)$item['values']['bonus'];
                $bonus_type = $item['values']['bonus_type'];
            }

            if ($bonus_type == 'percent'){
                $bonus_price = (int)$arrorder['SUM_PAID'] * $bonus / 100;
                $bonus_price =  \Itb\Bonus\ItbHelpers::Round($bonus_price);
            }else{
                $bonus_price = \Itb\Bonus\ItbHelpers::Round($bonus);
            }

            return  ['BONUS_PRICE' => $bonus_price];
        }else{
            return [];
        }
    }

    public static function AddBonusFromUser($user_id, $bonus, $bonus_price)
    {
        $resl =  BonusAddTable::getList([
            "filter" => ["ORDER_ID" => $bonus['ORDER_ID']],
        ])->Fetch();

        if (empty($resl)){
            BonusAddTable::add($bonus);

            $user = new \CUser;

            $userData = UserTable::getList([
                'select' => ['UF_*'], // 'UF_*' выбирает все пользовательские поля
                'filter' => ['ID' => $user_id],
            ])->fetch();

            $bonus = $bonus_price['BONUS_PRICE'] + $userData['UF_BONUS_COUNT'];


            $fields = [
                "UF_BONUS_COUNT" => $bonus,
            ];

            $result = $user->Update($user_id, $fields);

            if ($result)
                return true;

        } elseif(!empty($resl) && $resl['BONUS_PRICE'] == 0 ){

            $user = new \CUser;

            $userData = UserTable::getList([
                'select' => ['UF_*'], // 'UF_*' выбирает все пользовательские поля
                'filter' => ['ID' => $user_id],
            ])->fetch();

            $bonus = $bonus_price['BONUS_PRICE'] + $userData['UF_BONUS_COUNT'];

            $fields = [
                "UF_BONUS_COUNT" => $bonus,
            ];

            $result = $user->Update($user_id, $fields);

            BonusAddTable::update($resl['ID'], ['BONUS_PRICE' => $bonus_price['BONUS_PRICE']] );

            if ($result)
                return true;

        }else{

            $user = new \CUser;

            $userData = UserTable::getList([
                'select' => ['UF_*'], // 'UF_*' выбирает все пользовательские поля
                'filter' => ['ID' => $user_id],
            ])->fetch();

            $bonus = $bonus_price['BONUS_PRICE'] + $userData['UF_BONUS_COUNT'];

            $fields = [
                "UF_BONUS_COUNT" => $bonus,
            ];

            $result = $user->Update($user_id, $fields);

            BonusAddTable::update($resl['ID'], ['BONUS_PRICE' => $bonus_price['BONUS_PRICE']] );

            if ($result)
                return true;
        }
        return false;
    }

    public static function MinusBonusUser($userId, $order_id){

        $resl =  BonusAddTable::getList([
            "filter" => ["ORDER_ID" => $order_id],
        ])->Fetch();

        if (!empty($resl)){

            $user = new \CUser;

            $userData = UserTable::getList([
                'select' => ['UF_*'], // 'UF_*' выбирает все пользовательские поля
                'filter' => ['ID' => $userId],
            ])->fetch();



            if ($userData['UF_BONUS_COUNT'] > $resl['BONUS_PRICE'])
                $bonus = $userData['UF_BONUS_COUNT'] - $resl['BONUS_PRICE'] ;
            else
                $bonus = $resl['BONUS_PRICE'] - $userData['UF_BONUS_COUNT']  ;

            $fields = [
                "UF_BONUS_COUNT" => $bonus,
            ];

            $result = $user->Update($userId, $fields);

            BonusAddTable::update($resl['ID'], ["BONUS_PRICE" => 0]);

        }
    }

}