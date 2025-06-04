<?php
namespace Itb\Bonus\Conditions;

use Itb\Bonus\Helper\ItbHelpers;

class OrderBonus
{
    public static function ArrayParams($mode=''){

        $arParams = array(
            "parentContainer" => 'OrderConditions',
            "form" => '',
            "formName" => 'itb_bonus',
            "sepID" => '__',
            "prefix" => "profileProductsCond",
            "messTree" => array(
                "SELECT_CONTROL" => GetMessage("ITB_SELECT_COND"),
                "ADD_CONTROL" => GetMessage("ITB_ADD_PROFILE_COND"),
                "DELETE_CONTROL" => GetMessage("ITB_DEL_COND")
            )
        );

        if($mode=='json'){
            return \Bitrix\Main\Web\Json::encode($arParams);
        }

        return $arParams;
    }

    public static function OrderBaseConditions($mode='')
    {
        $params = array(
            "id" => '0',
            "controlId" => 'CondGroup',
            "children" => array(
                array(
                    'id' => 0,
                    'controlId' => "conditionGroup",
                    'values' => array(
                        "bonus" => 0,
                        "bonus_type" => "percent",
                        "round" => "C",
                        "All" => "OR",
                        "True" => "True",
                    ),
                    'children' => [],
                ),
            ),
        );

       if($mode=='json'){
            return \Bitrix\Main\Web\Json::encode($params);
        }
        return $params;
    }

