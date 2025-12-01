<?php
namespace Itb\Bonus\Helper;

class ItbHelpers
{

    public static function GetListSties()
    {
        $arrSites = [];
        $by = "id";
        $order = "asc";
        $sites = \CSite::GetList($by, $order, array());
        while($arSite = $sites->Fetch())
        {
            $arrSites[$arSite["ID"]] = $arSite["NAME"];
        }
        return $arrSites;
    }

    public static function getCatalogs(){
        $arCatalogs = array();
        $dbCatalogs = \CIBlock::GetList(array(), array('ACTIVE'=>'Y', ), false);
        //take only torgoviy catalog
        while($arCatalog = $dbCatalogs->Fetch())
        {
            $catDb = \CCatalog::GetByID($arCatalog["ID"]);
            if($catDb)
                $arCatalogs[$catDb["ID"]] = $catDb["NAME"];
        }
        return $arCatalogs;
    }

    public static function FormatBonusString($string, $search, $var)
    {
        if(strpos($string, $search) !== false) {
            $newString = str_replace($search, (string)$var, $string);
        }
        else
            $newString = $string. ' '.$var;

        return $newString;
    }

    public static function GetUserGroups()
    {
        $userGrups = array();
        $by = "id";
        $order = "asc";
        $rsGroups = \CGroup::GetList($by, $order, array("ACTIVE"  => "Y"));
        while($arUserGroups = $rsGroups->Fetch()) {
            $userGrups[$arUserGroups["ID"]] = $arUserGroups["NAME"];
        }
        return $userGrups;
    }

    public static function GetOrderStatuses()
    {
        $arStatuses = array();
        $arrStatuses = \Bitrix\Sale\Internals\StatusLangTable::getList(array('order' => array('STATUS.SORT'=>'ASC'), 'filter' => array('STATUS.TYPE'=>'O', 'LID'=>LANGUAGE_ID)));
        while($arStatus = $arrStatuses->fetch())
        {
            $arStatuses[$arStatus["STATUS_ID"]] = $arStatus["NAME"];
        }

        return $arStatuses;
    }

    public static function getBasketRules(){
        $basketRules = array();
        $discountIterator = \Bitrix\Sale\Internals\DiscountTable::getList(array(
            'select' => array("ID", "NAME"),
            'filter' => array('ACTIVE' => 'Y'),
            'order' => array("NAME" => "ASC")
        ));
        while ($discount = $discountIterator->fetch()){
            $basketRules[$discount['ID']] = $discount['NAME'];
        }
        return $basketRules ;
    }

    public static function getPaySystems(){
        $paySystems = array();
        $res = \Bitrix\Sale\Internals\PaySystemActionTable::GetList(array('order' => array("NAME" => "ASC")));
        while($row=$res->fetch()){
            $paySystems[]=$row;
        }
        return $paySystems;
    }

    public static function getDelivery(){
        $delivery = array();
        $res = \Bitrix\Sale\Delivery\Services\Table::getList(array('order' => array("NAME" => "ASC")));
        while($del = $res->Fetch()) {
            $delivery[] = $del;
        }
        return $delivery;
    }

    public static function getPersonTypes(){
        $personTypes = array();
        $res = \CSalePersonType::GetList(array('NAME'=>'ASC'),array(),false,false,array());
        while($type = $res->Fetch()){
            $personTypes[$type['ID']]=$type['NAME'];
        }
        return $personTypes;
    }

    public static function Round($number = 0, $round = 2, $round_method = 'MATH')
    {
        if($round_method == 'MATH')
        {
            $result = round($number, $round);
        }
        if($round_method == 'UP')
        {
            if($round == 0)
                $result = ceil($number);
            if($round == 1)
                $result = ceil($number*10)/10;
            if($round == 2)
                $result = ceil($number*100)/100;
            if($round == 3)
                $result = ceil($number*1000)/1000;
            if($round == 4)
                $result = ceil($number*10000)/10000;
        }
        if($round_method == 'DOWN')
        {
            if($round == 0)
                $result = floor($number);
            if($round == 1)
                $result = floor($number*10)/10;
            if($round == 2)
                $result = floor($number*100)/100;
            if($round == 3)
                $result = floor($number*1000)/1000;
            if($round == 4)
                $result = floor($number*10000)/10000;
        }
        return $result;
    }

    public static function UserBallance($user_id)
    {
        global $USER;
        if(!$user_id || $user_id == '')
            $user_id = $USER->GetID();

        $arParams["SELECT"] = array("UF_BONUS_COUNT");
        $by="ID";
        $order="desc";
        $DBUserBonus = \CUser::GetList($by,$order,array("ID" => $user_id),$arParams);
        if($arUserBonus = $DBUserBonus->Fetch())
        {
            $userBonus = $arUserBonus["UF_BONUS_COUNT"];
        }

        return $userBonus;
    }


    public static function PaySystemBonusId()
    {
        \CModule::IncludeModule("sale");
        $paySystemBonus = \CSalePaySystem::GetList(array(), array('CODE' => 'ITB_PAYMENT_BONUS'));
        while($ptype = $paySystemBonus->Fetch())
        {
            $paySystemId = $ptype["ID"];
        }

        return $paySystemId;
    }
}