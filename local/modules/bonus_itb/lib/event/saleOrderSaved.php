<?
namespace Itb\Bonus\Event;

use Bitrix\Main;

\Bitrix\Main\Loader::includeModule("bonus_itb");

class OnSaleOrderSaved {

	public static function OnSaleOrderBeforeSaved($event)
	{
			\ITB\Bonus\Ajax\SaleOrderAjax::saleOrderBeforeSaved($event);
	}

	public static function OrderAfterSaved($event)
	{
			\ITB\Bonus\Ajax\SaleOrderAjax::saleOrderAfterSaved($event);
	}

}

?>