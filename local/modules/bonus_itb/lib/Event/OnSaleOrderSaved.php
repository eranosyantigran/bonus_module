<?
namespace Itb\Bonus\Event;

use Itb\Bonus\Ajax\SaleOrderAjax;

class OnSaleOrderSaved {

	public static function OnSaleOrderBeforeSaved($event)
	{
			SaleOrderAjax::saleOrderBeforeSaved($event);
	}

	public static function OrderAfterSaved($event)
	{
			SaleOrderAjax::saleOrderAfterSaved($event);
	}

}

?>