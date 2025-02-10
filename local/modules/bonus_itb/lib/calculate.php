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

                $prof =  unserialize($arProfil['CONDITIONS'])['children'];
                $condition_price =  unserialize($arProfil['CONDITIONS_PRICE']);
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

//            foreach($arBonus as $arItemBonus):
//                $profileId = $arItemBonus["PROFILE_RULE"]["PROFILE"];
//                if((int)$arProfiles[$profileId]["sort"] > (int)$arProfiles[$mainProfile]["sort"])
//                    $mainProfile = $arItemBonus["PROFILE_RULE"]["PROFILE"];
//            endforeach;
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
            );
        }

        if($arParams["PROFILE_TYPE"] == 'pay_bonus')
        {
            $arProfile = end($arProfiles);
            $arResult["PROFILE"] = array(
                "PROFILE_ID" => $mainProfile,
                "OTHER_CONDITIONS" => $condition_price
            );
            if(empty($arProfile["PRODUCT_CONDITIONS"]))
                $arResult["PROFILE"]["NO_PRODUCT_CONDITIONS"] = 'Y';
        }

        return $arResult;
    }

    public static function GetBonusItem($arItem, $arAllInfo, $arProfiles, $arParams)
    {

        if($arParams["TYPE"] == 'cart')
        {
            $arAllInfo["ELEMENTS"][$arItem["ID"]]["PRICE"]["DISCOUNT_PRICE"] = $arItem["PRICE"]["DISCOUNT_PRICE"];
            $arAllInfo["ELEMENTS"][$arItem["ID"]]["PRICE"]["DISCOUNT"] = $arItem["PRICE"]["DISCOUNT"];
            $arAllInfo["ELEMENTS"][$arItem["ID"]]["QUANTITY"] = $arItem["QUANTITY"];
            $arAllInfo["ELEMENTS"][$arItem["ID"]]['BASKET_PROPS'] = $arItem["BASKET_PROPS"];
        }

        $arResult = array();
        $arBonus = self::CheckBonus($arItem["ID"], $arAllInfo, $arProfiles);

        $arResult[$arItem["ID"]] = $arItem;

        if(!empty($arItem["OFFERS"]))
        {
            foreach($arItem["OFFERS"] as $offerId):

                if($arParams["TYPE"] == 'cart')
                {
                    $arAllInfo["ELEMENTS"][$offerId]["PRICE"]["DISCOUNT_PRICE"] = $arItem["PRICE"]["DISCOUNT_PRICE"];
                    $arAllInfo["ELEMENTS"][$offerId]["PRICE"]["DISCOUNT"] = $arItem["PRICE"]["DISCOUNT"];
                    $arAllInfo["ELEMENTS"][$offerId]["QUANTITY"] = $arItem["QUANTITY"];
                    $arAllInfo["ELEMENTS"][$offerId]['BASKET_PROPS'] = $arItem["BASKET_PROPS"];
                }

                $bonusOffer = $bonusOfferView = $bonusOfferAll = 0;
                $arBonusOffer = self::CheckBonus($offerId, $arAllInfo, $arProfiles);

                $minQuantity = $arAllInfo["ELEMENTS"][$offerId]["PRICE"]["MIN_QUANTITY"];

                if($arAllInfo["ELEMENTS"][$offerId]["QUANTITY"] > 0)
                    $offerQuantity = $arAllInfo["ELEMENTS"][$offerId]["QUANTITY"];
                else
                    $offerQuantity = $minQuantity;

                $bonusOffer = $arPrice = '';
                if($arBonusOffer["BONUS"] > 0)
                {
                    if($arBonusOffer["BONUS_TYPE"] == 'bonus')
                    {
                        $arPrice = $arAllInfo["ELEMENTS"][$offerId]["PRICE"];

                        $bonusOffer = $arBonusOffer["BONUS"];
                        $bonusOfferAll = $arBonusOffer["BONUS"] * $offerQuantity;

                        if($arBonusOffer["VIEW_IN_CATALOG"] == 'N' && $arParams["TYPE"] == 'catalog')
                            $bonusOfferView = $bonusOfferAllView = 0;
                        else
                        {
                            $bonusOfferView = $bonusOffer;
                            $bonusOfferAllView = $bonusOfferAll;
                        }


                    }
                    elseif($arBonusOffer["BONUS_TYPE"] == 'percent')
                    {
                        $arPrice = $arAllInfo["ELEMENTS"][$offerId]["PRICE"];

                        $arBonusPosition = self::calcUnitAndPosition($arPrice, $offerQuantity, $arBonusOffer, $arParams);
                        $bonusOffer = $arBonusPosition["BONUS_UNIT"];
                        $bonusOfferAll = $arBonusPosition["BONUS_POSITION"];

                        if($arBonusOffer["VIEW_IN_CATALOG"] == 'N' && $arParams["TYPE"] == 'catalog')
                            $bonusOfferView = $bonusOfferAllView = 0;
                        else
                        {
                            $bonusOfferView = $bonusOffer;
                            $bonusOfferAllView = $bonusOfferAll;
                        }


                        if(isset($arPrice["PRICE_MATRIX"]))
                        {
                            $priceMatrix = array();
                            foreach($arPrice["PRICE_MATRIX"] as $onePrice):
                                $onePrice["ADD_BONUS"] = round($arBonusOffer["BONUS"] * $onePrice["PRICE"] / 100, $arBonusOffer["ROUND"]);
                                if($arBonusOffer["VIEW_IN_CATALOG"] == 'N' && $arParams["TYPE"] == 'catalog')
                                    $onePrice["VIEW_BONUS"] = 0;
                                else
                                    $onePrice["VIEW_BONUS"] = $onePrice["ADD_BONUS"];
                                $priceMatrix[$onePrice["QUANTITY_HASH"]] = $onePrice;
                            endforeach;
                            $arPrice["PRICE_MATRIX"] = $priceMatrix;
                        }
                    }
                }


                $arResult[$offerId] = array(
                    "ID"=>$offerId,
                    "OFFER"=>'Y',
                    "MAIN_PRODUCT"=>array("ID"=>$arItem["ID"]),
                    "NAME" => $arAllInfo["ELEMENTS"][$offerId]["FIELDS"]["NAME"],
                    "BONUS" => $arBonusOffer["BONUS"],
                    "ADD_BONUS_UNIT" => $bonusOffer,
                    "ADD_BONUS"=>$bonusOfferAll,
                    "VIEW_BONUS"=>$bonusOfferView,
                    "BONUS_TYPE"=>$arBonusOffer["BONUS_TYPE"],
                    "QUANTITY" => $offerQuantity,
                    "PRICE"=>$arPrice,
                    "PROFILE_RULE" => $arBonusOffer["PROFILE_RULE"],
                    "VIEW_IN_CATALOG" => $arBonusOffer["VIEW_IN_CATALOG"],
                    "ROUND" => $arBonusOffer["ROUND"],
                    "ROUND_TYPE" => $arBonusOffer["ROUND_TYPE"]
                );

                if($arParams["TYPE"] == 'cart')
                    $arResult[$offerId]["BASKET_ITEM_ID"] = $arItem["BASKET_ITEM_ID"];

                if($bonusOffer > $arResult[$arItem["ID"]]["ADD_BONUS"])
                {
                    $arResult[$arItem["ID"]]["NAME"] = $arAllInfo["ELEMENTS"][$arItem["ID"]]["FIELDS"]["NAME"];
                    $arResult[$arItem["ID"]]["BONUS"] = $arBonusOffer["BONUS"];
                    $arResult[$arItem["ID"]]["ADD_BONUS_UNIT"] = $bonusOffer;
                    $arResult[$arItem["ID"]]["ADD_BONUS"] = $bonusOfferAll;
                    $arResult[$arItem["ID"]]["VIEW_BONUS"] = $bonusOfferView;
                    $arResult[$arItem["ID"]]["BONUS_TYPE"] = $arBonusOffer["BONUS_TYPE"];
                    $arResult[$arItem["ID"]]["PRICE"] = $arPrice;
                    $arResult[$arItem["ID"]]["PROFILE_RULE"] = $arBonusOffer["PROFILE_RULE"];
                    $arResult[$arItem["ID"]]["ROUND"] = $arBonusOffer["ROUND"];
                    $arResult[$arItem["ID"]]["ROUND_TYPE"] = $arBonusOffer["ROUND_TYPE"];
                }

            endforeach;

        }
        else
        {
            if($arBonus["BONUS"] > 0)
            {
                $minQuantity = $arAllInfo["ELEMENTS"][$arItem["ID"]]["PRICE"]["MIN_QUANTITY"];

                if($arAllInfo["ELEMENTS"][$arItem["ID"]]["QUANTITY"] > 0)
                    $quantity = $arAllInfo["ELEMENTS"][$arItem["ID"]]["QUANTITY"];
                else
                    $quantity = $minQuantity;

                if($arBonus["BONUS_TYPE"] == 'bonus')
                {
                    $bonus = $arBonus["BONUS"];
                    $bonusAll = $arBonus["BONUS"] * $quantity;

                    if($arBonus["VIEW_IN_CATALOG"] == 'N' && $arParams["TYPE"] == 'catalog')
                        $bonusView = $bonusAllView = 0;
                    else
                    {
                        $bonusView = $bonus;
                        $bonusAllView = $bonusAll;
                    }
                }
                elseif($arBonus["BONUS_TYPE"] == 'percent')
                {
                    $arPrice = $arAllInfo["ELEMENTS"][$arItem["ID"]]["PRICE"];

                    $arBonusPosition = self::calcUnitAndPosition($arPrice, $quantity, $arBonus, $arParams);
                    $bonus = $arBonusPosition["BONUS_UNIT"];
                    $bonusAll = $arBonusPosition["BONUS_POSITION"];

                    if($arBonus["VIEW_IN_CATALOG"] == 'N' && $arParams["TYPE"] == 'catalog')
                        $bonusView = $bonusAllView = 0;
                    else
                    {
                        $bonusView = $bonus;
                        $bonusAllView = $bonusAll;
                    }

                    if(isset($arPrice["PRICE_MATRIX"]))
                    {
                        $priceMatrix = array();
                        foreach($arPrice["PRICE_MATRIX"] as $onePrice):
                            $onePrice["ADD_BONUS"] = round($arBonus["BONUS"] * $onePrice["PRICE"] / 100, $arBonus["ROUND"]);
                            if($arBonus["VIEW_IN_CATALOG"] == 'N' && $arParams["TYPE"] == 'catalog')
                                $onePrice["VIEW_BONUS"] = 0;
                            else
                                $onePrice["VIEW_BONUS"] = $onePrice["ADD_BONUS"];
                            $priceMatrix[$onePrice["QUANTITY_HASH"]] = $onePrice;
                        endforeach;
                        $arPrice["PRICE_MATRIX"] = $priceMatrix;
                    }
                }
            }
            $arResult[$arItem["ID"]]["NAME"] = $arAllInfo["ELEMENTS"][$arItem["ID"]]["FIELDS"]["NAME"];
            $arResult[$arItem["ID"]]["BONUS"] = $arBonus["BONUS"];
            $arResult[$arItem["ID"]]["ADD_BONUS_UNIT"] = $bonus;
            $arResult[$arItem["ID"]]["ADD_BONUS"] = $bonusAll;
            $arResult[$arItem["ID"]]["VIEW_BONUS"] = $bonusView;
            $arResult[$arItem["ID"]]["BONUS_TYPE"] = $arBonus["BONUS_TYPE"];
            $arResult[$arItem["ID"]]["PRICE"] = $arPrice;
            $arResult[$arItem["ID"]]["PROFILE_RULE"] = $arBonus["PROFILE_RULE"];
            $arResult[$arItem["ID"]]["VIEW_IN_CATALOG"] = $arBonus["VIEW_IN_CATALOG"];
            $arResult[$arItem["ID"]]["ROUND"] = $arBonus["ROUND"];
            $arResult[$arItem["ID"]]["ROUND_TYPE"] = $arBonus["ROUND_TYPE"];

        }
        return $arResult;
    }

    public static function CheckBonus($arItemId, $arAllInfo, $arProfiles)
    {
        $arElementProps = $arAllInfo["ELEMENTS"][$arItemId];

        $arBonus = array("BONUS" => '', "BONUS_TYPE" => '');

        //Perebiraem profili
        foreach($arProfiles as $arProfile):

            //Perebiraem gruppi usloviy
            foreach($arProfile["PRODUCT_CONDITIONS"] as $arConditions):

                if($arConditions["controlId"] == 'conditionGroup3')
                    continue;

                $bonusType = $arConditions["values"]["bonus_type"];
                $round = $arConditions["values"]["round"];
                if(isset($arConditions["values"]["round_type"]))
                    $round_type = $arConditions["values"]["round_type"];
                else
                    $round_type = 'UNIT';
                if(isset($arConditions["values"]["round_method"]))
                    $round_method = $arConditions["values"]["round_method"];
                else
                    $round_method = 'MATH';
                $globalLogic = $arConditions["values"]["All"];
                if(isset($arConditions["values"]["bonus"]))
                    $bonus = (float)$arConditions["values"]["bonus"];

                elseif($arConditions["values"]["bonus_from_props"])
                {
                    $bonusProp = $arConditions["values"]["bonus_from_props"];
                    $bonus = 'N';
                    $sectId = $arElementProps["FIELDS"]["IBLOCK_SECTION_ID"];
                    if($arElementProps["OFFER"] == 'Y')
                    {
                        $mainProductId = $arElementProps["MAIN_PRODUCT_ID"];
                        $sectId =  $arAllInfo["ELEMENTS"][$arElementProps["MAIN_PRODUCT_ID"]]["FIELDS"]["IBLOCK_SECTION_ID"];
                    }

                    if($arElementProps["PROPERTIES"][$bonusProp]["VALUE"] != '')
                        $bonus = (float)$arElementProps["PROPERTIES"][$bonusProp]["VALUE"];

                    if($arElementProps["OFFER"] == 'Y')
                    {
                        if($bonus == 'N' && $arAllInfo["ELEMENTS"][$mainProductId]["PROPERTIES"][$bonusProp]["VALUE"] != '')
                            $bonus = (float)$arAllInfo["ELEMENTS"][$mainProductId]["PROPERTIES"][$bonusProp]["VALUE"];
                    }



                    if($bonus == 'N' && $arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sectId][$bonusProp] != '')
                        $bonus = (float)$arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sectId][$bonusProp];
                    if($bonus == 'N' && !empty($arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sectId]["PARENTS"]))
                    {
                        foreach($arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sectId]["PARENTS"] as $sId):
                            if(is_numeric($arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sId][$bonusProp]))
                                $bonus = $arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sId][$bonusProp];
                        endforeach;
                    }

                    if((string)$bonus == 'N')
                        continue;

                }

                $arDone = array("CONDITIONS_DONE"=>array(), "CONDITIONS_NO_DONE"=>array());
                $GroupDone = (!empty($arConditions["children"]) ? 'N' : 'Y');

                //Perebiraem usloviya
                foreach($arConditions["children"] as $arCondition):
                    $logic = $arCondition["values"]["logic"];
                    $logicValue = $arCondition["values"]["value"];
                    $done = 'N';
                    switch(true)
                    {
                        case $arCondition["controlId"] == 'product':

                            if($arElementProps["OFFER"] == 'Y')
                            {
                                //$mainProductId = $arElementProps["MAIN_PRODUCT_ID"]
                                if($logic == 'Equal' && in_array($arElementProps["ID"], $logicValue) ||  $logic == 'Equal' && in_array($arElementProps["MAIN_PRODUCT_ID"], $logicValue))
                                    $done = 'Y';
                                if($logic == 'Not' && !in_array($arElementProps["ID"], $logicValue) && $logic == 'Not' && !in_array($arElementProps["MAIN_PRODUCT_ID"], $logicValue))
                                    $done = 'Y';
                            }
                            else
                            {
                                if(in_array($arElementProps["ID"], $logicValue) && $logic == 'Equal')
                                    $done = 'Y';
                                if(!in_array($arElementProps["ID"], $logicValue) && $logic == 'Not')
                                    $done = 'Y';
                            }
                            break;

                        case $arCondition["controlId"] == 'product_categoty':
                            if($arElementProps["OFFER"] == 'Y')
                                $arElementProps["SECTIONS"] = $arAllInfo["ELEMENTS"][$arElementProps["MAIN_PRODUCT_ID"]]["SECTIONS"];

                            if(!empty($arElementProps["SECTIONS"]))
                            {
                                if($logic == 'Equal')
                                {
                                    foreach($arElementProps["SECTIONS"] as $sectionId):
                                        if(!isset($arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sectionId]))
                                            $arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sectionId]["PARENTS"] = [];
                                        if($sectionId == $logicValue || in_array($logicValue ,$arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sectionId]["PARENTS"]))
                                            $done = 'Y';
                                    endforeach;
                                }
                                if($logic == 'Not')
                                {
                                    $inside = [];
                                    foreach($arElementProps["SECTIONS"] as $sectionId):
                                        if(!isset($arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sectionId]))
                                            $arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sectionId]["PARENTS"] = [];

                                        if(in_array($logicValue, $arElementProps["SECTIONS"]) || in_array($logicValue ,$arAllInfo["SECTIONS"]["SECTIONS_LIST"][$sectionId]["PARENTS"]))
                                            $inside[] = 'Y';
                                    endforeach;

                                    if(empty($inside))
                                        $done = 'Y';
                                }

                            }
                            break;

                        case $arCondition["controlId"] == 'iblock':
                            if(in_array($arElementProps["FIELDS"]["IBLOCK_ID"], $logicValue) && $logic == 'Equal')
                                $done = 'Y';
                            if(!in_array($arElementProps["FIELDS"]["IBLOCK_ID"], $logicValue) && $logic == 'Not')
                                $done = 'Y';
                            break;

                        case $arCondition["controlId"] == 'price':
                            if(isset($arElementProps["OFFERS"]))
                            {
                                $done = 'A';
                                continue;
                            }
                            $price = $arElementProps["PRICE"]["DISCOUNT_PRICE"];
                            if($logic == 'Equal' && $logicValue == $price)
                                $done = 'Y';
                            if($logic == 'Not' && $logicValue != $price)
                                $done = 'Y';
                            if($logic == 'Great' && $price > $logicValue)
                                $done = 'Y';
                            if($logic == 'EqGr' && $price >= $logicValue)
                                $done = 'Y';
                            if($logic == 'Less' && $price < $logicValue)
                                $done = 'Y';
                            if($logic == 'EqLs' && $price <= $logicValue)
                                $done = 'Y';
                            break;

                        case $arCondition["controlId"] == 'discount':
                            $discount = $arElementProps["PRICE"]["DISCOUNT"];
                            if($logicValue == 'N' && $discount <= 0)
                                $done = 'Y';
                            if($logicValue == 'Y' && $discount > 0)
                                $done = 'Y';
                            break;

                        case $arCondition["controlId"] == 'discount_size':
                            if(isset($arElementProps["OFFERS"]))
                            {
                                $done = 'A';
                                continue;
                            }
                            $size_type = $arCondition["values"]["type"];

                            if($size_type == 'C')
                            {
                                if($arElementProps["PRICE"]["DISCOUNT"] >= 0)
                                    $discount = $arElementProps["PRICE"]["DISCOUNT"];
                                else
                                    $discount = 0;
                                if($logic == 'Equal' && $logicValue == $discount)
                                    $done = 'Y';
                                if($logic == 'Not' && $logicValue != $discount)
                                    $done = 'Y';
                                if($logic == 'Great' && $discount > $logicValue)
                                    $done = 'Y';
                                if($logic == 'EqGr' && $discount >= $logicValue)
                                    $done = 'Y';
                                if($logic == 'Less' && $discount < $logicValue)
                                    $done = 'Y';
                                if($logic == 'EqLs' && $discount <= $logicValue)
                                    $done = 'Y';

                            }
                            if($size_type == 'P')
                            {
                                if($arElementProps["PRICE"]["DISCOUNT"] >= 0)
                                    $discount = $arElementProps["PRICE"]["DISCOUNT"];
                                if($arElementProps["PRICE"]["FULL_PRICE"] > 0)
                                    $discount_percent = $discount * 100 / $arElementProps["PRICE"]["FULL_PRICE"];
                                else
                                    $discount_percent = 0;

                                $logicValue = round($logicValue, 2);
                                $discount_percent = round($discount_percent, 2);

                                if($logic == 'Equal' && $logicValue == $discount_percent)
                                    $done = 'Y';
                                if($logic == 'Not' && $logicValue != $discount_percent)
                                    $done = 'Y';
                                if($logic == 'Great' && $discount_percent > $logicValue)
                                    $done = 'Y';
                                if($logic == 'EqGr' && $discount_percent >= $logicValue)
                                    $done = 'Y';
                                if($logic == 'Less' && $discount_percent < $logicValue)
                                    $done = 'Y';
                                if($logic == 'EqLs' && $discount_percent <= $logicValue)
                                    $done = 'Y';
                            }
                            break;

                        case $arCondition["controlId"] == 'product_prop_in_cart':
                            if($arAllInfo["PARAMS"]["TYPE"] = 'cart'):
                                $checkType = $arCondition["values"]["logic-type"];
                                $checkTypeValue = $arCondition["values"]["logic-type_value"];

                                //unset($arElementProps['PROPERTIES']);
                                //echo '<pre>'.$arItemId; print_r($arElementProps); echo '</pre>';
                                if(isset($arElementProps["BASKET_PROPS"]) && !empty($arElementProps["BASKET_PROPS"])):
                                    $haveThisProp = 'N';
                                    foreach($arElementProps["BASKET_PROPS"] as $basketProp):
                                        //proveryaem svoistvo na primenimost'
                                        if($checkType == 'name' && $basketProp["NAME"] == $checkTypeValue || $checkType == 'xml_id' && $basketProp["CODE"] == $checkTypeValue)
                                        {
                                            $propValue = $basketProp["VALUE"];

                                            if($logic == 'Equal' && $logicValue == $propValue)
                                                $done = 'Y';
                                            if($logic == 'Not' && $logicValue != $propValue)
                                                $done = 'Y';
                                            if($logic == 'Contain' && strpos($propValue, $logicValue) !== false)
                                                $done = 'Y';
                                            if($logic == 'NotCont' && strpos($propValue, $logicValue) === false)
                                                $done = 'Y';

                                            $haveThisProp = 'Y';
                                        }
                                    endforeach;
                                else:
                                    $haveThisProp = 'N';
                                endif;

                                if($haveThisProp == 'N' && $logic == 'Not' || $haveThisProp == 'N' && $logic == 'NotCont')
                                    $done = 'Y';

                            endif;

                            break;


                        case strpos($arCondition["controlId"], 'CondIBProp') !== false:
                            $arExp = explode(':', $arCondition["controlId"]);
                            $iblockId = $arExp[1];
                            $propertyId = $arExp[2];
                            $arPropFields = $arAllInfo["IBLOCKS_PROPS"][$propertyId];
                            $propertyCode = $arPropFields["CODE"];

                            if($arElementProps["OFFER"] == 'Y')
                            {
                                $mainProductId = $arElementProps["MAIN_PRODUCT_ID"];
                                $mainElementProps = $arAllInfo["ELEMENTS"][$mainProductId];

                                if($iblockId == $mainElementProps["PROPERTIES"][$propertyCode]["IBLOCK_ID"])
                                    $arElementProps["PROPERTIES"][$propertyCode] = $mainElementProps["PROPERTIES"][$propertyCode];
                            }

                            if($iblockId != $arElementProps["PROPERTIES"][$propertyCode]["IBLOCK_ID"])
                            {
                                $done = 'A';
                                continue;
                            }


                            if($arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == '')
                            {

                                if($arPropFields["MULTIPLE"] == 'N')
                                {

                                    $elementPropValue = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];

                                    if($logic == 'Equal' && $logicValue == $elementPropValue)
                                        $done = 'Y';
                                    if($logic == 'Not' && $logicValue != $elementPropValue)
                                        $done = 'Y';
                                    if($logic == 'Contain' && strpos($elementPropValue, $logicValue) !== false)
                                        $done = 'Y';
                                    if($logic == 'NotCont' && strpos($elementPropValue, $logicValue) === false)
                                        $done = 'Y';
                                }

                                if($arPropFields["MULTIPLE"] == 'Y')
                                {
                                    $elementPropValue = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];
                                    if(!is_array($elementPropValue))
                                        $elementPropValue = [];

                                    if($logic == 'Equal' && in_array($logicValue, $elementPropValue))
                                        $done = 'Y';
                                    if($logic == 'Not' && !in_array($logicValue, $elementPropValue))
                                        $done = 'Y';
                                    if($logic == 'Contain')
                                    {
                                        $fdone = 0;
                                        foreach($elementPropValue as $val):
                                            if(strpos($val, $logicValue) !== false)
                                                $fdone++;
                                        endforeach;
                                        if($fdone > 0)
                                            $done = 'Y';
                                    }
                                    if($logic == 'NotCont')
                                    {
                                        $fdone = 0;
                                        foreach($elementPropValue as $val):
                                            if(strpos($val, $logicValue) !== false)
                                                $fdone++;
                                        endforeach;
                                        if($fdone == 0)
                                            $done = 'Y';
                                    }
                                }

                            }

                            elseif($arPropFields["PROPERTY_TYPE"] == 'N')
                            {
                                $elementPropValue = array();
                                if($arPropFields["MULTIPLE"] == 'N')
                                    $elementPropValue[] = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];
                                if($arPropFields["MULTIPLE"] == 'Y')
                                    $elementPropValue = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];

                                $fdone = 0;
                                foreach($elementPropValue as $val):
                                    $val =str_replace(',', '.', $val);

                                    if($logic == 'Equal' && $logicValue == $val)
                                        $fdone++;
                                    if($logic == 'Not' && $logicValue != $val)
                                        $fdone++;
                                    if($logic == 'Great' && $val > $logicValue)
                                        $fdone++;
                                    if($logic == 'EqGr' && $val >= $logicValue)
                                        $fdone++;
                                    if($logic == 'Less' && $val < $logicValue)
                                        $fdone++;
                                    if($logic == 'EqLs' && $val <= $logicValue)
                                        $fdone++;
                                endforeach;

                                if($fdone > 0)
                                    $done = 'Y';

                            }
                            elseif($arPropFields["PROPERTY_TYPE"] == 'L')
                            {
                                $elementPropValue = $arElementProps["PROPERTIES"][$propertyCode]["VALUE_ENUM_ID"];

                                if($arPropFields["MULTIPLE"] == 'N')
                                {
                                    if($logic == 'Equal' && $logicValue == $elementPropValue)
                                        $done = 'Y';
                                    if($logic == 'Not' && $logicValue != $elementPropValue)
                                        $done = 'Y';
                                }
                                if($arPropFields["MULTIPLE"] == 'Y')
                                {
                                    if(!is_array($elementPropValue))
                                        $elementPropValue = [];
                                    if($logic == 'Equal' && in_array($logicValue, $elementPropValue))
                                        $done = 'Y';
                                    if($logic == 'Not' && !in_array($logicValue, $elementPropValue))
                                        $done = 'Y';
                                }

                            }
                            elseif($arPropFields["PROPERTY_TYPE"] == 'E')
                            {
                                $elementPropValue = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];

                                if($arPropFields["MULTIPLE"] == 'N')
                                {
                                    if($logic == 'Equal' && $logicValue == $elementPropValue)
                                        $done = 'Y';
                                    if($logic == 'Not' && $logicValue != $elementPropValue)
                                        $done = 'Y';
                                }
                                if($arPropFields["MULTIPLE"] == 'Y')
                                {
                                    if(!is_array($elementPropValue))
                                        $elementPropValue = [];
                                    if($logic == 'Equal' && in_array($logicValue, $elementPropValue))
                                        $done = 'Y';
                                    if($logic == 'Not' && !in_array($logicValue, $elementPropValue))
                                        $done = 'Y';
                                }

                            }
                            elseif($arPropFields["PROPERTY_TYPE"] == 'G')
                            {
                                $elementPropValue = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];

                                if($arPropFields["MULTIPLE"] == 'N')
                                {
                                    if($logic == 'Equal' && $logicValue == $elementPropValue)
                                        $done = 'Y';
                                    if($logic == 'Not' && $logicValue != $elementPropValue)
                                        $done = 'Y';
                                }
                                if($arPropFields["MULTIPLE"] == 'Y')
                                {
                                    if(!is_array($elementPropValue))
                                        $elementPropValue = [];
                                    if($logic == 'Equal' && in_array($logicValue, $elementPropValue))
                                        $done = 'Y';
                                    if($logic == 'Not' && !in_array($logicValue, $elementPropValue))
                                        $done = 'Y';
                                }
                            }

                            elseif($arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'HTML')
                            {
                                $elementPropValue = array();
                                if($arPropFields["MULTIPLE"] == 'N')
                                    $elementPropValue[] = $arElementProps["PROPERTIES"][$propertyCode]["~VALUE"];
                                if($arPropFields["MULTIPLE"] == 'Y')
                                    $elementPropValue = $arElementProps["PROPERTIES"][$propertyCode]["~VALUE"];


                                if($logic == 'Equal')
                                {
                                    $fdone == 0;
                                    foreach($elementPropValue as $val):
                                        if($logicValue == $val["TEXT"])
                                            $fdone++;
                                    endforeach;
                                    if($fdone > 0)
                                        $done = 'Y';
                                }
                                if($logic == 'Not')
                                {
                                    $fdone == 0;
                                    foreach($elementPropValue as $val):
                                        if($logicValue != $val["TEXT"])
                                            $fdone++;
                                    endforeach;
                                    if($fdone > 0)
                                        $done = 'Y';
                                }
                                if($logic == 'Contain')
                                {
                                    $fdone = 0;
                                    foreach($elementPropValue as $val):
                                        if(strpos($val["TEXT"], $logicValue) !== false)
                                            $fdone++;
                                    endforeach;
                                    if($fdone > 0)
                                        $done = 'Y';
                                }
                                if($logic == 'NotCont')
                                {
                                    $fdone = 0;
                                    foreach($elementPropValue as $val):
                                        if(strpos($val["TEXT"], $logicValue) !== false)
                                            $fdone++;
                                    endforeach;
                                    if($fdone == 0)
                                        $done = 'Y';
                                }
                            }

                            elseif($arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'video')
                            {
                                $elementPropValue = array();
                                if($arPropFields["MULTIPLE"] == 'N')
                                    $elementPropValue[] = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];
                                if($arPropFields["MULTIPLE"] == 'Y')
                                    $elementPropValue = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];

                                if($logic == 'Equal')
                                {
                                    $fdone == 0;
                                    foreach($elementPropValue as $val):
                                        if($logicValue == $val["path"])
                                            $fdone++;
                                    endforeach;
                                    if($fdone > 0)
                                        $done = 'Y';
                                }
                                if($logic == 'Not')
                                {
                                    $fdone == 0;
                                    foreach($elementPropValue as $val):
                                        if($logicValue != $val["path"])
                                            $fdone++;
                                    endforeach;
                                    if($fdone > 0)
                                        $done = 'Y';
                                }
                                if($logic == 'Contain')
                                {
                                    $fdone = 0;
                                    foreach($elementPropValue as $val):
                                        if(strpos($val["path"], $logicValue) !== false)
                                            $fdone++;
                                    endforeach;
                                    if($fdone > 0)
                                        $done = 'Y';
                                }
                                if($logic == 'NotCont')
                                {
                                    $fdone = 0;
                                    foreach($elementPropValue as $val):
                                        if(strpos($val["path"], $logicValue) !== false)
                                            $fdone++;
                                    endforeach;
                                    if($fdone == 0)
                                        $done = 'Y';
                                }
                            }
                            elseif($arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'Date' || $arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'DateTime')
                            {
                                $elementPropValue = array();
                                if($arPropFields["MULTIPLE"] == 'N')
                                    $elementPropValue[] = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];
                                if($arPropFields["MULTIPLE"] == 'Y')
                                    $elementPropValue = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];

                                $fdone = 0;
                                foreach($elementPropValue as $val):
                                    $val = \MakeTimeStamp($val, \CSite::GetDateFormat());
                                    $logicValue = \MakeTimeStamp($logicValue, \CSite::GetDateFormat());

                                    if($logic == 'Equal' && $logicValue == $val)
                                        $fdone++;
                                    if($logic == 'Not' && $logicValue != $val)
                                        $fdone++;
                                    if($logic == 'Great' && $val > $logicValue)
                                        $fdone++;
                                    if($logic == 'EqGr' && $val >= $logicValue)
                                        $fdone++;
                                    if($logic == 'Less' && $val < $logicValue)
                                        $fdone++;
                                    if($logic == 'EqLs' && $val <= $logicValue)
                                        $fdone++;
                                endforeach;

                                if($logic == 'Not')
                                {
                                    if($fdone == 0)
                                        $done = 'Y';
                                }
                                else
                                {
                                    if($fdone > 0)
                                        $done = 'Y';
                                }
                            }

                            elseif($arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'Money')
                            {
                            }

                            elseif(
                                $arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'map_yandex'
                                ||
                                $arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'map_google'
                                ||
                                $arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'UserID'
                                ||
                                $arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'TopicID'
                                ||
                                $arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'FileMan'
                                ||
                                $arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'ElementXmlID'
                                ||
                                $arPropFields["PROPERTY_TYPE"] == 'S' && $arPropFields["USER_TYPE"] == 'directory'
                            )
                            {
                                $elementPropValue = $arElementProps["PROPERTIES"][$propertyCode]["VALUE"];

                                if($arPropFields["MULTIPLE"] == 'N')
                                {
                                    if($logic == 'Equal' && $logicValue == $elementPropValue)
                                        $done = 'Y';
                                    if($logic == 'Not' && $logicValue != $elementPropValue)
                                        $done = 'Y';
                                    if($logic == 'Contain' && strpos($elementPropValue, $logicValue) !== false)
                                        $done = 'Y';
                                    if($logic == 'NotCont' && strpos($elementPropValue, $logicValue) === false)
                                        $done = 'Y';
                                }

                                if($arPropFields["MULTIPLE"] == 'Y')
                                {
                                    if(!is_array($elementPropValue))
                                        $elementPropValue = [];
                                    if($logic == 'Equal' && in_array($logicValue, $elementPropValue))
                                        $done = 'Y';
                                    if($logic == 'Not' && !in_array($logicValue, $elementPropValue))
                                        $done = 'Y';
                                    if($logic == 'Contain')
                                    {
                                        $fdone = 0;
                                        foreach($elementPropValue as $val):
                                            if(strpos($val, $logicValue) !== false)
                                                $fdone++;
                                        endforeach;
                                        if($fdone > 0)
                                            $done = 'Y';
                                    }
                                    if($logic == 'NotCont')
                                    {
                                        $fdone = 0;
                                        foreach($elementPropValue as $val):
                                            if(strpos($val, $logicValue) !== false)
                                                $fdone++;
                                        endforeach;
                                        if($fdone == 0)
                                            $done = 'Y';
                                    }
                                }
                            }

                            break;
                    }
                    if($done == 'Y')
                        $arDone["CONDITIONS_DONE"][] = $arCondition;
                    elseif($done == 'N')
                        $arDone["CONDITIONS_NO_DONE"][] = $arCondition;
                    elseif($done == 'A')
                        $arDone["CONDITIONS_ALIEN"][] = $arCondition;

                endforeach; //conditions


                if($globalLogic == 'AND' && empty($arDone["CONDITIONS_NO_DONE"]))
                    $GroupDone = 'Y';
                if($globalLogic == 'OR' && !empty($arDone["CONDITIONS_DONE"]))
                    $GroupDone = 'Y';


                $arRound = array('A'=>0, 'B'=>1, 'C'=>2, 'D'=>3, 'E'=>4);
                if($GroupDone == 'Y')
                {
                    $arBonus["PRODUCT_ID"] = $arItemId;
                    $arBonus["BONUS"] = $bonus;
                    $arBonus["BONUS_TYPE"] = $bonusType;
                    $arBonus["ROUND"] = $arRound[$round];
                    $arBonus["ROUND_TYPE"] = $round_type;
                    $arBonus["ROUND_METHOD"] = $round_method;
                    $arBonus["VIEW_IN_CATALOG"] = $arProfile["VIEW_IN_CATALOG"];
                    $arBonus["PROFILE_RULE"] = array("PROFILE" => $arProfile["id"], "PROFILE_TYPE" => $arProfile['type'], "GROUP" => $arConditions["id"]);

                    $arBonus["PROFILES"][$arProfile["id"]] = array("ID" => $arProfile["id"], "VIEW_IN_CATALOG" => $arProfile["VIEW_IN_CATALOG"], "SORT" => $arProfile["sort"], "active_after_period" => $arProfile["active_after_period"], "active_after_type" => $arProfile["active_after_type"], "deactive_after_period" => $arProfile["deactive_after_period"], "deactive_after_period" => $arProfile["deactive_after_period"]);
                    //$arBonus["PROFILES"][$arProfile["id"]]["CONDITIONS"] = $arDone;
                }

            endforeach; //groups

        endforeach; //profiles

        /* SOBITIE POSLE PROVERKI USLOVIYA */
        $eventAfterCheck = $arBonus;
        $eventAfterCheck['PARAMS'] = $arAllInfo['PARAMS'];
        $eventAfterCheck['ELEMENT_INFO'] = $arAllInfo['ELEMENTS'][$arItemId];
        $event = new \Bitrix\Main\Event("logictim.balls", "BeforeCalculateBonus", $eventAfterCheck);
        $event->send();
        if($event->getResults())
        {
            foreach($event->getResults() as $eventResult):
                $arBonusCustom = $eventResult->getParameters();
                $arBonus['BONUS'] = $arBonusCustom['BONUS'];
                $arBonus['BONUS_TYPE'] = $arBonusCustom['BONUS_TYPE'];
                $arBonus['ROUND'] = $arBonusCustom['ROUND'];
                $arBonus['ROUND_TYPE'] = $arBonusCustom['ROUND_TYPE'];
                $arBonus['ROUND_METHOD'] = $arBonusCustom['ROUND_METHOD'];
                $arBonus['VIEW_IN_CATALOG'] = $arBonusCustom['VIEW_IN_CATALOG'];
            endforeach;
        }
        /* SOBITIE POSLE PROVERKI USLOVIYA */

        return $arBonus;
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

    public static function getBonusPrice($arItems, $orderParams){

        $arResult = [];
        $arBonus = BonusEventTable::getList();

        if (!empty($arBonus)){

            $arBonus = $arBonus->fetch();
            $arrbonuscond = unserialize($arBonus['CONDITIONS']);
            $bonusVal = 0;
            $bonusType = '';
            $round_method = '';
            $round = 0;

            if (count($arrbonuscond['children']) > 0){
                foreach ($arrbonuscond['children'] as $condition){
                    $bonusVal = (int)$condition['values']['bonus'];
                    $bonusType = $condition['values']['bonus_type'];
                    $round_method = $condition['values']['round_method'];
                    $round = (int)$condition['values']['round'];
                }
            }

            $arrPrice = [];

            if (!empty($arItems)){

                foreach ($arItems as $item){
                    $price = (int)$item['BASE_PRICE'] ;
                    if ($bonusType == "percent"){
                        $prices = (($price * $bonusVal) / 100) * $item['QUANTITY'] ;
                        $arrPrice[] = round($prices, $round);
                    }elseif ($bonusType == "bonus"){
                        $arrPrice[] = $bonusVal;
                    }
                }
            }
        }

        $allPrice = 0;
        if (!empty($arrPrice)){
            foreach ($arrPrice as $priceitem){
                $allPrice += $priceitem;
            }
        }

        $arResult['ALL_BONUS'] = $allPrice;

        return $arResult;

    }
}