<?php

namespace Itb\Bonus\Install;

\Bitrix\Main\Loader::includeModule('sale');
use Bitrix\Sale\Internals\PaySystemActionTable;

class OrderProps
{

    public static function AddOrderProps () {

        if (\CModule::IncludeModule("sale")){

            //Добавление свойств и гуп

            $dbPersonTypes = \CSalePersonType::GetList(Array("SORT" => "ASC"), Array());
            while($ptype = $dbPersonTypes->Fetch()) {

                $addGroup = 'Y';
                $db_propsGroup = \CSaleOrderPropsGroup::GetList(["SORT" => "ASC"], ["PERSON_TYPE_ID" => $ptype["ID"]], false, false, []);
                while($propsGroup = $db_propsGroup->Fetch())
                {
                    if($propsGroup['NAME'] == "Бонусная система Itb")
                    {
                        $PropGroupID = $propsGroup['ID'];
                        $addGroup = 'N';
                    }
                }
                if($addGroup == 'Y')
                    $PropGroupID = \CSaleOrderPropsGroup::Add(array("PERSON_TYPE_ID" => $ptype["ID"], "NAME" => "Бонусная система Itb", "SORT" => 100));

                $addBonusProp = 'Y';
                $addPayBonusProp = 'Y';
                $db_props = \CSaleOrderProps::GetList(["SORT" => "ASC"], array("PERSON_TYPE_ID" => $ptype["ID"], "CODE" => ['ITB_ADD_BONUS', 'ITB_PAYMENT_BONUS']), false, false, []);
                while ($prop = $db_props->Fetch()) {
                    if ($prop['CODE'] == 'ITB_PAYMENT_BONUS')
                        $addBonusProp = 'N';
                    if ($prop['CODE'] == 'ITB_ADD_BONUS')
                        $addPayBonusProp = 'N';
                }

                if ($addBonusProp == 'Y'){
                    $arFields = array(
                        "PERSON_TYPE_ID" => $ptype["ID"],
                        "NAME" =>  "Оплатить баллами",
                        "TYPE" => "TEXT",
                        "REQUIED" => "N",
                        "DEFAULT_VALUE" => "-",
                        "SORT" => 100,
                        "CODE" => "ITB_PAYMENT_BONUS",
                        "USER_PROPS" => "N",
                        "IS_LOCATION" => "N",
                        "IS_LOCATION4TAX" => "N",
                        "PROPS_GROUP_ID" => $PropGroupID,
                        "SIZE1" => 0,
                        "SIZE2" => 0,
                        "DESCRIPTION" => "",
                        "IS_EMAIL" => "N",
                        "IS_PROFILE_NAME" => "N",
                        "IS_PAYER" => "N",
                    );

                    \CSaleOrderProps::Add($arFields);
                }

                if($addPayBonusProp == 'Y') {
                    $arFields = array(
                        "PERSON_TYPE_ID" => $ptype["ID"],
                        "NAME" => "Начислено бонусов по заказу",
                        "TYPE" => "TEXT",
                        "REQUIED" => "N",
                        "DEFAULT_VALUE" => "0",
                        "SORT" => 100,
                        "CODE" => "ITB_ADD_BONUS",
                        "USER_PROPS" => "N",
                        "IS_LOCATION" => "N",
                        "IS_LOCATION4TAX" => "N",
                        "PROPS_GROUP_ID" => $PropGroupID,
                        "SIZE1" => 0,
                        "SIZE2" => 0,
                        "DESCRIPTION" => "",
                        "IS_EMAIL" => "N",
                        "IS_PROFILE_NAME" => "N",
                        "IS_PAYER" => "N",
                    );
                   \CSaleOrderProps::Add($arFields);
                }
            }
        }
    }

    public static function DeleteOrderProps(){

        if (\CModule::IncludeModule("sale"))
        {

            $db_props = \CSaleOrderProps::GetList(
                array("ID" => "ASC"),
                array("CODE" => 'ITB_PAYMENT_BONUS'),
                false,
                false,
                array()
            );
            while ($props = $db_props->Fetch())
            {
                \CSaleOrderProps::Delete($props["ID"]);
            }

            $db_props = \CSaleOrderProps::GetList(
                array("ID" => "ASC"),
                array("CODE" => 'ITB_ADD_BONUS'),
                false,
                false,
                array()
            );
            $PropsGroupID = array();
            while ($props = $db_props->Fetch())
            {
                $PropsGroupID[] = $props["PROPS_GROUP_ID"];
                \CSaleOrderProps::Delete($props["ID"]);
            }
            //Delette groups of Bonus
            foreach($PropsGroupID as $PropGroup) {
                \CSaleOrderPropsGroup::Delete($PropGroup);
            }
        }
    }

    public static function InstallPaySystem(){

        $fields = array(
            "NAME" => "Бонусный счет",
            "PSA_NAME" => "Бонусный счет",
            "ACTIVE" => 'Y',
            "CODE" => 'ITB_PAYMENT_BONUS',
            "NEW_WINDOW" => 'N',
            "ALLOW_EDIT_PAYMENT" => 'Y',
            "IS_CASH" => 'Y',
            "SORT" => 10000000,
            "ENCODING" => '',
            "DESCRIPTION" => '',
            "ACTION_FILE" => 'cash',
        );
        if(array_key_exists('ENTITY_REGISTRY_TYPE', \Bitrix\Sale\Internals\PaySystemActionTable::getMap()))
            $fields['ENTITY_REGISTRY_TYPE'] = \Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER;

        $result = PaySystemActionTable::add($fields);

        if (!$result->isSuccess())
        {
            $errorMessage .= join(',', $result->getErrorMessages());
        }
        else
        {
            $id = $result->getId();
        }

    }

    public static function DeletePaySystem() {

        \CModule::IncludeModule("sale");
        $paySystemBonus = \CSalePaySystem::GetList(array(), array('CODE' => 'ITB_PAYMENT_BONUS'));
        while($ptype = $paySystemBonus->Fetch())
        {
            \CSalePaySystem::Delete($ptype["ID"]);
        }

    }

}