<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile(__FILE__);

\Bitrix\Main\Loader::includeModule('sale');
use Bitrix\Sale\Internals\PaySystemActionTable;

$fields = array(
			"NAME" => GetMessage("ITB_PAYSYSTEM_NAME"),
			"PSA_NAME" => GetMessage("ITB_PAYSYSTEM_NAME"),
			"ACTIVE" => 'Y',
			"CODE" => 'ITB_PAYMENT_BONUS',
			"NEW_WINDOW" => 'N',
			"ALLOW_EDIT_PAYMENT" => 'Y',
			"IS_CASH" => 'Y',
			"SORT" => 10000000,
			"ENCODING" => '',
			"DESCRIPTION" => '',
			"ACTION_FILE" => 'cash',
		);
if(array_key_exists('ENTITY_REGISTRY_TYPE', \Bitrix\Sale\Internals\PaySystemActionTable::getMap()))
	$fields['ENTITY_REGISTRY_TYPE'] = \Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER;

$result = PaySystemActionTable::add($fields); 

if (!$result->isSuccess())
{
	$errorMessage .= join(',', $result->getErrorMessages());
}
else
{
	$id = $result->getId();
}
?>