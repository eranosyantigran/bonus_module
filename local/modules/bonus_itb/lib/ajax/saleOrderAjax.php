<?php

namespace ITB\Bonus\Ajax;

use Bitrix\Sale\PriceMaths;
use Bitrix\Sale\Discount;
use Bitrix\Sale\DiscountBase;
use Bitrix\Sale\DiscountCouponsManager;
\Bitrix\Main\Loader::includeModule("bonus_itb");

class SaleOrderAjax {

    public static function OrderAjaxResultPrepared($order, &$arUserResult, $request, &$arParams, &$arResult){

        $basket = $order->getBasket();

        if(!empty($_POST))
            $post = 'Y';

        $cartSum = 0;
        $arItems = array();
        foreach($basket as $basketItem):
            $arItem = array();
            $arItem["PRODUCT_ID"] = $basketItem->getProductId();
            $arItem["BASKET_ID"] = $basketItem->getId();
            $arItem["NAME"] = $basketItem->getField('NAME');
            $arItem["QUANTITY"] = $basketItem->getQuantity();
            $arItem["BASE_PRICE"] = $basketItem->getField('BASE_PRICE');
            $arItem["PRICE"] = $basketItem->getPrice();
            $arItem["DISCOUNT_PRICE"] = $basketItem->getField('DISCOUNT_PRICE');
            $arItem["PRICE_POSITION"] = $basketItem->getFinalPrice();

            if(isset($resultDiscounts["PRICES"]["BASKET"][$arItem["BASKET_ID"]]))
            {
                $arItem["BASE_PRICE"] = $resultDiscounts["PRICES"]["BASKET"][$arItem["BASKET_ID"]]["BASE_PRICE"];
                $arItem["PRICE"] = $resultDiscounts["PRICES"]["BASKET"][$arItem["BASKET_ID"]]["PRICE"];
                $arItem["DISCOUNT_PRICE"] = $resultDiscounts["PRICES"]["BASKET"][$arItem["BASKET_ID"]]["DISCOUNT"];
                $arItem["PRICE_POSITION"] = $arItem["QUANTITY"] * $arItem["PRICE"];
            }
            $arItems[$arItem["BASKET_ID"]] = $arItem;
            $cartSum = $cartSum + $arItem["PRICE_POSITION"];
        endforeach;


        //Payments
        $paymentCollection = $order->getPaymentCollection();
        $arPayments = array();
        foreach($paymentCollection as $payment):
            $paymentId = $payment->getPaymentSystemId();
            $arPayments[$paymentId] = array("ID"=>$paymentId, "SUM"=>$payment->getSum(), "NAME"=>$payment->getPaymentSystemName(), "IS_PAYED"=>$payment->isPaid(), "IS_INNER"=>$payment->isInner());

            if($payment->isInner() == true)
                $paySumFromInner = $payment->getSum();
        endforeach;

        //Delivery
        $deliveryCollection = $order->getDeliverySystemId();
        $arDelivery = array();
        foreach($deliveryCollection as $delivery):
            $arDelivery[$delivery] = array("ID"=>$delivery);
        endforeach;


        //Polya zakaza
        $arOrderParams = array(
            "USER_ID" => $order->getUserId(),
            "ORDER_SUM" => $cartSum+$order->getDeliveryPrice(),
            "CART_SUM" => $cartSum,
            "DELIVERY_SUM" => $order->getDeliveryPrice(),
            "PERSON_TYPE_ID" => $order->getPersonTypeId(),
            "CURRENCY" => $order->getCurrency(),
            "PAYMENTS" => $arPayments,
            "DELIVERY" => $arDelivery,
        );

        global $USER;
        if($USER->IsAuthorized())
            $UserBallance = \Itb\Bonus\ItbHelpers::UserBallance($arOrderParams["USER_ID"]);
        else
            $UserBallance = 0;


        $props = $order->getPropertyCollection();
        foreach($props as $prop) {
            $fields = $prop->GetFields();
            $values = $fields->GetValues();
            if($values["CODE"] == 'ITB_PAYMENT_BONUS')
            {
                $payment_prop_id = $values["ORDER_PROPS_ID"];
                $input_bonus = str_replace(',', '.', $values["VALUE"]);
                if(!is_numeric($input_bonus))
                    $input_bonus = 0;
            }
            if($values["CODE"] == 'ITB_ADD_BONUS')
                $addBpnus_prop_id = $values["ORDER_PROPS_ID"];
        }


        if ($USER->IsAuthorized()):

            if($post == 'Y')
            {
                $pay_bonus = $input_bonus;
                if(!is_numeric($pay_bonus))
                    $pay_bonus = 0;
            }
            else
            {
                $pay_bonus = 'MAX';
            }

            $arOrderParams["PAY_BONUS"] = $pay_bonus;
            $arPayBonus = \Itb\Bonus\CalculateBonus::OrderBonusPayment($arItems, $arOrderParams);
            $minBonusSum = $arPayBonus["MIN_ORDER_PAY"];
            $maxBonusSum = $arPayBonus["MAX_ORDER_PAY"];
            $pay_bonus = $arOrderParams["PAY_BONUS"] = $arPayBonus["PAY_BONUS"];
            $bonusPayCart = $arPayBonus["PAY_CART"];
            $bonusPayDelivery = $arPayBonus["PAY_DELIVERY"];
            $newDeliveryPrice = $arPayBonus["NEW_DELIVERY_PRICE"];


            $PayBonusToDiscount = "Y";
            if($PayBonusToDiscount == 'B' && $pay_bonus >= 0 || $PayBonusToDiscount == 'Y' && $pay_bonus >= 0):

                if($bonusPayCart >= 0):

                    foreach($basket as $basketItem):
                        $item = $basketItem->getFields();
                        $arBasketItem = $item->getValues();
                        $arItem = $arItems[$arBasketItem["ID"]];

                        //Esli tovar nel'zya oplatit' bonusami
                        if($arPayBonus["PAY_PRODUCTS"]["ITEMS"][$arItem["BASKET_ID"]]["ADD_BONUS"] > 0 || $arPayBonus["PAY_PRODUCTS"]["PROFILE"]["NO_PRODUCT_CONDITIONS"] == 'Y')
                        {
                            $canPayProduct = $arPayBonus["PAY_PRODUCTS"]["ITEMS"][$arItem["BASKET_ID"]]["ADD_BONUS"];
                            $canPayProductUnit = $arPayBonus["PAY_PRODUCTS"]["ITEMS"][$arItem["BASKET_ID"]]["ADD_BONUS_UNIT"];
                            $canPayQuantity = $arPayBonus["PAY_PRODUCTS"]["ITEMS"][$arItem["BASKET_ID"]]["QUANTITY"];
                        }
                        else
                            continue;

                        //Procentnoe sootnoshenie pozicii tovara s obshhej summoj
                        if($arPayBonus["PAY_PRODUCTS"]["PROFILE"]["NO_PRODUCT_CONDITIONS"] == 'Y') //Esli net usloviy po tovaram, no est' ogranichenie po summe oplati zakaza
                            $productPart = $arItem["PRICE"] * $arItem["QUANTITY"] * 100 / $arPayBonus["PAY_PRODUCTS_SUM"];
                        else
                            $productPart = $canPayProductUnit * $canPayQuantity * 100 / $arPayBonus["PAY_PRODUCTS"]["ALL_BONUS"];

                        $discountPlusPosition = $bonusPayCart * $productPart / 100; //Skol'ko rublej nado pripljusovat' k skidke pozicii tovara
                        $discountPlusUnit = $discountPlusPosition / $arItem["QUANTITY"];
                        $newPrice = $arItem["PRICE"] - $discountPlusUnit; //Cena s uchetom raskidanooj skidki
                        $newPriceRound = \Bitrix\Catalog\Product\Price::roundPrice($arBasketItem["PRICE_TYPE_ID"], $newPrice, $arBasketItem["CURRENCY"]);
                        $payBonusUnit = $arItem["PRICE"] - $newPriceRound;
                        $payBonusPosition = $payBonusUnit * $arItem["QUANTITY"];
                        $newDiscount = $arItem["BASE_PRICE"] - $newPriceRound; //Skidka s uchetom dobavlennoj novoj skidki

                        //zapisivaem novie ceni v nash massiv
                        $arItems[$arItem["BASKET_ID"]]["PRICE"] = $newPriceRound;
                        $arItems[$arItem["BASKET_ID"]]["PRICE_FORMAT"] = SaleFormatCurrency($newPriceRound, $arBasketItem["CURRENCY"]);
                        $arItems[$arItem["BASKET_ID"]]["PRICE_POSITION"] = $newPriceRound * $arItem["QUANTITY"];
                        $arItems[$arItem["BASKET_ID"]]["PRICE_POSITION_FORMAT"] = SaleFormatCurrency($arItems[$arItem["BASKET_ID"]]["PRICE_POSITION"], $arBasketItem["CURRENCY"]);
                        $arItems[$arItem["BASKET_ID"]]["BASE_PRICE"] = $arItem["BASE_PRICE"];
                        $arItems[$arItem["BASKET_ID"]]["BASE_PRICE_FORMAT"] = SaleFormatCurrency($arItem["BASE_PRICE"], $arBasketItem["CURRENCY"]);
                        $arItems[$arItem["BASKET_ID"]]["DISCOUNT_PRICE"] = $newDiscount;

                        $arItems[$arItem["BASKET_ID"]]["BITRIX_DISCOUNT_PRICE"] = $arItem["PRICE"];
                        $arItems[$arItem["BASKET_ID"]]["PAY_BONUS_QUANTITY"] = $payBonusUnit;
                        $arItems[$arItem["BASKET_ID"]]["PAY_BONUS_POSITION"] = $payBonusPosition;

                        //zapisivaem novie ceni arresult komponenta
                        if($PayBonusToDiscount == 'B')
                        {
                            $arResult["JS_DATA"]["GRID"]["ROWS"][$arItem["BASKET_ID"]]["data"]["PRICE_FORMATED"] = $arItems[$arItem["BASKET_ID"]]["PRICE_FORMAT"];
                            $arResult["JS_DATA"]["GRID"]["ROWS"][$arItem["BASKET_ID"]]["data"]["PRICE"] = $arItems[$arItem["BASKET_ID"]]["PRICE"];

                            $arResult["JS_DATA"]["GRID"]["ROWS"][$arItem["BASKET_ID"]]["data"]["SUM_NUM"] = $arItems[$arItem["BASKET_ID"]]["PRICE_POSITION"];
                            $arResult["JS_DATA"]["GRID"]["ROWS"][$arItem["BASKET_ID"]]["data"]["SUM"] = $arItems[$arItem["BASKET_ID"]]["PRICE_POSITION_FORMAT"];

                            $arResult["JS_DATA"]["GRID"]["ROWS"][$arItem["BASKET_ID"]]["data"]["DISCOUNT_PRICE"] = $arItems[$arItem["BASKET_ID"]]["DISCOUNT_PRICE"];
                            $arResult["JS_DATA"]["GRID"]["ROWS"][$arItem["BASKET_ID"]]["data"]["SUM_DISCOUNT_DIFF"] = $arItems[$arItem["BASKET_ID"]]["DISCOUNT_PRICE"]*$arItems[$arItem["BASKET_ID"]]["QUANTITY"];
                            $arResult["JS_DATA"]["GRID"]["ROWS"][$arItem["BASKET_ID"]]["data"]["SUM_DISCOUNT_DIFF_FORMATED"] = SaleFormatCurrency($arResult["JS_DATA"]["GRID"]["ROWS"][$arItem["BASKET_ID"]]["data"]["SUM_DISCOUNT_DIFF"], $arBasketItem["CURRENCY"]);
                        }
                    endforeach;
                endif;

                $arResult["JS_DATA"]["ITB_BONUS"]["ARR_PAY_BONUS"]["ITEMS"] = $arItems;


                //New basket and order sum
                $currency = $order->getCurrency();
                $newCartSum = $newOrderSum = 0;
                foreach($arItems as $arItem):
                    $newCartSum += $arItem["PRICE_POSITION"];
                endforeach;
                $arOrderParams['CART_SUM'] = $newCartSum;

                /* if pay from bill */
                if($paySumFromInner > 0)
                {
                    $arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_LEFT_TO_PAY_FORMATED'] = \SaleFormatCurrency($arOrderParams['ORDER_SUM'] - $paySumFromInner - $pay_bonus, $currency);

                    if($paySumFromInner > $arOrderParams['ORDER_SUM'] - $pay_bonus)
                    {
                        $newPaySumFromInner = $arOrderParams['ORDER_SUM'] - $pay_bonus;
                        $arResult['JS_DATA']['TOTAL']['PAYED_FROM_ACCOUNT_FORMATED'] = \SaleFormatCurrency($newPaySumFromInner, $currency);
                        $arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_LEFT_TO_PAY_FORMATED'] = \SaleFormatCurrency(0, $currency);
                    }
                }
                /* if pay from bill */

                if($bonusPayDelivery > 0)
                {
                    $newDeliveryPrice = $newDeliveryPrice;
                    $oldDeliveryPrice = $arResult["JS_DATA"]["TOTAL"]["DELIVERY_PRICE"];
                }
                else
                {
                    $newDeliveryPrice = $arResult["JS_DATA"]["TOTAL"]["DELIVERY_PRICE"];
                    $oldDeliveryPrice = $arResult["JS_DATA"]["TOTAL"]["DELIVERY_PRICE"];
                }
                $newOrderSum = $newCartSum + $newDeliveryPrice;
                $arOrderParams['ORDER_SUM'] = $newOrderSum;

                if($PayBonusToDiscount == 'B')
                {
                    $arResult["JS_DATA"]["TOTAL"]["ORDER_PRICE"] = $newCartSum;
                    $arResult["JS_DATA"]["TOTAL"]["ORDER_PRICE_FORMATED"] = \SaleFormatCurrency($newCartSum, $currency);
                    $arResult["JS_DATA"]["TOTAL"]["ORDER_TOTAL_PRICE"] = $newOrderSum;
                    $arResult["JS_DATA"]["TOTAL"]["ORDER_TOTAL_PRICE_FORMATED"] = \SaleFormatCurrency($newOrderSum, $currency);

                    $arResult["JS_DATA"]["TOTAL"]["DISCOUNT_PRICE"] = $arResult["JS_DATA"]["TOTAL"]["PRICE_WITHOUT_DISCOUNT_VALUE"] + $oldDeliveryPrice - $newOrderSum;
                    $arResult["JS_DATA"]["TOTAL"]["DISCOUNT_PRICE_FORMATED"] = \SaleFormatCurrency($arResult["JS_DATA"]["TOTAL"]["DISCOUNT_PRICE"], $currency);

                    //Esli bonusami oplacheno bol'she, chem stoimost korzini, to vichitaem ih iz dostavki
                    if($bonusPayDelivery > 0):
                        foreach($arDelivery as $shipment):
                            $arResult["JS_DATA"]["TOTAL"]["DELIVERY_PRICE"] = $newDeliveryPrice;
                            $arResult["JS_DATA"]["TOTAL"]["DELIVERY_PRICE_FORMATED"] = \SaleFormatCurrency($newDeliveryPrice, $currency);
                            $arResult["JS_DATA"]["DELIVERY"][$shipment["ID"]]["DELIVERY_DISCOUNT_PRICE"] = $newDeliveryPrice;
                            $arResult["JS_DATA"]["DELIVERY"][$shipment["ID"]]["DELIVERY_DISCOUNT_PRICE_FORMATED"] = \SaleFormatCurrency($newDeliveryPrice, $currency);
                        endforeach;
                    endif;

                }
                if($PayBonusToDiscount == 'Y')
                {
                    $arResult["JS_DATA"]["TOTAL"]["ORDER_TOTAL_PRICE"] = $newOrderSum;
                    $arResult["JS_DATA"]["TOTAL"]["ORDER_TOTAL_PRICE_FORMATED"] = \SaleFormatCurrency($newOrderSum, $currency);
                }

            endif;


            if($PayBonusToDiscount == 'N' && $pay_bonus > 0)
            {
                $order_new_sum = $arOrderParams["ORDER_SUM"] - $pay_bonus;
                $arResult["JS_DATA"]["TOTAL"]["ORDER_TOTAL_PRICE"] = $order_new_sum;
                $arResult["JS_DATA"]["TOTAL"]["ORDER_TOTAL_PRICE_FORMATED"] = $arResult["ORDER_TOTAL_PRICE_FORMATED"] = \SaleFormatCurrency($order_new_sum, $arResult['BASE_LANG_CURRENCY']);

                /* if pay from bill */
                if($paySumFromInner > 0)
                {
                    if($paySumFromInner > $arOrderParams['ORDER_SUM'] - $pay_bonus)
                    {
                        $newPaySumFromInner = $arOrderParams['ORDER_SUM'] - $pay_bonus;

                        $arResult['JS_DATA']['TOTAL']['PAYED_FROM_ACCOUNT_FORMATED'] = $arResult["PAYED_FROM_ACCOUNT_FORMATED"] = \SaleFormatCurrency($newPaySumFromInner, $arResult['BASE_LANG_CURRENCY']);
                        $arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_LEFT_TO_PAY_FORMATED'] = $arResult["ORDER_TOTAL_LEFT_TO_PAY_FORMATED"] = \SaleFormatCurrency(0, $arResult['BASE_LANG_CURRENCY']);
                    }
                    else
                        $arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_LEFT_TO_PAY_FORMATED'] = $arResult["ORDER_TOTAL_LEFT_TO_PAY_FORMATED"] = \SaleFormatCurrency($arOrderParams['ORDER_SUM'] - $paySumFromInner - $pay_bonus, $arResult['BASE_LANG_CURRENCY']);
                }
                /* if pay from bill */
            }
        endif;


        $UserBonusSystemDostup = 'Y';
        $arResult["MIN_BONUS"] = $arResult["JS_DATA"]["ITB_BONUS"]["MIN_BONUS"] = $minBonusSum;
        $arResult["MAX_BONUS"] = $arResult["JS_DATA"]["ITB_BONUS"]["MAX_BONUS"] = $maxBonusSum;
        $arResult["USER_BONUS"] = $arResult["JS_DATA"]["ITB_BONUS"]["USER_BONUS"] = $UserBallance;
        $arResult["ITB_BONUS_USER_DOSTUP"] = $arResult["JS_DATA"]["ITB_BONUS"]["ITB_BONUS_USER_DOSTUP"] = $UserBonusSystemDostup;

        $arResult["JS_DATA"]["ITB_BONUS"]["INPUT_BONUS"] = $input_bonus;
        $arResult["PAY_BONUS"] = $arResult["JS_DATA"]["ITB_BONUS"]["PAY_BONUS"] = $pay_bonus;
        $arResult["PAY_BONUS_FORMATED"] = $arResult["JS_DATA"]["ITB_BONUS"]["PAY_BONUS_FORMATED"] = \SaleFormatCurrency($pay_bonus, $arOrderParams['CURRENCY']);
        $arResult["JS_DATA"]["ITB_BONUS"]["PAY_BONUS_NO_POST"] = $pay_bonus; //OLD
        $arResult["JS_DATA"]["ITB_BONUS"]["PAY_BONUS_NO_POST_FORMATED"] = SaleFormatCurrency($pay_bonus, $arOrderParams['CURRENCY']); //OLD

        $arResult["JS_DATA"]["ITB_BONUS"]["ORDER_SUM"] = $arOrderParams["ORDER_SUM"];
        $arResult["JS_DATA"]["ITB_BONUS"]["ORDER_SUM_FORMATED"] = SaleFormatCurrency($arOrderParams["ORDER_SUM"], $arOrderParams['CURRENCY']);

        $arResult["ARR_BONUS"] = $arResult["JS_DATA"]["ITB_BONUS"]["ARR_BONUS"] = $arBonus;
        $arResult["ADD_BONUS"] = $arResult["JS_DATA"]["ITB_BONUS"]["ADD_BONUS"] = (string)$arBonus["ALL_BONUS"];

        $formatAll = \COption::GetOptionString("bonus_itb", "TEMPLATE_BONUS_FOR_ORDER".$langSufix, '');
        if(!$formatAll || $formatAll == '')
            $formatAll = '#BONUS#';
        $arResult["ADD_BONUS_FORMAT"] = $arResult["JS_DATA"]["ITB_BONUS"]["ADD_BONUS_FORMAT"] = str_replace('#BONUS#', (string)$arBonus["ALL_BONUS"], $formatAll);


        $arResult["ORDER_PROP_PAYMENT_BONUS_ID"] = $arResult["JS_DATA"]["ITB_BONUS"]["ORDER_PROP_PAYMENT_BONUS_ID"] = $payment_prop_id;
        $arResult["ORDER_PROP_ADD_BONUS_ID"] = $arResult["JS_DATA"]["ITB_BONUS"]["ORDER_PROP_ADD_BONUS_ID"] = $addBpnus_prop_id;

        $arResult["JS_DATA"]["ITB_BONUS"]["DISCOUNT_TO_PRODUCTS"] = \COption::GetOptionString("bonus_itb", "DISCOUNT_TO_PRODUCTS", 'N');

        $arResult["JS_DATA"]["ITB_BONUS"]["ORDER_PAY_BONUS_AUTO"] = \COption::GetOptionString("bonus_itb", "ORDER_PAY_BONUS_AUTO", 'Y');

        //ADD_TEXT
        $arResult["JS_DATA"]["ITB_BONUS"]["TEXT_BONUS_BALLS"] = \COption::GetOptionString("bonus_itb", "TEXT_BONUS_BALLS".$langSufix, 'bonus:');
        $arResult["JS_DATA"]["ITB_BONUS"]["TEXT_BONUS_PAY"] = \COption::GetOptionString("bonus_itb", "TEXT_BONUS_PAY".$langSufix, 'pay from bonus:');
        $arResult["JS_DATA"]["ITB_BONUS"]["TEXT_BONUS_FOR_ITEM"] = \COption::GetOptionString("bonus_itb", "TEXT_BONUS_FOR_ITEM".$langSufix, 'pay from bonus:');

        $payCooment = '';
        if(strpos(\COption::GetOptionString("bonus_itb", "MIN_BONUS_TEXT".$langSufix, ''), '#BONUS#') !== false || strpos(\COption::GetOptionString("bonus_itb", "MAX_BONUS_TEXT".$langSufix, ''), '#BONUS#') !== false)
        {
            $payCooment .= \COption::GetOptionString("bonus_itb", "CAN_BONUS_TEXT".$langSufix, 'Can use bonus');
            if($minBonusSum > 0)
                $payCooment .= ' '.\Itb\Bonus\ItbHelpers::FormatBonusString(\COption::GetOptionString("bonus_itb", "MIN_BONUS_TEXT".$langSufix, 'Min use bonus'), '#BONUS#', $minBonusSum);
            if($maxBonusSum > 0 && $maxBonusSum >= $minBonusSum)
                $payCooment .= ' '.\Itb\Bonus\ItbHelpers::FormatBonusString(\COption::GetOptionString("bonus_itb", "MAX_BONUS_TEXT".$langSufix, 'Max use bonus'), '#BONUS#', $maxBonusSum);
        }
        else
        {
            if($minBonusSum > 0)
                $payCooment .= '<span>'.\Itb\Bonus\ItbHelpers::FormatBonusString(\COption::GetOptionString("bonus_itb", "MIN_BONUS_TEXT".$langSufix, 'Min use bonus'), '#BONUS#', $minBonusSum).'</span>';
            if($maxBonusSum > 0 && $maxBonusSum >= $minBonusSum)
                $payCooment .= '<span>'.\Itb\Bonus\ItbHelpers::FormatBonusString(\COption::GetOptionString("bonus_itb", "MAX_BONUS_TEXT".$langSufix, 'Max use bonus'), '#BONUS#', $maxBonusSum).'</span>';
        }
        $errorMinBonusComment = '';
        if(\COption::GetOptionString("bonus_itb", "TEXT_BONUS_ERROR_MIN_BONUS".$langSufix, '') != '')
        {
            if(strpos(\COption::GetOptionString("bonus_itb", "TEXT_BONUS_ERROR_MIN_BONUS".$langSufix, ''), '#BONUS#') !== false)
                $errorMinBonusComment .= \Itb\Bonus\ItbHelpers::FormatBonusString(\COption::GetOptionString("bonus_itb", "TEXT_BONUS_ERROR_MIN_BONUS".$langSufix, ''), '#BONUS#', $minBonusSum);
        }
        else
            $errorMinBonusComment .= $payCooment;

        $paymentComment = \COption::GetOptionString("bonus_itb", "TEXT_BONUS_PAYMENT_COMMENT".$langSufix, '');
        if(\COption::GetOptionString("bonus_itb", "TEXT_BONUS_PAYMENT_COMMENT".$langSufix, '') != '')
        {
            if(strpos(\COption::GetOptionString("bonus_itb", "TEXT_BONUS_PAYMENT_COMMENT".$langSufix, ''), '#BONUS#') !== false)
                $paymentComment = str_replace('#BONUS#', $arResult["PAY_BONUS"], \COption::GetOptionString("bonus_itb", "TEXT_BONUS_PAYMENT_COMMENT".$langSufix, ''));
        }

        $maxBonusText = \COption::GetOptionString("bonus_itb", "MAX_BONUS_TEXT".$langSufix, '');
        if($maxBonusText != '')
        {
            if(strpos($maxBonusText, '#BONUS#') !== false)
                $maxBonusText = str_replace('#BONUS#', $arResult["MAX_BONUS"], $maxBonusText);
        }
        $minBonusText = \COption::GetOptionString("bonus_itb", "MIN_BONUS_TEXT".$langSufix, '');
        if($minBonusText != '')
        {
            if(strpos($minBonusText, '#BONUS#') !== false)
                $minBonusText = str_replace('#BONUS#', $arResult["MIN_BONUS"], $minBonusText);
        }

        $arResult["JS_DATA"]["ITB_BONUS"]["MODULE_LANG"] = array(
            "HAVE_BONUS_TEXT" => \COption::GetOptionString("bonus_itb", "HAVE_BONUS_TEXT".$langSufix, 'Have bonus'),
            "HAVE_BONUS_TEXT_FORMAT" => \Itb\Bonus\ItbHelpers::FormatBonusString(\COption::GetOptionString("bonus_itb", "HAVE_BONUS_TEXT".$langSufix, 'Have bonus'), '#BONUS#', $UserBallance),
            "CAN_USE_BONUS_TEXT" => \COption::GetOptionString("bonus_itb", "CAN_BONUS_TEXT".$langSufix, 'Can use bonus'),
            "CAN_USE_BONUS_TEXT_FORMAT" => $payCooment,
            "MIN_BONUS_TEXT" => $minBonusText,
            "MAX_BONUS_TEXT" => $maxBonusText,
            "PAY_BONUS_TEXT" => \COption::GetOptionString("bonus_itb", "PAY_BONUS_TEXT".$langSufix, 'Pay from bonus'),
            "TEXT_BONUS_FOR_PAYMENT" => \COption::GetOptionString("bonus_itb", "TEXT_BONUS_FOR_PAYMENT".$langSufix, 'Pay from bonus'),
            "TEXT_BONUS_FOR_PAYMENT" => \COption::GetOptionString("bonus_itb", "TEXT_BONUS_FOR_PAYMENT".$langSufix, 'Pay from bonus'),
            "TEXT_BONUS_USE_BONUS_BUTTON" => \COption::GetOptionString("bonus_itb", "TEXT_BONUS_USE_BONUS_BUTTON".$langSufix, 'Use'),
            "TEXT_BONUS_ERROR_MIN_BONUS_FORMAT" => $errorMinBonusComment,
            "TEXT_BONUS_PAYMENT_COMMENT" => $paymentComment,
        );


        //Udalyaem platejnie sistemi bonusov iz shablona
        foreach($arResult["JS_DATA"]["PAY_SYSTEM"] as $keyPaysystem => $paySystem):
            if($paySystem["CODE"] == 'LOGICTIM_PAYMENT_BONUS')
                unset($arResult["JS_DATA"]["PAY_SYSTEM"][$keyPaysystem]);
        endforeach;
        $arResult["JS_DATA"]["PAY_SYSTEM"] = array_values($arResult["JS_DATA"]["PAY_SYSTEM"]);
        foreach($arResult["PAY_SYSTEM"] as $keyPaysystem => $paySystem):
            if($paySystem["CODE"] == 'LOGICTIM_PAYMENT_BONUS')
                unset($arResult["PAY_SYSTEM"][$keyPaysystem]);
        endforeach;
        $arResult["PAY_SYSTEM"] = array_values($arResult["PAY_SYSTEM"]);

        global $APPLICATION;
        if(\COption::GetOptionString("bonus_itb", "INTEGRATE_IN_SALE_ORDER_AJAX", 'N') == 'Y')
        {
            $APPLICATION->AddHeadScript('/bitrix/js/bonus_itb/sale_order_ajax.js');
            $APPLICATION->SetAdditionalCSS("/bitrix/js/bonus_itb/sale_order_ajax.css");
            //CJSCore::Init(array("jquery2"));
        }


    }


