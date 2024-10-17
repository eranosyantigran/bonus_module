<?
namespace Itb\Bonus\Event;


\Bitrix\Main\Loader::includeModule("bonus_itb");

class OrderResultPrepared {

	public static function OnSaleComponentOrderResultPrepared($order, &$arUserResult, $request, &$arParams, &$arResult)
	{
			\ITB\Bonus\Ajax\SaleOrderAjax::OrderAjaxResultPrepared($order, $arUserResult, $request, $arParams, $arResult);
	}
}

?>