    public static function BonusOrderControls($mode='', $type = 'order'){

        $params = array();

        $params[] = array(
            "controlId" => 'conditionGroup',
            'group'=> true,
            'label'=> GetMessage("ITB_SELECT_COND_ADD_BONUS"),
            'showIn'=> array('CondGroup'),
            'visual'=> array(
                'controls' => array('All', 'True'),
                'values' => array(
                    array(
                        'All' => 'AND',
                        'True' => 'True',
                    ),
                    /*array(
                            'All' => 'AND',
                            'True' => 'False',
                        ),*/
                    array(
                        'All' => 'OR',
                        'True' => 'True',
                    ),
                    /*array(
                            'All' => 'OR',
                            'True' => 'False',
                        )*/
                ),
                'logic' => array(
                    array(
                        'style' => 'condition-logic-and',
                        'message' => GetMessage("ITB_COND_AND")
                    ),
                    /*array(
                            'style' => 'condition-logic-and',
                            'message' => 'AND NOT'
                        ),*/
                    array(
                        'style' => 'condition-logic-or',
                        'message' => GetMessage("ITB_COND_OR")
                    ),
                    /*array(
                            'style' => 'condition-logic-or',
                            'message' => 'OR NOT'
                        )*/
                )
            ),
            'control'=> array(
                GetMessage("ITB_SELECT_COND_ADD_BONUS_TEXT"),
                array
                (
                    'id' => 'bonus',
                    'name' => 'bonus',
                    'type' => 'input',
                    'show_value' => 'Y',
                    'defaultValue' => '10'
                ),
                array
                (
                    'id' => 'bonus_type',
                    'name' => 'bonus_type',
                    'type' => 'select',
                    'values' => array('percent'=>GetMessage("ITB_SELECT_ADD_BONUS_PERCENT"), 'bonus'=>GetMessage("ITB_SELECT_ADD_BONUS_BONUS")),
                    'defaultText' => GetMessage("ITB_SELECT_ADD_BONUS_PERCENT"),
                    'defaultValue' => 'percent'
                ),
                GetMessage("ITB_ROUND_LABEL_NEW"),
                array
                (
                    'id' => 'round_type',
                    'name' => 'round_type',
                    'type' => 'select',
                    'values' => array('UNIT'=>GetMessage("ITB_ROUND_FOR_UNIT"), 'POSITION'=>GetMessage("ITB_ROUND_FOR_POSITION")),
                    'defaultValue' => 'UNIT',
                    'defaultText' => GetMessage("ITB_ROUND_FOR_UNIT"),
                ),
                GetMessage("ITB_ROUND_LABEL_TO"),
                array
                (
                    'id' => 'round',
                    'name' => 'round',
                    'type' => 'select',
                    'values' => array('A'=>'0', 'B'=>'1', 'C'=>'2', 'D'=>'3', 'E'=>'4'),
                    'defaultValue' => 'C',
                    'defaultText' => '2',
                ),
                GetMessage("ITB_ROUND_SYMBOLS"),
                array
                (
                    'id' => 'round_method',
                    'name' => 'round_method',
                    'type' => 'select',
                    'values' => array('MATH'=>GetMessage("ITB_ROUND_MATH"), 'UP'=>GetMessage("ITB_ROUND_UP"), 'DOWN'=>GetMessage("ITB_ROUND_DOWN")),
                    'defaultValue' => 'MATH',
                    'defaultText' => GetMessage("ITB_ROUND_MATH"),
                ),
                GetMessage("ITB_FOR_PRODUCT"),
                array
                (
                    'id' => 'All',
                    'name' => 'All',
                    'type' => 'select',
                    'values' => array('AND'=>GetMessage("ITB_AND_CONDS"), 'OR'=>GetMessage("ITB_OR_CONDS")),
                    'defaultText' => GetMessage("ITB_OR_CONDS"),
                    'defaultValue' => 'OR'
                ),
                array
                (
                    'id' => 'True',
                    'name' => 'True',
                    'type' => 'select',
                    'values' => array('True'=>GetMessage("ITB_CONDS_TRUE")/*, 'False'=>GetMessage("ITB_CONDS_FALSE")*/),
                    'defaultText' => GetMessage("ITB_CONDS_TRUE"),
                    'defaultValue' => 'True'
                ),
            ),
            'mess' => array
            (
                'ADD_CONTROL' => GetMessage("ITB_ADD_COND"),
                'SELECT_CONTROL' => GetMessage("ITB_SELECT_COND")
            )

        );


        $params[] = array(
            "controlId" => 'conditionGroup2',
            'group'=> true,
            'label'=> GetMessage("ITB_SELECT_COND_ADD_BONUS_GROUP_FROM_PROPS"),
            'showIn'=> array('CondGroup'),
            'visual'=> array(
                'controls' => array('All', 'True'),
                'values' => array(
                    array(
                        'All' => 'AND',
                        'True' => 'True',
                    ),
                    /*array(
                            'All' => 'AND',
                            'True' => 'False',
                        ),*/
                    array(
                        'All' => 'OR',
                        'True' => 'True',
                    ),
                    /*array(
                            'All' => 'OR',
                            'True' => 'False',
                        )*/
                ),
                'logic' => array(
                    array(
                        'style' => 'condition-logic-and',
                        'message' => GetMessage("ITB_COND_AND")
                    ),
                    /*array(
                            'style' => 'condition-logic-and',
                            'message' => '? ??'
                        ),*/
                    array(
                        'style' => 'condition-logic-or',
                        'message' => GetMessage("ITB_COND_OR")
                    ),
                    /*array(
                            'style' => 'condition-logic-or',
                            'message' => '??? ??'
                        )*/
                )
            ),
            'control'=> array(
                GetMessage("ITB_COND_ADD_BONUS_FROM_PROP"),
                array
                (
                    'id' => 'bonus_from_props',
                    'name' => 'bonus_from_props',
                    'type' => 'input',
                    'show_value' => 'Y',
                    'defaultValue' => 'LOGICTIM_BONUS_BALLS'
                ),
                GetMessage("ITB_COND_ADD_BONUS_FROM_PROP_TYPE"),
                array
                (
                    'id' => 'bonus_type',
                    'name' => 'bonus_type',
                    'type' => 'select',
                    'values' => array('percent'=>GetMessage("ITB_SELECT_ADD_BONUS_PERCENT"), 'bonus'=>GetMessage("ITB_SELECT_ADD_BONUS_BONUS")),
                    'defaultText' => GetMessage("ITB_SELECT_ADD_BONUS_PERCENT"),
                    'defaultValue' => 'percent'
                ),
                GetMessage("ITB_ROUND_LABEL_SHORT"),
                array
                (
                    'id' => 'round',
                    'name' => 'round',
                    'type' => 'select',
                    'values' => array('A'=>'0', 'B'=>'1', 'C'=>'2', 'D'=>'3', 'E'=>'4'),
                    'defaultValue' => 'C',
                    'defaultText' => '2',
                ),
                GetMessage("ITB_ROUND_SYMBOLS"),
                array
                (
                    'id' => 'All',
                    'name' => 'All',
                    'type' => 'select',
                    'values' => array('AND'=>GetMessage("ITB_AND_CONDS"), 'OR'=>GetMessage("ITB_OR_CONDS")),
                    'defaultText' => GetMessage("ITB_OR_CONDS"),
                    'defaultValue' => 'OR'
                ),
                array
                (
                    'id' => 'True',
                    'name' => 'True',
                    'type' => 'select',
                    'values' => array('True'=>GetMessage("ITB_CONDS_TRUE")/*, 'False'=>GetMessage("ITB_CONDS_FALSE")*/),
                    'defaultText' => GetMessage("ITB_CONDS_TRUE"),
                    'defaultValue' => 'True'
                ),
            ),
            'mess' => array
            (
                'ADD_CONTROL' => GetMessage("ITB_ADD_COND"),
                'SELECT_CONTROL' => GetMessage("ITB_SELECT_COND")
            )

        );

        $params[] = array(
            "controlId" => 'conditionGroup3',
            'group'=> true,
            'label'=> GetMessage("ITB_SELECT_COND_ADD_BONUS_ORDER"),
            'showIn'=> array('CondGroup'),
            'visual'=> array(
                'controls' => array('All', 'True'),
                'values' => array(
                    array(
                        'All' => 'AND',
                        'True' => 'True',
                    ),
                    array(
                        'All' => 'OR',
                        'True' => 'True',
                    ),
                ),
                'logic' => array(
                    array(
                        'style' => 'condition-logic-and',
                        'message' => GetMessage("ITB_COND_AND")
                    ),
                    array(
                        'style' => 'condition-logic-or',
                        'message' => GetMessage("ITB_COND_OR")
                    ),
                )
            ),
            'control'=> array(
                GetMessage("ITB_SELECT_COND_ADD_BONUS_ORDER_TEXT"),
                array
                (
                    'id' => 'bonus',
                    'name' => 'bonus',
                    'type' => 'input',
                    'show_value' => 'Y',
                    'defaultValue' => '100'
                ),
                array
                (
                    'id' => 'bonus_type',
                    'name' => 'bonus_type',
                    'type' => 'select',
                    'values' => array(/*'percent'=>GetMessage("ITB_SELECT_ADD_BONUS_PERCENT"), */'bonus'=>GetMessage("ITB_SELECT_ADD_BONUS_BONUS")),
                    'defaultText' => GetMessage("ITB_SELECT_ADD_BONUS_PERCENT"),
                    'defaultValue' => 'bonus'
                ),
            ),
            'mess' => array
            (
                'ADD_CONTROL' => GetMessage("ITB_ADD_COND"),
                'SELECT_CONTROL' => GetMessage("ITB_SELECT_COND")
            )

        );

        $arCatalogs = ItbHelpers::getCatalogs();
        $arSites = ItbHelpers::GetListSties();
        $params[] = array(
            'controlgroup'=> '1',
            'group'=> false,
            'label'=> GetMessage("ITB_COND_MAIN_PARAMS"),
            'showIn'=> array('conditionGroup', 'conditionGroup2'),
            'children'=> array(
                array(
                    'controlId'=> 'iblock',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_IBLOCK"),
                    'showIn'=> array('conditionGroup', 'conditionGroup2'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_IBLOCK")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type'=> 'select',
                            'multiple'=>'Y',
                            'values'=> $arCatalogs,
                            'id'=> 'value',
                            'name'=> 'value',
                            'show_value'=>'Y',
                            'first_option'=> '...',
                            'defaultText'=> '...',
                            'defaultValue'=> ''
                        )
                    )
                ),
                array(
                    'controlId'=> 'product_categoty',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_SECTION"),
                    'showIn'=> array('conditionGroup', 'conditionGroup2'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_SECTION")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type'=> 'popup',
                            'popup_url'=> 'iblock_section_search.php',
                            'popup_params'=> array('lang'=>LANGUAGE_ID,'discount'=>'Y','simplename'=>'Y'),
                            'param_id'=> 'n',
                            'multiple'=> 'Y',
                            'show_value'=> 'Y',
                            'id'=> 'value',
                            'name'=> 'value'
                        )
                    )
                ),
                array(
                    'controlId'=> 'product',
                    'description'=> '',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_PRODUCT"),
                    'showIn'=> array('conditionGroup', 'conditionGroup2'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_PRODUCT")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type'=> 'multiDialog',
                            'popup_url'=> 'cat_product_search_dialog.php',
                            'popup_params'=> array('lang'=>LANGUAGE_ID, 'caller'=>'discount_rules','allow_select_parent'=>'Y'),
                            'param_id'=> 'n',
                            'show_value'=> 'Y',
                            'id'=> 'value',
                            'name'=> 'value'
                        )
                    )
                ),
                array(
                    'controlId'=> 'price',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_PRICE"),
                    'showIn'=> array('conditionGroup', 'conditionGroup2'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_PRICE")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT"),
                                'Great' => GetMessage("ITB_COND_GREAT"),
                                'Less' => GetMessage("ITB_COND_LESS"),
                                'EqGr' => GetMessage("ITB_COND_EQGR"),
                                'EqLs' => GetMessage("ITB_COND_EQLS"),
                            ),
                            'defaultText' => GetMessage("ITB_COND_GREAT"),
                            'defaultValue' => 'Great'
                        ),
                        array(
                            'type'=> 'input',
                            'id'=> 'value',
                            'name'=> 'value',
                            'show_value'=>'Y',
                            'defaultValue'=> '0',
                            'logictimType' => 'float'
                        )
                    )
                ),
                array(
                    'controlId'=> 'discount',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_DISCOUNT"),
                    'showIn'=> array('conditionGroup', 'conditionGroup2'),
                    'control'=> array(
                        array(
                            'type'=> 'select',
                            'id'=> 'value',
                            'name'=> 'value',
                            'values' => array
                            (
                                'N' => GetMessage("ITB_COND_WITHOUT_DISCOUNT"),
                                'Y' => GetMessage("ITB_COND_WITH_DISCOUNT"),
                            ),
                            'defaultValue'=> 'N',
                        )
                    )
                ),
                array(
                    'controlId'=> 'discount_size',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_DISCOUNT_SIZE"),
                    'showIn'=> array('conditionGroup', 'conditionGroup2'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_DISCOUNT_SIZE")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT"),
                                'Great' => GetMessage("ITB_COND_GREAT"),
                                'Less' => GetMessage("ITB_COND_LESS"),
                                'EqGr' => GetMessage("ITB_COND_EQGR"),
                                'EqLs' => GetMessage("ITB_COND_EQLS"),
                            ),
                            'defaultText' => GetMessage("ITB_COND_GREAT"),
                            'defaultValue' => 'Great'
                        ),
                        array(
                            'type'=> 'input',
                            'id'=> 'value',
                            'name'=> 'value',
                            'show_value'=>'Y',
                            'defaultValue'=> '0',
                            'logictimType' => 'float'
                        ),
                        array(
                            'type'=> 'select',
                            'id'=> 'type',
                            'name'=> 'type',
                            'values' => array
                            (
                                'P' => GetMessage("ITB_COND_PERCENT"),
                                'C' => GetMessage("ITB_COND_EDINIC"),
                            ),
                            'defaultValue'=> 'P',
                        )
                    )
                ),

            )
        );

        //CART PARAMS
        $params[] = array(
            'controlgroup'=> '1',
            'group'=> false,
            'label'=> GetMessage("ITB_COND_CART_PARAMS"),
            'showIn'=> array('conditionGroup', 'conditionGroup2'),
            'children'=> array(
                array(
                    'controlId'=> 'product_prop_in_cart',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_PRODUCT_PROP_IN_CART"),
                    'showIn'=> array('conditionGroup', 'conditionGroup2'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_PRODUCT_PROP_IN_CART")),
                        array(
                            'id' => 'logic-type',
                            'name' => 'logic-type',
                            'type' => 'select',
                            'values' => array
                            (
                                'xml_id' => GetMessage("ITB_COND_PRODUCT_PROP_XML_ID"),
                                'name' => GetMessage("ITB_COND_PRODUCT_PROP_NAME"),
                            ),
                            'defaultText' => GetMessage("ITB_COND_PRODUCT_PROP_XML_ID"),
                            'defaultValue' => 'xml_id'
                        ),
                        array(
                            'type'=> 'input',
                            'id'=> 'logic-type_value',
                            'name'=> 'logic-type_value',
                            'show_value'=>'Y',
                            'defaultValue'=> '',
                        ),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT"),
                                'Contain' => GetMessage("ITB_COND_CONT"),
                                'NotCont' => GetMessage("ITB_COND_NOTCONT"),
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type'=> 'input',
                            'id'=> 'value',
                            'name'=> 'value',
                            'show_value'=>'Y',
                            'defaultValue'=> '',
                        ),
                    )
                ),

            )
        );

        $condotionsProps = \CCatalogCondCtrlIBlockProps::GetControlShow(array('SHOW_IN_GROUPS'=>array('conditionGroup', 'conditionGroup2')));
        if(count($condotionsProps) > 0)
        {
            foreach($condotionsProps as $oneProp):
                $params[] = $oneProp;
            endforeach;

        }

        $params[]=array(
            'controlId'=> 'CondGroup',
            'group'=> true,
            'label'=> '',
            'defaultText'=> '',
            'showIn'=> array(),
            'control'=> array('CONDITION_PERFORM_OPERATIONS')
        );

        if($mode=='json'){
            return \Bitrix\Main\Web\Json::encode($params);
        }
        return $params;

    }

    public static function Controls($mode='', $type = 'order')
    {
        $arSites = ItbHelpers::GetListSties();
        $arUserGroups = ItbHelpers::GetUserGroups();
        $basketRules = ItbHelpers::getBasketRules();
        $arPaySystems = array();
        foreach(ItbHelpers::getPaySystems() as $arPaySystem){
            $arPaySystems[$arPaySystem['ID']] = $arPaySystem['NAME'];
        }
        $arDelivery = array();
        foreach(ItbHelpers::getDelivery() as $delivery){
            $arDelivery[$delivery['ID']] = $delivery['NAME'];
        }
        $arPersonTypes = ItbHelpers::getPersonTypes();
        $arOrderStatuses = ItbHelpers::GetOrderStatuses();
        $arOrderStatuses = array_merge(array('All'=>GetMessage("ITB_COND_COUNT_ORDERS_ALL")), $arOrderStatuses);

        $params = array();

        $params[]=array(
            'controlId'=> 'CondGroup',
            'group'=> true,
            'label'=> '',
            'defaultText'=> '',
            'showIn'=> array(),
            'control'=> array('CONDITION_PERFORM_OPERATIONS')
        );

        $params[] = array(
            'controlgroup'=> '1',
            'group'=> true,
            'label'=> GetMessage("ITB_COND_MAIN_PARAMS"),
            'showIn'=> array('CondGroup'),
            'children'=> array(
                array(
                    'controlId'=> 'sites',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_SITE"),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_SITE")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type'=> 'select',
                            'multiple'=>'Y',
                            'values'=> $arSites,
                            'id'=> 'value',
                            'name'=> 'value',
                            'show_value'=>'Y',
                            'first_option'=> '...',
                            'defaultText'=> '...',
                            'defaultValue'=> ''
                        )
                    )
                ),
                array(
                    'controlId'=> 'userGroups',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_USER_GROUP"),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_USER_GROUP")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type'=> 'select',
                            'multiple'=>'Y',
                            'values'=> $arUserGroups,
                            'id'=> 'value',
                            'name'=> 'value',
                            'show_value'=>'Y',
                            'first_option'=> '...',
                            'defaultText'=> '...',
                            'defaultValue'=> ''
                        )
                    )
                ),
                array(
                    'controlId'=> 'pay_bonus',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_PAY_BONUS"),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_PAY_BONUS")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_HAVE"),
                                'Not' => GetMessage("ITB_COND_HAVE_NO")
                            ),
                            'defaultText' => GetMessage("ITB_COND_HAVE_NO"),
                            'defaultValue' => 'Not'
                        ),
                    )
                ),

            )
        );

        $params[] = array(
            'controlgroup'=> '1',
            'group'=> true,
            'label'=> GetMessage("ITB_COND_OTHER_PARAMS"),
            'showIn'=> array('CondGroup'),
            'children'=> array(
                array(
                    'controlId'=> 'MainUserId',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_USERS_ID"),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_USERS_ID")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type' => 'userPopup',
                            'popup_url' => '/bitrix/admin/user_search.php',
                            'popup_params' => array('FN'=>'logictim_profile'),
                            'param_id' => 'n',
                            'show_value'=>'Y',
                            'user_load_url' => '/bitrix/admin/sale_discount_edit.php',
                            'id'=> 'value',
                            'name'=> 'value',
                        ),
                    )
                ),
                array(
                    'controlId'=> 'orderRowNum',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_USER_ORDERS_COUNT_ROW").($type == 'order_referal' ? ' '.GetMessage("ITB_COND_USER_REFERALA_POSTFIX") : ''),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_USER_ORDERS_COUNT_ROW_USE")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Evry' => GetMessage("ITB_COND_USER_ORDERS_COUNT_ROW_EVRY"),
                                'Only' => GetMessage("ITB_COND_USER_ORDERS_COUNT_ROW_ONLY"),
                            ),
                            'defaultText' => GetMessage("ITB_COND_USER_ORDERS_COUNT_ROW_EVRY"),
                            'defaultValue' => 'Evry'
                        ),
                        array(
                            'type'=> 'input',
                            'id'=> 'ordersCount',
                            'name'=> 'ordersCount',
                            'show_value'=>'Y',
                            'defaultValue' => '2'
                        ),
                        GetMessage("ITB_COND_USER_POSTFIX_IY"),
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_USER_ORDERS_COUNT_ROW_ORDER")),
                        '( '.GetMessage("ITB_COND_COUNT_ORDERS_TEXT_1"),
                        array(
                            'id' => 'type_count',
                            'name' => 'type_count',
                            'type' => 'select',
                            'values' => array
                            (
                                'Include' => GetMessage("ITB_COND_COUNT_TYPE_ORDERS_INCLUDE"),
                                'Exclude' => GetMessage("ITB_COND_COUNT_TYPE_ORDERS_NOT_INCLUDE"),
                            ),
                            'defaultText' => GetMessage("ITB_COND_COUNT_TYPE_ORDERS_INCLUDE"),
                            'defaultValue' => 'Include'
                        ),
                        array(
                            'id' => 'cancell',
                            'name' => 'cancell',
                            'type' => 'select',
                            'values' => array
                            (
                                'All' => GetMessage("ITB_COND_COUNT_ORDERS_ALL"),
                                'Cancell' => GetMessage("ITB_COND_COUNT_ORDERS_CANCELL"),
                                'NotCancell' => GetMessage("ITB_COND_COUNT_ORDERS_NOT_CANCELL"),
                            ),
                            'defaultText' => GetMessage("ITB_COND_COUNT_ORDERS_ALL"),
                            'defaultValue' => 'All'
                        ),
                        array(
                            'id' => 'paid',
                            'name' => 'paid',
                            'type' => 'select',
                            'values' => array
                            (
                                'All' => GetMessage("ITB_COND_COUNT_ORDERS_ALL"),
                                'Paid' => GetMessage("ITB_COND_COUNT_ORDERS_PAID"),
                                'NotPaid' => GetMessage("ITB_COND_COUNT_ORDERS_NOT_PAID"),
                            ),
                            'defaultText' => GetMessage("ITB_COND_COUNT_ORDERS_ALL"),
                            'defaultValue' => 'All'
                        ),
                        GetMessage("ITB_COND_COUNT_ORDERS_STATUS"),
                        array(
                            'id' => 'order_status',
                            'name' => 'order_status',
                            'type' => 'select',
                            'values' => $arOrderStatuses,
                            'defaultText' => GetMessage("ITB_COND_COUNT_ORDERS_ALL"),
                            'defaultValue' => 'All'
                        ),
                        ')'
                    )
                ),
                array(
                    'controlId'=> 'ordersSum',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_USER_ORDERS_SUM").($type == 'order_referal' ? ' '.GetMessage("ITB_COND_USER_REFERALA_POSTFIX") : ''),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_USER_ORDERS_SUM")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'EqGr' => GetMessage("ITB_COND_EQGR"),
                                'Less' => GetMessage("ITB_COND_LESS")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQGR"),
                            'defaultValue' => 'EqGr'
                        ),
                        array(
                            'type'=> 'input',
                            'id'=> 'ordersSum',
                            'name'=> 'ordersSum',
                            'show_value'=>'Y',
                            'defaultValue' => '0'
                        ),
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_FOR_PERIOD")),
                        array(
                            'type'=> 'input',
                            'id'=> 'period',
                            'name'=> 'period',
                            'defaultValue' => '1'
                        ),
                        array(
                            'type'=> 'select',
                            'id'=> 'period_type',
                            'name'=> 'period_type',
                            'values' => array
                            (
                                'D' => GetMessage("ITB_COND_DAY"),
                                'M' => GetMessage("ITB_COND_MONTH"),
                                'Y' => GetMessage("ITB_COND_YEAR")
                            ),
                            'defaultValue' => 'Y',
                            'defaultText' => GetMessage("ITB_COND_YEAR")
                        ),
                    )
                ),
                array(
                    'controlId'=> 'firstOrderDate',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_USER_FIRST_ORDER_DATE").($type == 'order_referal' ? ' '.GetMessage("ITB_COND_USER_REFERALA_POSTFIX") : ''),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_USER_FIRST_ORDER_DATE_USE")),
                        array(
                            'type'=> 'input',
                            'id'=> 'order_num',
                            'name'=> 'order_num',
                            'defaultValue' => '1'
                        ),
                        GetMessage("ITB_COND_USER_POSTFIX_GO"),
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>($type == 'order_referal' ? GetMessage("ITB_COND_USER_FIRST_ORDER_DATE_USE_1_REFERAL") : GetMessage("ITB_COND_USER_FIRST_ORDER_DATE_USE_1"))),
                        array(
                            'type'=> 'input',
                            'id'=> 'period',
                            'name'=> 'period',
                            'defaultValue' => '1'
                        ),
                        array(
                            'type'=> 'select',
                            'id'=> 'period_type',
                            'name'=> 'period_type',
                            'values' => array
                            (
                                'D' => GetMessage("ITB_COND_DAY"),
                                'M' => GetMessage("ITB_COND_MONTH"),
                                'Y' => GetMessage("ITB_COND_YEAR")
                            ),
                            'defaultValue' => 'Y',
                            'defaultText' => GetMessage("ITB_COND_YEAR")
                        ),
                    )
                ),
                array(
                    'controlId'=> 'registrationDate',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_USER_REGISTRATION_DATE").($type == 'order_referal' ? ' '.GetMessage("ITB_COND_USER_REFERALA_POSTFIX") : ''),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>($type == 'order_referal' ? GetMessage("ITB_COND_USER_REGISTRATION_DATE_USE_REFERAL") : GetMessage("ITB_COND_USER_REGISTRATION_DATE_USE"))),
                        array(
                            'type'=> 'input',
                            'id'=> 'period',
                            'name'=> 'period',
                            'defaultValue' => '1'
                        ),
                        array(
                            'type'=> 'select',
                            'id'=> 'period_type',
                            'name'=> 'period_type',
                            'values' => array
                            (
                                'D' => GetMessage("ITB_COND_DAY"),
                                'M' => GetMessage("ITB_COND_MONTH"),
                                'Y' => GetMessage("ITB_COND_YEAR")
                            ),
                            'defaultValue' => 'Y',
                            'defaultText' => GetMessage("ITB_COND_YEAR")
                        ),
                    )
                ),
                array(
                    'controlId'=> 'cartSum',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_CART_SUM"),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_CART_SUM")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'EqGr' => GetMessage("ITB_COND_EQGR"),
                                'Less' => GetMessage("ITB_COND_LESS")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQGR"),
                            'defaultValue' => 'EqGr'
                        ),
                        array(
                            'type'=> 'input',
                            'id'=> 'value',
                            'name'=> 'value',
                            'show_value'=>'Y',
                            'defaultValue' => '0'
                        )
                    )
                ),
                array(
                    'controlId'=> 'orderSum',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_ORDER_SUM"),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_ORDER_SUM")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'EqGr' => GetMessage("ITB_COND_EQGR"),
                                'Less' => GetMessage("ITB_COND_LESS")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQGR"),
                            'defaultValue' => 'EqGr'
                        ),
                        array(
                            'type'=> 'input',
                            'id'=> 'value',
                            'name'=> 'value',
                            'show_value'=>'Y',
                            'defaultValue' => '0'
                        )
                    )
                ),
                array(
                    'controlId'=> 'basketRules',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_BASKET_RULES"),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_BASKET_RULES")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL_USE"),
                                'Not' => GetMessage("ITB_COND_NOT_USE")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type'=> 'select',
                            'multiple'=>'Y',
                            'size'=> 7,
                            'values'=> $basketRules,
                            'show_value'=>'Y',
                            'id'=> 'value',
                            'name'=> 'value',
                        )
                    )
                ),
                array(
                    'controlId'=> 'paySystems',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_PAY_SYSTEM"),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_PAY_SYSTEM")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type'=> 'select',
                            'multiple'=>'Y',
                            'values'=> $arPaySystems,
                            'show_value'=>'Y',
                            'id'=> 'value',
                            'name'=> 'value',
                        )
                    )
                ),
                array(
                    'controlId'=> 'delivery',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_DELIVERY_SYSTEM"),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_DELIVERY_SYSTEM")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type'=> 'select',
                            'multiple'=>'Y',
                            'values'=> $arDelivery,
                            'show_value'=>'Y',
                            'id'=> 'value',
                            'name'=> 'value',
                        )
                    )
                ),
                array(
                    'controlId'=> 'personTypes',
                    'group'=> false,
                    'label'=> GetMessage("ITB_COND_PERSON_TYPE"),
                    'showIn'=> array('CondGroup'),
                    'control'=> array(
                        array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_PERSON_TYPE")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                            (
                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                'Not' => GetMessage("ITB_COND_NOT")
                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),
                        array(
                            'type'=> 'select',
                            'multiple'=>'Y',
                            'values'=> $arPersonTypes,
                            'show_value'=>'Y',
                            'id'=> 'value',
                            'name'=> 'value',
                        )
                    )
                ),
            ),
        );


        if($mode=='json'){
            return \Bitrix\Main\Web\Json::encode($params);
        }
        return $params;
    }
    
    public static function SaveConditions($requestConditions)
    {
        $arIblocks = array();
        foreach($requestConditions as $arCondition):
            if(strpos($arCondition["controlId"], 'CondIBProp') !== false)
            {
                $arExp = explode(':', $arCondition["controlId"]);
                $arIblocks[] = $arExp[1];
            }
        endforeach;

        $arIBProps = array();
        foreach($arIblocks as $iblockId):
            $dbIbProps = \CIBlock::GetProperties($iblockId);
            while($dbProp = $dbIbProps->Fetch())
            {
                $arIBProps[$dbProp["ID"]] = $dbProp;
            }
        endforeach;

        $arConditions = array();
        $arLevels = array(0=>0, 1=>0, 2=>0);
        foreach($requestConditions as $key => $arCond):
            $arKey = explode('__', $key);
            $level = count($arKey)-1;

            if($level < $lastLevel)
            {
                foreach($arLevels as $keyL => $ValL):
                    if($keyL > $level)
                        $arLevels[$keyL] = 0;
                endforeach;
            }


            $id = $arLevels[$level];

            $arBlock = array('id'=>$id, 'controlId'=>$arCond['controlId'], 'values'=>array());

            foreach($arCond as $keyVal => $val):
                if($keyVal == 'controlId')
                    continue;

                if(is_array($val))
                {
                    $arVal = $val;
                    $val = array();
                    foreach($arVal as $valAr):
                        if($valAr != '')
                            $val[] = $valAr;
                    endforeach;
                    $val = array_unique($val);
                }

                if($keyVal == 'value')
                {
                    if(strpos($arCond['controlId'], 'CondIBProp') !== false)
                    {
                        $arExp = explode(':', $arCondition["controlId"]);
                        $propertyId = $arExp[2];
                        if($arIBProps[$propertyId]["PROPERTY_TYPE"] == 'N')
                        {
                            $val =str_replace(',', '.', $val);
                            $val =(float)$val;
                            $val =(string)$val;
                        }
                    }

                    if($arCond['controlId'] == 'price' || $arCond['controlId'] == 'cartSum'  || $arCond['controlId'] == 'orderSum')
                    {
                        $val =str_replace(',', '.', $val);
                        $val =(float)$val;
                        $val =(string)$val;
                    }
                }
                elseif($keyVal == 'bonus')
                {
                    $val =str_replace(',', '.', $val);
                    $val =(float)$val;
                    $val =(string)$val;
                }
                elseif(is_array($keyVal))
                {
                    if($keyVal['controlId'] == 'bonus' || $keyVal['controlId'] == 'ordersSum')
                    {
                        $val =str_replace(',', '.', $val);
                        $val =(float)$val;
                        $val =(string)$val;
                    }
                }

                if($arCond['controlId'] == 'orderRowNum' && $keyVal == 'ordersCount')
                {
                    $val = (int)$val == 0 ? 1: (int)$val;
                    $val =(string)$val;
                }


                $arBlock['values'][$keyVal] = $val;
            endforeach;

            if(is_array($arBlock['values']['value']) && empty($arBlock['values']['value']) && $level > 0)
                continue;

            if(!isset($arBlock['children']))
                $arBlock['children'] = array();

            if($level == 0)
                $arConditions = array('id'=>$id, 'controlId'=>$arCond['controlId'], 'children'=>array());
            elseif($level == 1)
                $arConditions['children'][$id] = $arBlock;
            elseif($level == 2)
                $arConditions['children'][$arLevels[$level-1]-1]['children'][$id] = $arBlock;


            $lastLevel = $level;
            $arLevels[$level] = $arLevels[$level]+1;
        endforeach;

        return $arConditions;

        //$arConditions = \Bitrix\Main\Web\Json::encode($arConditions);
    }
}