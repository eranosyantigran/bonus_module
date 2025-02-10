<?php
use Bitrix\Main;
use Bitrix\Sale;
Main\Loader::includeModule("sale");
IncludeModuleLangFile(__FILE__);


class SaleOrderPaid
{
    public static function SaleOrderPaidAddBonus($order)
    {

        \Itb\Bonus\Event\BonusOrder::onOrderSave($order);

    }
}