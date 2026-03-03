<?
$showRightsTab = true;

//Select discounts
$arDiscounts = array();
$discountIterator = \Bitrix\Sale\Internals\DiscountTable::getList(array('filter' => array()));
$arDiscounts["REFERENCE_ID"][] = 0;
$arDiscounts["REFERENCE"][] = GetMessage("ITB_REFERAL_COUPON_DISCOUNT_NO");
while($discount = $discountIterator->fetch())
{
	$arDiscounts["REFERENCE_ID"][] = $discount["ID"];
	$arDiscounts["REFERENCE"][] = $discount["NAME"];
}


//Select currencies
//$arCurrency = array("REFERENCE_ID" => array('0'), "REFERENCE" => array('-'));
if(CModule::IncludeModule("sale"))
{
	$lcur = CCurrency::GetList(($by="sort"), ($order="asc"));
	while($lcur_res = $lcur->Fetch())
	{
		$arCurrency["REFERENCE_ID"][] = $lcur_res["CURRENCY"];
		$arCurrency["REFERENCE"][] = $lcur_res["FULL_NAME"];
		if($lcur_res["BASE"] == 'Y')
			$defaultCurrency = $lcur_res["CURRENCY"];
	}

}

//select order statuses
$arStatuses = array();
$arStatuses["REFERENCE_ID"][] = '';
$arStatuses["REFERENCE"][] = GetMessage("ITB_EVENT_ORDER_STATUS_NO");
$dbStatuses = \Bitrix\Sale\Internals\StatusLangTable::getList(array('order' => array('STATUS.SORT'=>'ASC'), 'filter' => array('STATUS.TYPE'=>'O', 'LID'=>LANGUAGE_ID)));
while($arStatus = $dbStatuses->fetch())
{
	$arStatuses["REFERENCE_ID"][] = $arStatus["STATUS_ID"];
	$arStatuses["REFERENCE"][] = '['.$arStatus["STATUS_ID"].'] '.$arStatus["NAME"];
}

//select  all user groups
$userGrups = array();
$rsGroups = CGroup::GetList(($by="id"), ($order="asc"), array("ACTIVE"  => "Y"));
while($arUserGroups = $rsGroups->Fetch()) {
	$userGrups["REFERENCE_ID"][] = $arUserGroups["ID"];
	$userGrups["REFERENCE"][] = $arUserGroups["NAME"];
}

$arTabs = array(
   array(
      'DIV' => 'edit1',
      'TAB' => GetMessage("ITB_MAIN_OPTIONS_TAB"),
      'ICON' => '',
      'TITLE' => GetMessage("ITB_MAIN_OPTIONS_TAB")
   ),
   array(
      'DIV' => 'edit4',
      'TAB' => GetMessage("ITB_REFERAL_OPTIONS_TAB"),
      'ICON' => '',
      'TITLE' => GetMessage("ITB_REFERAL_OPTIONS_TAB"),
   ),
   array(
      'DIV' => 'edit3',
      'TAB' => GetMessage("ITB_TAB_3"),
      'ICON' => '',
      'TITLE' => GetMessage("ITB_TAB_3"),
   ),
    array(
      'DIV' => 'edit5',
      'TAB' => GetMessage("ITB_TAB_5"),
      'ICON' => '',
      'TITLE' => GetMessage("ITB_TAB_5"),
   ),
	 array(
      'DIV' => 'edit6',
      'TAB' => GetMessage("ITB_TAB_6"),
      'ICON' => '',
      'TITLE' => GetMessage("ITB_TAB_6"),
   ),
   array(
      'DIV' => 'edit_access_tab',
      'TAB' => GetMessage("ITB_TAB_ACCESS"),
      'ICON' => '',
      'TITLE' => GetMessage("ITB_TAB_ACCESS"),
   ),
);

