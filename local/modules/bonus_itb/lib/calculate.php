<?php
namespace Itb\Bonus;

use Itb\Entity\BonusEventTable;

$module_id='bonus_itb';
\Bitrix\Main\Loader::includeModule($module_id);
\CModule::IncludeModule("catalog");

class CalculateBonus
{

    public static function getBonus($arProducts = array(), $arParams = array("TYPE"=>'catalog')){

        if(empty($arProducts) && $arParams["TYPE"] == 'catalog')
            return;

        global $USER;

        $arAllInfo = array();

        //---Set user params---//
        if($arParams["ORDER"]["ORDER_ID"] > 0)
        {
            $arParams["USER_ID"] = $arParams["ORDER"]["USER_ID"];
            $arParams["SITE_ID"] = $arParams["ORDER"]["SITE_ID"];
        }
        if(!isset($arParams["USER_ID"]))
            $arParams["USER_ID"] = $USER->GetID();
        if(!isset($arParams["SITE_ID"]))
            $arParams["SITE_ID"] = SITE_ID;

        $arParams["USER_GROUPS"] = \CUser::GetUserGroup($arParams["USER_ID"]);
        $arAllInfo["PARAMS"] = $arParams;

        if($arParams["TYPE"] == 'cart'):
            if(!isset($arParams["ORDER"]["ORDER_ID"]) || $arParams["ORDER"]["ORDER_ID"] === 0)
            {
                $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
                $basketItems = $basket->getOrderableItems();

                //if($basket->count() == 0)
                if(!$basketItems->count())
                    return;

                if(!empty($arProducts))
                {
                    foreach($arProducts as $key => $arProduct):
                        if($arProduct["ID"] > 0 && $arProduct["ID"] != $key)
                        {
                            $arProducts[$arProduct["ID"]] = $arProduct;
                            unset($arProducts[$key]);
                        }
                    endforeach;
                }

                /*--- Calculate basket with discounts ---*/
                $discounts = \Bitrix\Sale\Discount::buildFromBasket($basket, new \Bitrix\Sale\Discount\Context\Fuser($basket->getFUserId(true)));
                $discounts->calculate();
                $resultDiscounts = $discounts->getApplyResult(true);

                $aplyDiscounts = array();
                $aplyCoupons = array();
                if(!empty($resultDiscounts["FULL_DISCOUNT_LIST"])):
                    foreach($resultDiscounts["FULL_DISCOUNT_LIST"] as $arDiscount):
                        $aplyDiscounts[$arDiscount["ID"]] = array("ID"=>$arDiscount["ID"], "NAME"=>$arDiscount["NAME"], "USE_COUPONS"=>$arDiscount["USE_COUPONS"]);
                        if($arDiscount["USE_COUPONS"] == 'Y' && is_array($arDiscount["COUPON"]))
                            $aplyCoupons[$arDiscount["COUPON"]["ID"]] = $arDiscount["COUPON"];
                    endforeach;
                endif;
                $arParams["ORDER_DICOUNTS"]["DISCOUNTS"] = $aplyDiscounts;
                $arParams["ORDER_DICOUNTS"]["COUPONS"] = $aplyCoupons;

                $cartSum = 0;
                foreach($basketItems as $basketItem):
                    $arItem = array();
                    $arItem["PRODUCT_ID"] = $basketItem->getProductId();
                    $arItem["BASKET_ID"] = $basketItem->getId();
                    $arItem["NAME"] = $basketItem->getField('NAME');
                    $arItem["QUANTITY"] = $basketItem->getQuantity();

                    $arItem["BASE_PRICE"] = $basketItem->getField('BASE_PRICE');
                    $arItem["PRICE"] = $basketItem->getPrice();
                    $arItem["DISCOUNT_PRICE"] = $basketItem->getField('DISCOUNT_PRICE');//razmer skidki
                    $arItem["POSITION_FINAL_PRICE"] = $basketItem->getFinalPrice();

                    if(isset($resultDiscounts["PRICES"]["BASKET"][$arItem["BASKET_ID"]]))
                    {
                        $arItem["BASE_PRICE"] = $resultDiscounts["PRICES"]["BASKET"][$arItem["BASKET_ID"]]["BASE_PRICE"];
                        $arItem["PRICE"] = $resultDiscounts["PRICES"]["BASKET"][$arItem["BASKET_ID"]]["PRICE"];
                        $arItem["DISCOUNT_PRICE"] = $resultDiscounts["PRICES"]["BASKET"][$arItem["BASKET_ID"]]["DISCOUNT"];
                        $arItem["POSITION_FINAL_PRICE"] = $arItem["QUANTITY"] * $arItem["PRICE"];
                    }
                    $cartSum = $cartSum + $arItem["POSITION_FINAL_PRICE"];

                    $collection = $basketItem->getPropertyCollection();
                    $arPropItem = $collection->getPropertyValues();
                    $arItem["BASKET_PROPS"] = $arPropItem;

                    if(isset($arProducts[$arItem["BASKET_ID"]]))
                    {
                        $arProducts[$arItem["BASKET_ID"]]["BASKET_ID"] = $arItem["BASKET_ID"];
                        $arProducts[$arItem["BASKET_ID"]]["BASKET_PROPS"] = $arPropItem;
                    }
                    else
                        $arProducts[$arItem["BASKET_ID"]] = $arItem;

                endforeach;
                /*--- Calculate basket with discounts ---*/
            }
            else
            {
                $order = \Bitrix\Sale\Order::load($arParams["ORDER"]["ORDER_ID"]);
                $basket = $order->getBasket();
                $cartSum = $basket->getPrice();

                $discounts = $order->getDiscount();
                $resultDiscounts = $discounts->getApplyResult();

                $aplyDiscounts = array();
                $aplyCoupons = array();
                if(!empty($resultDiscounts["DISCOUNT_LIST"])):
                    foreach($resultDiscounts["DISCOUNT_LIST"] as $arDiscount):
                        $aplyDiscounts[$arDiscount["REAL_DISCOUNT_ID"]] = array("ID"=>$arDiscount["REAL_DISCOUNT_ID"], "NAME"=>$arDiscount["NAME"], "USE_COUPONS"=>$arDiscount["USE_COUPONS"]);
                        if($arDiscount["USE_COUPONS"] == 'Y' && is_array($arDiscount["COUPON"]))
                            $aplyCoupons[$arDiscount["COUPON"]["ID"]] = $arDiscount["COUPON"];
                    endforeach;
                endif;
                $arParams["ORDER_DICOUNTS"]["DISCOUNTS"] = $aplyDiscounts;
                $arParams["ORDER_DICOUNTS"]["COUPONS"] = $aplyCoupons;

                foreach($basket as $basketItem):
                    $arItem = array();
                    $arItem["PRODUCT_ID"] = $basketItem->getProductId();
                    $arItem["BASKET_ID"] = $basketItem->getId();

                    $collection = $basketItem->getPropertyCollection();
                    $arPropItem = $collection->getPropertyValues();
                    $arItem["BASKET_PROPS"] = $arPropItem;

                    $arProducts[$arItem["BASKET_ID"]]["BASKET_ID"] = $arItem["BASKET_ID"];
                    $arProducts[$arItem["BASKET_ID"]]["BASKET_PROPS"] = $arPropItem;
                endforeach;

                $arParams["ORDER"]["CART_SUM"] = $cartSum;
            }

        endif;

        $arProfiles = BonusEventTable::getList();

        if(empty($arProfiles))
            return;

        $arBonusProps = array();

        while ($arProfil = $arProfiles->fetch()){
                $prof =  unserialize($arProfil->fetch()['CONDITIONS'])['children'];
                foreach($prof as $condProf):
                    if($condProf["controlId"] == 'conditionGroup2' && $condProf["values"]["bonus_from_props"] != '')
                        $arBonusProps[] = $condProf["values"]["bonus_from_props"];
                    if($condProf["controlId"] == 'conditionGroup')
                        $fixOrderBonus = $condProf["values"]["bonus"];
                endforeach;
        }

        if(empty($arProducts))
            return;
        $arProductsId = array();
        $arProductList = array();
        foreach($arProducts as $item):

            $arItem = array();

            switch($arParams["TYPE"])
            {
                case 'cart':

                    //Poluchaem id tovara po predlogeniyu
                    $mxResult = \CCatalogSku::GetProductInfo($item["PRODUCT_ID"]);
                    if(is_array($mxResult))
                    {
                        $mainProductId = $mxResult['ID'];
                        $offerId = $item["PRODUCT_ID"];
                        if(!isset($arProductList[$item["BASKET_ID"]]))
                        {
                            $arItem["ID"] = $mainProductId;
                            $arItem["BASKET_ITEM_ID"] = $item["BASKET_ID"];
                            $arItem["QUANTITY"] = $item["QUANTITY"];
                            $arItem["PRICE"]["FULL_PRICE"] = $item["BASE_PRICE"];
                            $arItem["PRICE"]["DISCOUNT_PRICE"] = $item["PRICE"];
                            $arItem["PRICE"]["DISCOUNT"] = $item["DISCOUNT_PRICE"];
                            $arItem["PRICE"]["MIN_QUANTITY"] = $item["MEASURE_RATIO"];
                            $arItem['BASKET_PROPS'] = $item['BASKET_PROPS'];
                            $arProductList[$item["BASKET_ID"]] = $arItem;
                        }
                        $arProductList[$item["BASKET_ID"]]["OFFERS"][] = $offerId;

                        if(!isset($arAllInfo["ELEMENTS"][$mainProductId]))
                            $arAllInfo["ELEMENTS"][$mainProductId]["ID"] = $mainProductId;
                        $arAllInfo["ELEMENTS"][$mainProductId]["OFFERS"][] = $offerId;


                        $arOffer = array("ID" => $offerId, "OFFER" => 'Y', "MAIN_PRODUCT_ID" => $mainProductId, "QUANTITY" => $item["QUANTITY"], "BASKET_ITEM_ID" => $item["BASKET_ID"]);
                        $arOffer["PRICE"] = array("FULL_PRICE"=>$item["BASE_PRICE"], "DISCOUNT_PRICE"=>$item["PRICE"], "DISCOUNT"=>$item["DISCOUNT_PRICE"], "MIN_QUANTITY"=>$item["MEASURE_RATIO"]);
                        $arAllInfo["ELEMENTS"][$offerId] = $arOffer;

                        //Dobavlyaem v predlojenie svoystva korzini
                        $arAllInfo["ELEMENTS"][$offerId]["BASKET_PROPS"] = $item["BASKET_PROPS"];

                        $arProductsId[] = $mainProductId;
                        $arProductsId[] = $offerId;
                    }
                    else
                    {
                        $arItem["ID"] = $item["PRODUCT_ID"];
                        $arItem["BASKET_ITEM_ID"] = $item["BASKET_ID"];
                        $arItem["QUANTITY"] = $item["QUANTITY"];
                        $arItem["PRICE"]["FULL_PRICE"] = $item["BASE_PRICE"];
                        $arItem["PRICE"]["DISCOUNT_PRICE"] = $item["PRICE"];
                        $arItem["PRICE"]["DISCOUNT"] = $item["DISCOUNT_PRICE"];
                        $arItem["PRICE"]["MIN_QUANTITY"] = $item["MEASURE_RATIO"];

                        //Dobavlyaem v tovar svoystva korzini
                        $arItem["BASKET_PROPS"] = $item["BASKET_PROPS"];

                        $arProductList[$item["BASKET_ID"]] = $arItem;
                        $arAllInfo["ELEMENTS"][$item["PRODUCT_ID"]] = $arItem;
                        $arProductsId[] = $item["PRODUCT_ID"];
                    }

                    break;

                case 'catalog':
                    $arItem["ID"] = $item["ID"];

                    $arProductsId[] = $item["ID"];


                    if(!empty($item["OFFERS"])):
                        //Perebiraem offersi
                        foreach($item["OFFERS"] as $offer):

                            $arOffer = array("ID" => $offer["ID"], "OFFER" => 'Y', "MAIN_PRODUCT_ID" => $item["ID"]);
                            $arItem["OFFERS"][$arOffer["ID"]] = $arOffer["ID"];
                            $arProductsId[] = $arOffer["ID"];

                            if(!empty($offer["MIN_PRICE"]) && isset($offer["MIN_PRICE"]["VALUE"]) && isset($offer["MIN_PRICE"]["DISCOUNT_VALUE"]) && isset($offer["MIN_PRICE"]["DISCOUNT_DIFF"]) && isset($offer["MIN_PRICE"]["MIN_QUANTITY"]))
                            {
                                $offer["PRICE"] = [
                                    "FULL_PRICE" => $offer["MIN_PRICE"]["VALUE"],
                                    "DISCOUNT_PRICE" => $offer["MIN_PRICE"]["DISCOUNT_VALUE"],
                                    "DISCOUNT" => $offer["MIN_PRICE"]["DISCOUNT_DIFF"],
                                    "MIN_QUANTITY" => $offer["MIN_PRICE"]["MIN_QUANTITY"]
                                ];
                            }
                            if(!empty($offer["ITEM_PRICES"]) && count($offer["ITEM_PRICES"]) > 0)
                                $arOffer["PRICE"]["PRICE_MATRIX"] = $offer["ITEM_PRICES"];

                            $arAllInfo["ELEMENTS"][$arOffer["ID"]] = $arOffer;
                        endforeach;
                    else:
                        if(!empty($item["MIN_PRICE"]) && isset($item["MIN_PRICE"]["VALUE"]) && isset($item["MIN_PRICE"]["DISCOUNT_VALUE"]) && isset($item["MIN_PRICE"]["DISCOUNT_DIFF"]) && isset($item["MIN_PRICE"]["MIN_QUANTITY"]))
                        {
                            $arItem["PRICE"] = [
                                "FULL_PRICE" => $item["MIN_PRICE"]["VALUE"],
                                "DISCOUNT_PRICE" => $item["MIN_PRICE"]["DISCOUNT_VALUE"],
                                "DISCOUNT" => $item["MIN_PRICE"]["DISCOUNT_DIFF"],
                                "MIN_QUANTITY" => $item["MIN_PRICE"]["MIN_QUANTITY"]
                            ];
                        }
                        if(!empty($item["ITEM_PRICES"]) && count($item["ITEM_PRICES"]) > 0)
                            $arItem["PRICE"]["PRICE_MATRIX"] = $item["ITEM_PRICES"];
                    endif;

                    $arProductList[$item["ID"]] = $arItem;
                    $arAllInfo["ELEMENTS"][$item["ID"]] = $arItem;
                    break;

            }

        endforeach;
        $arProductsId = array_unique($arProductsId);

        $dbProdSections = \CIBlockElement::GetElementGroups($arProductsId, true, array('ID', 'IBLOCK_ELEMENT_ID'));
        while($prSect = $dbProdSections->Fetch())
        {
            $arAllInfo["ELEMENTS"][$prSect["IBLOCK_ELEMENT_ID"]]["SECTIONS"][] = $prSect["ID"];
        }

        $arIblocks = array();
        $DBproducts = \CIBlockElement::GetList(array("ID"=>"ASC"), array("ID" => $arProductsId), false, false, array("ID", "NAME", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
        while($el = $DBproducts->GetNextElement())
        {
            $arProd = $el->GetFields();
            $arAllInfo["ELEMENTS"][$arProd["ID"]]["FIELDS"] = $arProd;
            $arAllInfo["ELEMENTS"][$arProd["ID"]]["PROPERTIES"] = $el->GetProperties();

            //GetPrice
            if(!isset($arAllInfo["ELEMENTS"][$arProd["ID"]]["OFFERS"]))
            {
                $arPrice = $arAllInfo["ELEMENTS"][$arProd["ID"]]["PRICE"];
                if(!isset($arPrice["DISCOUNT_PRICE"]) || !is_numeric($arPrice["DISCOUNT_PRICE"])) //Check is_array for custom projects and components of catalog
                {
                    if(isset($arPrice["PRICE_MATRIX"]) && count($arPrice["PRICE_MATRIX"]) == 1 && $arPrice["PRICE_MATRIX"][0]["MIN_QUANTITY"] > 0)
                    {
                        $arPrice["FULL_PRICE"] = $arPrice["PRICE_MATRIX"][0]["BASE_PRICE"];
                        $arPrice["DISCOUNT_PRICE"] = $arPrice["PRICE_MATRIX"][0]["PRICE"];
                        $arPrice["DISCOUNT"] = $arPrice["PRICE_MATRIX"][0]["DISCOUNT"];
                        $arPrice["MIN_QUANTITY"] = $arPrice["PRICE_MATRIX"][0]["MIN_QUANTITY"];
                    }
                    else
                    {
                        $optimalPrice = self::GetOptimalPrice($arProd["ID"], 1, $arAllInfo["PARAMS"]["USER_GROUPS"], "N", array(), $arAllInfo["PARAMS"]["SITE_ID"]);
                        $arPrice["FULL_PRICE"] = $optimalPrice["RESULT_PRICE"]["BASE_PRICE"];
                        $arPrice["DISCOUNT_PRICE"] = $optimalPrice["RESULT_PRICE"]["DISCOUNT_PRICE"];
                        $arPrice["DISCOUNT"] = $optimalPrice["RESULT_PRICE"]["DISCOUNT"];
                        $minQuantity = \Bitrix\Catalog\MeasureRatioTable::getCurrentRatio($arProd["ID"]);
                        $arPrice["MIN_QUANTITY"] = $minQuantity[$arProd["ID"]];
                    }
                }
                if(!isset($arPrice["MIN_QUANTITY"]) || $arPrice["MIN_QUANTITY"] == '')
                {
                    $minQuantity = \Bitrix\Catalog\MeasureRatioTable::getCurrentRatio($arProd["ID"]);
                    $arPrice["MIN_QUANTITY"] = $minQuantity[$arProd["ID"]];
                }
                $arAllInfo["ELEMENTS"][$arProd["ID"]]["PRICE"] = $arPrice;
            }

            $arIblocks[] = $arProd["IBLOCK_ID"];
        }


        $sections = array();
        $arProp_ID_Code = array();
        foreach($arIblocks as $iblockId):

            //Get Sections
            $getSectionsProps = Array('ID', 'IBLOCK_ID', 'NAME','CODE','DEPTH_LEVEL','IBLOCK_SECTION_ID');
            if(!empty($arBonusProps))
                $getSectionsProps = array_merge($getSectionsProps, $arBonusProps);
            $dbSections = \CIBlockSection::GetList(array('left_margin' => 'asc'),Array('IBLOCK_ID'=>$iblockId),false,$getSectionsProps);
            $parent = array();
            while($arSection = $dbSections->fetch())
            {
                $arrCat[$arSection['ID']] = $arSection;

                if($arSection['DEPTH_LEVEL'] > $lastLevel && $lastLevel)
                    $parent[] = $lastId;

                if($arSection['DEPTH_LEVEL'] < $lastLevel && $lastLevel)
                    array_splice($parent, $arSection['DEPTH_LEVEL']-1, $lastLevel-$arSection['DEPTH_LEVEL']);

                if(!empty($parent))
                    $arSection["PARENTS"] = $parent;
                else
                    $arSection["PARENTS"] = [];

                $sections[$arSection['ID']] = $arSection;

                $lastLevel = $arSection['DEPTH_LEVEL'];
                $lastId = $arSection['ID'];
            }

            //Get Properties
            $dbIbProps = \CIBlock::GetProperties($iblockId);
            while($dbProp = $dbIbProps->Fetch())
            {
                $arProp_ID_Code[$dbProp["ID"]] = $dbProp;
            }
        endforeach;
        $arAllInfo["SECTIONS"]["SECTIONS_LIST"] = $sections;
        $arAllInfo["IBLOCKS_PROPS"] = $arProp_ID_Code;
        //---Get Sections and properties of Iblocks---//

        $arResult = array();
        $mainProfile = -1;

        foreach($arProductList as $arItem):
            $arBonus = self::GetBonusItem($arItem, $arAllInfo, $arProfiles, $arParams);
            if($arParams["TYPE"] == 'cart')
            {
                foreach($arBonus as $ibonus):
                    //Dobavlyaem v arresult bonusi za tovar po id tovara v korzine
                    if($ibonus['BASKET_ITEM_ID'] > 0)
                        $arResult[$ibonus['BASKET_ITEM_ID']] = $ibonus;
                    //Dobavlyaem v arresult bonusi za tovar po id tovara (dlya podderjki starih versiy)
                    $arResult = $arResult + $arBonus;
                endforeach;

            }
            else
                $arResult = $arResult + $arBonus;
            foreach($arBonus as $arItemBonus):
                $profileId = $arItemBonus["PROFILE_RULE"]["PROFILE"];
                if((int)$arProfiles[$profileId]["sort"] > (int)$arProfiles[$mainProfile]["sort"])
                    $mainProfile = $arItemBonus["PROFILE_RULE"]["PROFILE"];
            endforeach;
        endforeach;

        if($mainProfile == -1):
            foreach($arProfiles as $profSort):
                if(isset($arProfiles[$mainProfile]))
                {
                    if($profSort["sort"] > $arProfiles[$mainProfile]["sort"])
                        $mainProfile = $profSort["id"];
                }
                else
                    $mainProfile = $profSort["id"];
            endforeach;
        endif;


        if($arParams["TYPE"] == 'cart')
        {
            $sum = 0;
            foreach($arProducts as $item):
                $sum = $sum + $arResult[$item["BASKET_ID"]]["ADD_BONUS"];
            endforeach;

            if($fixOrderBonus > 0)
            {
                $sum = $sum + $fixOrderBonus;
                $cartResult["FIX_ORDER_BONUS"] = $fixOrderBonus;
            }

            $cartResult["ALL_BONUS"] = $sum;
            $cartResult["ITEMS"] = $arResult;
            $arResult = $cartResult;

            $arResult["PROFILE"] = array(
                "PROFILE_ID" => $mainProfile,
                "ACTIVE_AFTER" => $arProfiles[$mainProfile]["active_after_period"],
                "ACTIVE_AFTER_TYPE" => $arProfiles[$mainProfile]["active_after_type"],
                "DEACTIVE_AFTER" => $arProfiles[$mainProfile]["deactive_after_period"],
                "DEACTIVE_AFTER_TYPE" => $arProfiles[$mainProfile]["deactive_after_type"],
            );
        }

        if($arParams["PROFILE_TYPE"] == 'pay_bonus')
        {
            $arProfile = end($arProfiles);
            $arResult["PROFILE"] = array(
                "PROFILE_ID" => $mainProfile,
                "OTHER_CONDITIONS" => unserialize($arProfile["other_conditions"])
            );
            if(empty($arProfile["PRODUCT_CONDITIONS"]))
                $arResult["PROFILE"]["NO_PRODUCT_CONDITIONS"] = 'Y';
        }

        return $arResult;
    }



    public static function OrderBonusPayment($arItems, $arOrderParams)
    {
        $arResult = array();

        $UserBallance = (float)\Itb\Bonus\ItbHelpers::UserBallance($arOrderParams["USER_ID"]);

        if($UserBallance <= 0)
            return;

        $arPayBonus = self::getBonus($arItems, array("TYPE"=>'cart', "PROFILE_TYPE"=>"pay_bonus", "ORDER"=>$arOrderParams, "SORT_FIELD_1" => 'sort', "SORT_ORDER_1" => 'ASC'));
        $round = isset($arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["PAYMENT_ROUND"]) ? $arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["PAYMENT_ROUND"] : 2;

        /* ESLI EST' OGRANICHENIE NA MINIMAL'NUYU STOIMOST' TIVARA POSLE OPLATI BONUSAMI */
        if(isset($arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MIN_PRODUCT_PRICE"]))
            $minPrice = (float)$arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MIN_PRODUCT_PRICE"];
        else
            $minPrice = 0;
        if($minPrice > 0):
            $correctSum = 0; $minProdPrice = 0;
            foreach($arPayBonus['ITEMS'] as &$arItem):
                $minProdPrice = $arItem['PRICE']['DISCOUNT_PRICE']-$minPrice;
                if($arItem['ADD_BONUS_UNIT'] > $minProdPrice)
                {
                    $difPrice = $arItem['ADD_BONUS_UNIT'] - $minProdPrice;
                    $correctSum = $difPrice * $arItem['QUANTITY'];

                    $arItem['ADD_BONUS_UNIT'] = $minProdPrice;
                    $arItem['ADD_BONUS'] = $minProdPrice * $arItem['QUANTITY'];
                }
            endforeach;
            $arPayBonus['ALL_BONUS'] = $arPayBonus['ALL_BONUS'] - $correctSum;
        endif;
        /* ESLI EST' OGRANICHENIE NA MINIMAL'NUYU STOIMOST' TIVARA POSLE OPLATI BONUSAMI */

        /* GET PAY BONUS SUM */
        $pay_bonus = $arOrderParams["PAY_BONUS"];
        if((string)$arOrderParams["PAY_BONUS"] == 'MAX')
            $pay_bonus = $UserBallance;
        if($pay_bonus > $UserBallance)
            $pay_bonus = $UserBallance;
        /* GET PAY BONUS SUM */

        /* MIN SUM FROM PROFILE */
        if($arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MIN_PAYMENT_TYPE"] == 'bonus')
            $minOrderPay = (float)$arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MIN_PAYMENT_BONUS"];
        elseif($arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MIN_PAYMENT_TYPE"] == 'percent')
        {
            if($arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MIN_PAYMENT_INCLUDE_SHIPPING"] == 'Y')
                $minOrderPay = $arOrderParams["ORDER_SUM"] * (float)$arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MIN_PAYMENT_BONUS"] / 100;
            else
                $minOrderPay = $arOrderParams["CART_SUM"] * (float)$arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MIN_PAYMENT_BONUS"] / 100;

            /* ESLI EST' OGRANICHENIE NA MINIMAL'NUYU STOIMOST' TIVARA POSLE OPLATI BONUSAMI */
            if($minPrice > 0 && $minOrderPay > $arPayBonus['ALL_BONUS'])
            {
                if($arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MIN_PAYMENT_INCLUDE_SHIPPING"] == 'Y')
                    $minOrderPay = $arPayBonus['ALL_BONUS'] + $arOrderParams["DELIVERY_SUM"];
                else
                    $minOrderPay = $arPayBonus['ALL_BONUS'];
            }
            /* ESLI EST' OGRANICHENIE NA MINIMAL'NUYU STOIMOST' TIVARA POSLE OPLATI BONUSAMI */
        }
        $minOrderPay = $minOrderPayRound = round($minOrderPay, $round);
        if($minOrderPayRound == 0 && $round == 0)
            $minOrderPayRound = 1;
        if($minOrderPayRound == 0 && $round == 1)
            $minOrderPayRound = 0.1;
        if($minOrderPayRound == 0 && $round == 2)
            $minOrderPayRound = 0.01;
        /* MIN SUM FROM PROFILE */

        /* MAX SUM FROM PROFILE */
        if($arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MAX_PAYMENT_TYPE"] == 'bonus')
            $maxOrderPay = (float)$arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MAX_PAYMENT_BONUS"];
        elseif($arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MAX_PAYMENT_TYPE"] == 'percent')
        {
            if($arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MAX_PAYMENT_INCLUDE_SHIPPING"] == 'Y')
                $maxOrderPay = $arOrderParams["ORDER_SUM"] * (float)$arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MAX_PAYMENT_BONUS"] / 100;
            else
                $maxOrderPay = $arOrderParams["CART_SUM"] * (float)$arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MAX_PAYMENT_BONUS"] / 100;
        }
        $maxOrderPay = \Itb\Bonus\ItbHelpers::Round($maxOrderPay, $round, 'DOWN');
        /* MAX SUM FROM PROFILE */

        /* DELIVERY CONDITION */
        $canDeliveryPay = $arPayBonus["PROFILE"]["OTHER_CONDITIONS"]["MAX_PAYMENT_INCLUDE_SHIPPING"] == 'Y' ? $arOrderParams["DELIVERY_SUM"] : 0;

        if($arPayBonus["PROFILE"]["NO_PRODUCT_CONDITIONS"] == 'Y')
        {
            $canProductsPay = $arOrderParams["CART_SUM"];
            $productsPaySum = $arOrderParams["CART_SUM"];
        }
        else
        {
            $canProductsPay = $arPayBonus["ALL_BONUS"];
            $productsPaySum = 0;
            foreach($arItems as $cartItem):
                if($arPayBonus["ITEMS"][$cartItem["PRODUCT_ID"]]["ADD_BONUS"] > 0)
                    $productsPaySum = $productsPaySum + $cartItem["POSITION_FINAL_PRICE"];
            endforeach;
        }

        if($canProductsPay+$canDeliveryPay < $maxOrderPay)
            $maxOrderPay = $canProductsPay + $canDeliveryPay;
        $maxOrderPay = \Itb\Bonus\ItbHelpers::Round($maxOrderPay, $round, 'DOWN');
        /* DELIVERY CONDITION */


        if($pay_bonus < $minOrderPayRound && $pay_bonus != 0)
            $pay_bonus = $minOrderPayRound;
        if($pay_bonus > $maxOrderPay)
            $pay_bonus = $maxOrderPay;
        if($maxOrderPay < $minOrderPayRound)
            $pay_bonus = '0';
        if($maxOrderPay > $UserBallance)
            $maxOrderPay = \Itb\Bonus\ItbHelpers::Round($UserBallance, $round, 'DOWN');
        $pay_bonus = \Itb\Bonus\ItbHelpers::Round($pay_bonus, $round, 'DOWN');

        if($pay_bonus > $UserBallance || $pay_bonus < $minOrderPay || $pay_bonus > $maxOrderPay)
            $pay_bonus = '0';


        if($pay_bonus > $canProductsPay)
        {
            if($canDeliveryPay > 0)
            {
                $payDelivery = $pay_bonus - $canProductsPay;
                $newDeliveryPrice = $arOrderParams["DELIVERY_SUM"] - $payDelivery;
                $payCart = $canProductsPay;
            }
            else
            {
                $payDelivery = 0;
                $payCart = $canProductsPay;
            }
        }
        else
        {
            $payDelivery = 0;
            $payCart = $pay_bonus;
        }

        $arResult["PAY_BONUS"] = $pay_bonus;
        $arResult["PAY_CART"] = $payCart;
        $arResult["PAY_PRODUCTS_SUM"] = $productsPaySum;
        $arResult["PAY_DELIVERY"] = $payDelivery;
        $arResult["NEW_DELIVERY_PRICE"] = $newDeliveryPrice;

        $arResult["MIN_ORDER_PAY"] = $minOrderPay;
        $arResult["MIN_ORDER_PAY_ROUND"] = $minOrderPayRound;
        $arResult["MAX_ORDER_PAY"] = \Itb\Bonus\ItbHelpers::Round($maxOrderPay, $round, 'DOWN');
        $arResult["PAY_PRODUCTS"] = $arPayBonus;

        $arResult["ORDER_PARAMS"] = $arOrderParams;

        return $arResult;
    }
}