    public static function saleOrderSaved($order){

        global $USER;

        $user = new \CUser;
        $inofOrder = $order->toArray();
        $paymentCollection = $order->getPaymentCollection();
        $propertyCollection = $order->getPropertyCollection();
        $paysystemName = $paymentCollection->toArray()[0]['PAY_SYSTEM_NAME'];
        $paysystemId = $paymentCollection->getOrder()->getFields()->getValues()['PAY_SYSTEM_ID'];
        $payAllSum = $paymentCollection->getOrder()->getFields()->getValues()['PRICE'];
        $bonus_val = 0;
        $properties = $propertyCollection->getArray()['properties'];

        if($USER->IsAuthorized())
            $UserBallance = \Itb\Bonus\ItbHelpers::UserBallance($inofOrder["USER_ID"]);
        else
            $UserBallance = 0;

        foreach ($properties as $prop){
            if ($prop['CODE'] == 'ITB_PAYMENT_BONUS'){
                $bonus_val =  $prop['VALUE'][0];
            }
        }

        if ($paysystemName == 'Бонусный счет'){

            if ($inofOrder['PRICE'] < (int)$bonus_val && $UserBallance > 0 ){

                $bonus_after = (int)$bonus_val - $inofOrder['PRICE'];

                $newBalance = $UserBallance - $inofOrder['PRICE'] + $bonus_after;

                $fields = [
                    "UF_BONUS_COUNT" => $newBalance,
                ];

                if ($order->getId() === 0) {
                    $result = $user->Update($inofOrder["USER_ID"], $fields);
                    if ($paymentCollection->count() > 0) {
                        foreach ($paymentCollection as $payment) {
                            $payment->setField('PAID', 'Y');
                        }
                    }
                }

            }
        }
    }


}