$arGroups = array(
   'MAIN' => array('TITLE' => GetMessage("ITB_OPTIONS_MAIN"), 'TAB' => 0),
   'EVENTS_ORDER_TO_BONUS' => array('TITLE' => GetMessage("ITB_OPTIONS_EVENTS_ORDER"), 'TAB' => 0),
   'REFERAL_SYSTEM' => array('TITLE' => GetMessage("ITB_REFERAL_OPTIONS_SECTION"), 'TAB' => 1),
   'EVENTS_MAIL' => array('TITLE' => GetMessage("ITB_EVENTS_MAIL_GROUP"), 'TAB' => 2),
   'ORDER_FORM' => array('TITLE' => GetMessage("ITB_OPTIONS_ORDER_FORM"), 'TAB' => 3),
   'BASKET_INTAGRATE' => array('TITLE' => GetMessage("ITB_INTEGRATE_IN_SALE_BASKET"), 'TAB' => 3),
   'CATALOG_INTAGRATE' => array('TITLE' => GetMessage("ITB_INTEGRATE_IN_CATALOG"), 'TAB' => 3),
   'TEXT' => array('TITLE' => GetMessage("ITB_OPTIONS_TEXT"), 'TAB' => 3),
);

$arOptions = array(
	'MODULE_VERSION' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage("ITB_MODULE_VERSION"),
      'TYPE' => 'SELECT',
      'VALUES' => array('REFERENCE_ID' => array('3', '4'), 'REFERENCE' => array(GetMessage("ITB_MODULE_VERSION_3"), GetMessage("ITB_MODULE_VERSION_4"))),
	  'DEFAULT' => '4',
      'SORT' => '0',
	  'CLASS' => '',
	  'NOTES' => GetMessage("ITB_VERSION_CHANGE_NOTES")
   ),
    'BONUS_BILL_DESCRIPTION' => array(
      'GROUP' => 'MAIN',
      'TYPE' => 'CUSTOM',
      'SORT' => '0',
	  'NOTES' => GetMessage("ITB_BONUS_BILL_DESCRIPTION"),
   ),
   'BONUS_BILL' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage("ITB_SELECT_BONUS_BILL"),
      'TYPE' => 'SELECT',
      'VALUES' => array('REFERENCE_ID' => array(1, 2), 'REFERENCE' => array(GetMessage("ITB_BILL_BONUS"), GetMessage("ITB_BILL_BITRIX"),)),
	  'DEFAULT' => '1',
      'SORT' => '1',
	  'CLASS' => 'LGB_PARENT_SELECT'
   ),
   'BONUS_CURRENCY' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage("ITB_SELECT_BONUS_CURRENCY"),
      'TYPE' => 'SELECT',
      'VALUES' => $arCurrency,
	  'DEFAULT' => $defaultCurrency,
      'SORT' => '2',
	  'CLASS' => 'BONUS_BILL BONUS_BILL_2'
   ),
   'DISCOUNT_TO_PRODUCTS' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage("ITB_DISCOUNT_METOD"),
      'TYPE' => 'SELECT',
	  'DEFAULT' => 'Y',
	  'VALUES' => array(
					'REFERENCE_ID' => array('Y', 'B', 'N'), 
					'REFERENCE' => array(
										GetMessage("ITB_DISCOUNT_METOD_DISCOUNT"),
										GetMessage("ITB_DISCOUNT_METOD_MOMENT_DISCOUNT"),
										GetMessage("ITB_DISCOUNT_METOD_PAYMENT"),
										),
						),
      'SORT' => '10',
   ),


   
   'ORDER_STATUS' => array(
      'GROUP' => 'EVENTS_ORDER_TO_BONUS',
      'TITLE' => GetMessage("ITB_EVENT_ORDER_STATUS"),
      'TYPE' => 'SELECT',
      'VALUES' => $arStatuses,
	  'DEFAULT' => 'F',
      'SORT' => '1',
   ),
   'EVENT_ORDER_PAYED' => array(
      'GROUP' => 'EVENTS_ORDER_TO_BONUS',
      'TITLE' => GetMessage("ITB_EVENT_ORDER_PAYED"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'Y',
      'SORT' => '2'
   ),
   'USER_REGISTER' => array(
      'GROUP' => 'EVENTS_ORDER_TO_BONUS',
      'TITLE' => GetMessage("ITB_EVENT_USER_REGISTER_TITLE"),
      'TYPE' => 'SELECT',
      'VALUES' => array("REFERENCE_ID" => array('ADD', 'REGISTER'), "REFERENCE" => array(GetMessage("ITB_EVENT_USER_REGISTER_ADD"), GetMessage("ITB_EVENT_USER_REGISTER_REGISTER"))),
	  'DEFAULT' => 'ADD',
      'SORT' => '1',
   ),
   
   
   'REFERAL_LEVELS' => array(
      'GROUP' => 'REFERAL_SYSTEM',
      'TITLE' => GetMessage("ITB_REFERAL_LEVELS"),
      'TYPE' => 'INT',
	  'DEFAULT' => '1',
      'SORT' => '1',
	  'REFRESH' => 'Y',
   ),
   'REFERAL_USE_COUPONS' => array(
      'GROUP' => 'REFERAL_SYSTEM',
      'TITLE' => GetMessage("ITB_REFERAL_USE_COUPONS"),
      'TYPE' => 'SELECT',
	  'VALUES' => array(
					'REFERENCE_ID' => array('Y', 'N'), 
					'REFERENCE' => array(
										GetMessage("ITB_REFERAL_USE_COUPONS_Y"),
										GetMessage("ITB_REFERAL_USE_COUPONS_N"),
										)
									),
	  'DEFAULT' => 'N',
      'SORT' => '2',
	  'CLASS' => 'LGB_PARENT_SELECT'
   ),
   'REFERAL_COUPON_DISCOUNT' => array(
      'GROUP' => 'REFERAL_SYSTEM',
      'TITLE' => GetMessage("ITB_REFERAL_COUPON_DISCOUNT"),
      'TYPE' => 'SELECT',
      'VALUES' => $arDiscounts,
	  'DEFAULT' => '0',
      'SORT' => '3',
	  'CLASS' => 'REFERAL_USE_COUPONS REFERAL_USE_COUPONS_Y'
   ),
   'REFERAL_COUPON_DISCOUNT_IN_PROFILE' => array(
      'GROUP' => 'REFERAL_SYSTEM',
      'TITLE' => GetMessage("ITB_REFERAL_COUPON_DISCOUNT_IN_PROFILE"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'N',
      'SORT' => '3',
	  'CLASS' => 'REFERAL_USE_COUPONS REFERAL_USE_COUPONS_Y',
	  'NOTES' => GetMessage("ITB_REFERAL_COUPON_DISCOUNT_IN_PROFILE_NOTES"),
   ),
   'REFERAL_COUPON_CAN_USER' => array(
      'GROUP' => 'REFERAL_SYSTEM',
      'TITLE' => GetMessage("ITB_REFERAL_COUPON_CAN_USER"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'N',
      'SORT' => '3',
	  'CLASS' => 'REFERAL_USE_COUPONS REFERAL_USE_COUPONS_Y'
   ),
   'REFERAL_COUPON_PREFIX' => array(
      'GROUP' => 'REFERAL_SYSTEM',
      'TITLE' => GetMessage("ITB_REFERAL_COUPON_PREFIX"),
      'TYPE' => 'STRING',
	  'DEFAULT' => 'PARTNER_#USER_ID#',
      'SORT' => '4',
	  'CLASS' => 'REFERAL_USE_COUPONS REFERAL_USE_COUPONS_Y',
	  'NOTES' => GetMessage("ITB_REFERAL_COUPON_PREFIX_NOTES"),
   ),
   'PARTNER_GROUPS' => array(
      'GROUP' => 'REFERAL_SYSTEM',
      'TITLE' => GetMessage("ITB_REFERAL_OPTIONS_PARTNER_GROUP"),
      'TYPE' => 'MSELECT',
	  'VALUES' => $userGrups,
	  'DEFAULT' => '',
      'SORT' => '5',
	  'CLASS' => 'REFERAL_USE_COUPONS REFERAL_USE_COUPONS_Y',
   ),
   
    'EVENTS_MAIL_DESCRIPTION' => array(
      'GROUP' => 'EVENTS_MAIL',
      'TITLE' => GetMessage("ITB_EVENTS_MAIL_DESCRIPTION_LABEL"),
	  'VALUE' => GetMessage("ITB_EVENTS_MAIL_DESCRIPTION"),
      'TYPE' => 'CUSTOM',
      'SORT' => '1',
	  'NOTES' => ''
   ),
   'COUNT_DAY_WARNING' => array(
      'GROUP' => 'EVENTS_MAIL',
      'TITLE' => GetMessage("ITB_COUNT_DAY_WARNING"),
      'TYPE' => 'STRING',
	  'DEFAULT' => '0',
      'SORT' => '2',
	  'REFRESH' => 'Y',
   ),
   'AGENTS_WORK_TIME_FROM' => array(
      'GROUP' => 'EVENTS_MAIL',
      'TITLE' => GetMessage("ITB_AGENTS_WORK_TIME"),
      'TYPE' => 'STRING',
	  'DEFAULT' => '08:00',
      'SORT' => '3',
	  'REFRESH' => 'Y',
   ),
   'AGENTS_WORK_TIME_TO' => array(
      'GROUP' => 'EVENTS_MAIL',
      'TITLE' => GetMessage("ITB_AGENTS_WORK_TIME"),
      'TYPE' => 'STRING',
	  'DEFAULT' => '22:00',
      'SORT' => '3',
	  'REFRESH' => 'Y',
   ),
    
   
   'INTEGRATE_IN_SALE_ORDER_AJAX' => array(
      'GROUP' => 'ORDER_FORM',
      'TITLE' => GetMessage("ITB_INTEGRATE_IN_SALE_ORDER_AJAX"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'Y',
      'SORT' => '1',
	  'NOTES' => GetMessage("ITB_INTEGRATE_IN_SALE_ORDER_AJAX_NOTE")
   ),
   'ORDER_TOTAL_BONUS' => array(
      'GROUP' => 'ORDER_FORM',
      'TITLE' => GetMessage("ITB_ORDER_TOTAL_BONUS"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'Y',
      'SORT' => '7',
   ),
   'ORDER_PAY_BONUS_AUTO' => array(
      'GROUP' => 'ORDER_FORM',
      'TITLE' => GetMessage("ITB_ORDER_PAY_BONUS_AUTO"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'N',
      'SORT' => '8',
   ),
   'INTEGRATE_IN_SALE_BASKET' => array(
      'GROUP' => 'BASKET_INTAGRATE',
      'TITLE' => GetMessage("ITB_INTEGRATE_IN_SALE_BASKET_Y"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'Y',
      'SORT' => '1',
	  'NOTES' => GetMessage("ITB_INTEGRATE_IN_SALE_ORDER_AJAX_NOTE")
   ),
   'AJAX_IN_CATALOG' => array(
      'GROUP' => 'CATALOG_INTAGRATE',
      'TITLE' => GetMessage("ITB_INTEGRATE_AJAX_IN_CATALOG"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'N',
      'SORT' => '1',
	  'NOTES' => ''
   ),   
);


$arLangOptions = [
	'TEMPLATE_BONUS_FOR_CART_ITEM' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("TEMPLATE_BONUS_FOR_CART_ITEM"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => '+ #BONUS# '.GetMessage("ITB_TEXT_BONUS_FOR_ITEM"),
		'SORT' => '10',
   ],
	'TEMPLATE_BONUS_FOR_CART' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("TEMPLATE_BONUS_FOR_CART"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => '+ #BONUS# '.GetMessage("ITB_TEXT_BONUS_FOR_ITEM"),
		'SORT' => '10',
   ],
	'TEXT_BONUS_FOR_ITEM' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_TEXT_BONUS_FOR_ITEM"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => GetMessage("ITB_TEXT_BONUS_FOR_ITEM"),
		'SORT' => '9',
   ],
	'TEXT_BONUS_BALLS' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_ADD_BONUS"),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage("ITB_TEXT_BONUS_BALLS"),
		'SIZE' => 50,
		'SORT' => '1',
   ],
	'TEMPLATE_BONUS_FOR_ORDER' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("TEMPLATE_BONUS_FOR_ORDER"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => '+ #BONUS#',
		'SORT' => '10',
   ],
	'HAVE_BONUS_TEXT' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_USER_BONUS"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => GetMessage("ITB_HAVE_BONUS_TEXT"),
		'SORT' => '2',
   ],
	'CAN_BONUS_TEXT' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_CAN_USE"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => GetMessage("ITB_CAN_BONUS_TEXT"),
		'SORT' => '3',
   ],
	 'MIN_BONUS_TEXT' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_CAN_USE_MIN"),
		'SIZE' => 50,
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage("ITB_MIN_BONUS_TEXT"),
		'SORT' => '4',
   ],
   'MAX_BONUS_TEXT' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_CAN_USE_MAX"),
		'SIZE' => 50,
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage("ITB_MAX_BONUS_TEXT"),
		'SORT' => '5',
   ],
	'PAY_BONUS_TEXT' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_PAY_BONUS_TEXT"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => GetMessage("ITB_PAY_BONUS_TEXT"),
		'SORT' => '6',
   ],
	'TEXT_BONUS_PAY' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_TEXT_BONUS_PAY"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => GetMessage("ITB_TEXT_BONUS_PAY"),
		'SORT' => '7',
   ],
	'ERROR_1_TEXT' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_TEXT_ERROR_1"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => GetMessage("ITB_ERROR_1_TEXT"),
		'SORT' => '8',
   ],
	'TEXT_BONUS_FOR_PAYMENT' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_TEXT_BONUS_FOR_PAYMENT"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => GetMessage("ITB_TEXT_BONUS_FOR_PAYMENT"),
		'SORT' => '10',
   ],
	'TEXT_BONUS_USE_BONUS_BUTTON' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS_TEXT_ORDER_TEXT_BONUS_USE_BONUS"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => GetMessage("ITB_TEXT_BONUS_USE_BONUS"),
		'SORT' => '10',
   ],
	'TEXT_BONUS_ERROR_MIN_BONUS' => [
		'GROUP' => 'TEXT',
		'TITLE' => GetMessage("ITB_OPTIONS__TEXT_BONUS_ERROR_MIN_BONUS"),
		'TYPE' => 'STRING',
		'SIZE' => 50,
		'DEFAULT' => GetMessage("ITB_TEXT_BONUS_ERROR_MIN_BONUS"),
		'SORT' => '10',
   ],
	'TEXT_BONUS_PAYMENT_COMMENT' => [
      'GROUP' => 'TEXT',
      'TITLE' => GetMessage("ITB_OPTIONS_TEXT_BONUS_PAYMENT_COMMENT"),
      'TYPE' => 'STRING',
	  'SIZE' => 50,
      'DEFAULT' => GetMessage("ITB_TEXT_BONUS_PAYMENT_COMMENT"),
      'SORT' => '10',
   ],
	 /*'TEMPLATE_BONUS_FOR_CATALOG_SECTION' => array(
      'GROUP' => 'TEXT',
      'TITLE' => GetMessage("TEMPLATE_BONUS_FOR_CATALOG_SECTION"),
      'TYPE' => 'STRING',
	  'SIZE' => 50,
      'DEFAULT' => '+ #BONUS# '.GetMessage("TEXT_BONUS_FOR_ITEM"),
      'SORT' => '10',
   ),
   'TEMPLATE_BONUS_FOR_CATALOG_ELEMENT' => array(
      'GROUP' => 'TEXT',
      'TITLE' => GetMessage("TEMPLATE_BONUS_FOR_CATALOG_ELEMENT"),
      'TYPE' => 'STRING',
	  'SIZE' => 50,
      'DEFAULT' => '+ #BONUS# '.GetMessage("TEXT_BONUS_FOR_ITEM"),
      'SORT' => '10',
   ),*/
	'REST_USER_IDENT_FIELD' => [
      'TITLE' => GetMessage("ITB_REST_USER_IDENT_FIELD"),
      'TYPE' => 'SELECT',
	  'SIZE' => 50,
      'DEFAULT' => 'ID',
      'SORT' => '10',
   ],
	'REST_SEND_MESSAGES' => [
      'TITLE' => GetMessage("ITB_REST_SEND_MESSAGES"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'Y',
      'SORT' => '11',
   ],
	'REST_DEACTIVATE_ADD_BONUS' => [
      'TITLE' => GetMessage("ITB_REST_DEACTIVATE_ADD_BONUS"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'N',
      'SORT' => '11',
   ],
	'REST_DEACTIVATE_MINUS_BONUS' => [
      'TITLE' => GetMessage("ITB_REST_DEACTIVATE_MINUS_BONUS"),
      'TYPE' => 'CHECKBOX',
	  'DEFAULT' => 'N',
      'SORT' => '11',
   ],
];


$rsLang = \CLanguage::GetList($by='sort', $order='asc', []);
while($arLang = $rsLang->Fetch()) {
	foreach($arLangOptions as $keyOption => $arOption):
		if($arLang['LID'] != 'ru')
			$langSufix = '_'.$arLang['LID'];
		else
			$langSufix = '';
		$arOptions[$keyOption.$langSufix] = $arOption;
	endforeach;
}
?>