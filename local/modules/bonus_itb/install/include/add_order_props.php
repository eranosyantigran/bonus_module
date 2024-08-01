<?php
IncludeModuleLangFile(__FILE__);
if (CModule::IncludeModule("sale")){

    //Добавление свойств и гуп

    $dbPersonTypes = CSalePersonType::GetList(Array("SORT" => "ASC"), Array());
    while($ptype = $dbPersonTypes->Fetch()) {

        $addGroup = 'Y';
        $db_propsGroup = \CSaleOrderPropsGroup::GetList(["SORT" => "ASC"], ["PERSON_TYPE_ID" => $ptype["ID"]], false, false, []);
        while($propsGroup = $db_propsGroup->Fetch())
        {
            if($propsGroup['NAME'] == GetMessage("BONUS_ORDER_GROUP_NAME"))
            {
                $PropGroupID = $propsGroup['ID'];
                $addGroup = 'N';
            }
        }
        if($addGroup == 'Y')
            $PropGroupID = CSaleOrderPropsGroup::Add(array("PERSON_TYPE_ID" => $ptype["ID"], "NAME" => GetMessage("BONUS_ORDER_GROUP_NAME"), "SORT" => 100));

        $addBonusProp = 'Y';
        $addPayBonusProp = 'Y';
        $db_props = CSaleOrderProps::GetList(["SORT" => "ASC"], array("PERSON_TYPE_ID" => $ptype["ID"], "CODE" => ['ITB_ADD_BONUS', 'ITB_PAYMENT_BONUS']), false, false, []);
        while ($prop = $db_props->Fetch()) {
            if ($prop['CODE'] == 'ITB_PAYMENT_BONUS')
                $addBonusProp = 'N';
            if ($prop['CODE'] == 'ITB_ADD_BONUS')
                $addPayBonusProp = 'N';
        }

        if ($addBonusProp == 'Y'){
            $arFields = array(
                "PERSON_TYPE_ID" => $ptype["ID"],
                "NAME" => GetMessage("ITB_ORDER_PROP_PAYMENT_BONUS_NAME"),
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

            CSaleOrderProps::Add($arFields);
        }

        if($addPayBonusProp == 'Y') {
            $arFields = array(
                "PERSON_TYPE_ID" => $ptype["ID"],
                "NAME" => GetMessage("ITB_ORDER_PROP_ADD_BONUS_NAME"),
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
            CSaleOrderProps::Add($arFields);
        }
    }
}