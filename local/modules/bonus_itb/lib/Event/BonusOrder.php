<?php

namespace Itb\Bonus\Event;

use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Itb\Bonus\Entity\BonusEventTable;
use Itb\Bonus\Entity\BonusAddTable;
use Itb\Bonus\Helper\ItbHelpers;
use \Bitrix\Main\UserTable;

Loader::includeModule('sale');
Loader::includeModule('bonus_itb');
Loader::includeModule('main');

class BonusOrder
{

    public static function onOrderSave($order)
    {
        $is_new = $order->isNew();
        $fields = $order->GetFields();
        $values = $fields->GetValues();

        $order_id = $values['ID'];

        if (!$is_new):

            if($values['PAYED'] == "Y"){

                $rs =  BonusAddTable::getList([
                    "filter" => ["TYPE" => 'order' , 'ORDER_ID' => $order_id , 'OPERATION_TYPE' => 'PAYED_N'],
                ])->Fetch();

                if ($rs['USER'] > 0):
                    $UserBallance = ItbHelpers::UserBallance($rs['USER']);
                    $user_id = $rs['USER'];
                else:
                    $UserBallance = ItbHelpers::UserBallance(1);
                    $user_id = 1;
                endif;

                $newBalance = $UserBallance + $rs['BONUS_PRICE'];

                $user = new \CUser;

                $fields = [
                    "UF_BONUS_COUNT" => $newBalance,
                ];

                $result = $user->Update($user_id, $fields);
            }

        endif;

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
                $bonus_price =  ItbHelpers::Round($bonus_price);
            }else{
                $bonus_price = ItbHelpers::Round($bonus);
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

    public static function MinusBonusUser($arFields){

            $user = new \CUser;

            $fields = [
                "UF_BONUS_COUNT" => $arFields['AFTER_PRICE_BONUS'],
            ];

            $result = $user->Update($arFields['USER_ID'], $fields);

            if ($result){
                return true;
            }

    }

}