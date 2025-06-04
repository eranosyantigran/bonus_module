<?
namespace Itb\Bonus\Event;

use Itb\Bonus\Ajax\SaleOrderAjax;

\Bitrix\Main\Loader::includeModule("bonus_itb");

class OrderResultPrepared {

	public static function OnSaleComponentOrderResultPrepared($order, &$arUserResult, $request, &$arParams, &$arResult)
	{
			SaleOrderAjax::OrderAjaxResultPrepared($order, $arUserResult, $request, $arParams, $arResult);
	}
}

?>