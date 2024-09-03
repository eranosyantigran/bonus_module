<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile(__FILE__);

\Bitrix\Main\Loader::includeModule('sale');
use Bitrix\Sale\Internals\PaySystemActionTable;

CModule::IncludeModule("sale");
$paySystemBonus = CSalePaySystem::GetList(array(), array('CODE' => 'ITB_PAYMENT_BONUS'));
while($ptype = $paySystemBonus->Fetch())
{   
	CSalePaySystem::Delete($ptype["ID"]);
}
?>