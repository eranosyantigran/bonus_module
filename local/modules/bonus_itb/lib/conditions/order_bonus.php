<?php
namespace Itb\Bonus\Conditions;

use Itb\Bonus\ItbHelpers;

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
            'label'=> GetMessage("ITB_ADD_PAY_COND"),
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
                GetMessage("ITB_CAN_PAY"),
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
                GetMessage("ITB_ROUND_SYMBOLS_PAYMENT"),
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
                        /*array('id'=>'prefix', 'type'=>'prefix', 'text'=>GetMessage("ITB_COND_DISCOUNT")),
                        array(
                            'id' => 'logic',
                            'name' => 'logic',
                            'type' => 'select',
                            'values' => array
                                            (
                                                'Equal' => GetMessage("ITB_COND_EQUAL"),
                                                'Not' => GetMessage("ITB_COND_NOT"),
                                            ),
                            'defaultText' => GetMessage("ITB_COND_EQUAL"),
                            'defaultValue' => 'Equal'
                        ),*/
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
            'controlId'=> 'conditionGroup',
            'group'=> true,
            'label'=> '',
            'defaultText'=> '',
            'showIn'=> array(),
            'control'=> array('CONDITION_PERFORM_OPERATIONS')
        );


//        $str = \Bitrix\Main\Web\Json::encode($arrparams);
//        print_r($str);

//        echo "<pre>";
//        print_r(\Bitrix\Main\Web\Json::decode($str));
//
//        die("sss");

//        $str = '[{"controlId":"conditionGroup","group":true,"label":"\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u043f\u0440\u0430\u0432\u0438\u043b\u043e \u043e\u043f\u043b\u0430\u0442\u044b \u0431\u043e\u043d\u0443\u0441\u0430\u043c\u0438","showIn":["CondGroup"],"visual":{"controls":["All","True"],"values":[{"All":"AND","True":"True"},{"All":"OR","True":"True"}],"logic":[{"style":"condition-logic-and","message":"\u0418"},{"style":"condition-logic-or","message":"\u0418\u041b\u0418"}]},"control":["\u0420\u0430\u0437\u0440\u0435\u0448\u0438\u0442\u044c \u043e\u043f\u043b\u0430\u0447\u0438\u0432\u0430\u0442\u044c ",{"id":"bonus","name":"bonus","type":"input","show_value":"Y","defaultValue":"10"},{"id":"bonus_type","name":"bonus_type","type":"select","values":{"percent":"\u043f\u0440\u043e\u0446\u0435\u043d\u0442\u043e\u0432","bonus":"\u0431\u043e\u043d\u0443\u0441\u043e\u0432"},"defaultText":"\u043f\u0440\u043e\u0446\u0435\u043d\u0442\u043e\u0432","defaultValue":"percent"},"\u0441\u0442\u043e\u0438\u043c\u043e\u0441\u0442\u0438 \u0442\u043e\u0432\u0430\u0440\u0430 \u0441 \u043e\u043a\u0440\u0443\u0433\u043b\u0435\u043d\u0438\u0435\u043c \u0431\u043e\u043d\u0443\u0441\u043e\u0432 \u0437\u0430",{"id":"round_type","name":"round_type","type":"select","values":{"UNIT":"\u0435\u0434\u0438\u043d\u0438\u0446\u0443 \u0442\u043e\u0432\u0430\u0440\u0430","POSITION":"\u0432\u0441\u044e \u043f\u043e\u0437\u0438\u0446\u0438\u044e \u0442\u043e\u0432\u0430\u0440\u0430"},"defaultValue":"UNIT","defaultText":"\u0435\u0434\u0438\u043d\u0438\u0446\u0443 \u0442\u043e\u0432\u0430\u0440\u0430"},"\u0434\u043e",{"id":"round","name":"round","type":"select","values":{"A":"0","B":"1","C":"2","D":"3","E":"4"},"defaultValue":"C","defaultText":"2"},"\u0437\u043d\u0430\u043a\u043e\u0432 \u043f\u043e\u0441\u043b\u0435 \u0437\u0430\u043f\u044f\u0442\u043e\u0439, \u0434\u043b\u044f \u043a\u043e\u0442\u043e\u0440\u044b\u0445",{"id":"All","name":"All","type":"select","values":{"AND":"\u0432\u0441\u0435 \u0443\u0441\u043b\u043e\u0432\u0438\u044f","OR":"\u043b\u044e\u0431\u043e\u0435 \u0438\u0437 \u0443\u0441\u043b\u043e\u0432\u0438\u0439"},"defaultText":"\u043b\u044e\u0431\u043e\u0435 \u0438\u0437 \u0443\u0441\u043b\u043e\u0432\u0438\u0439","defaultValue":"OR"},{"id":"True","name":"True","type":"select","values":{"True":"\u0432\u044b\u043f\u043e\u043b\u043d\u0435\u043d\u043e(\u044b)"},"defaultText":"\u0432\u044b\u043f\u043e\u043b\u043d\u0435\u043d\u043e(\u044b)","defaultValue":"True"}],"mess":{"ADD_CONTROL":"\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u0443\u0441\u043b\u043e\u0432\u0438\u0435","SELECT_CONTROL":"\u0412\u044b\u0431\u0435\u0440\u0438\u0442\u0435 \u0443\u0441\u043b\u043e\u0432\u0438\u0435"}},{"controlgroup":"1","group":false,"label":"\u041e\u0441\u043d\u043e\u0432\u043d\u044b\u0435 \u043f\u0430\u0440\u0430\u043c\u0435\u0442\u0440\u044b","showIn":["conditionGroup","conditionGroup2"],"children":[{"controlId":"iblock","group":false,"label":"\u0418\u043d\u0444\u043e\u0431\u043b\u043e\u043a","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0418\u043d\u0444\u043e\u0431\u043b\u043e\u043a"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"select","multiple":"Y","values":{"2":"\u041e\u0434\u0435\u0436\u0434\u0430","3":"\u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f)"},"id":"value","name":"value","show_value":"Y","first_option":"...","defaultText":"...","defaultValue":""}]},{"controlId":"product_categoty","group":false,"label":"\u0420\u0430\u0437\u0434\u0435\u043b","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0420\u0430\u0437\u0434\u0435\u043b"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"popup","popup_url":"iblock_section_search.php","popup_params":{"lang":"ru","discount":"Y","simplename":"Y"},"param_id":"n","multiple":"Y","show_value":"Y","id":"value","name":"value"}]},{"controlId":"product","description":"","group":false,"label":"\u0422\u043e\u0432\u0430\u0440","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0422\u043e\u0432\u0430\u0440"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"multiDialog","popup_url":"cat_product_search_dialog.php","popup_params":{"lang":"ru","caller":"discount_rules","allow_select_parent":"Y"},"param_id":"n","show_value":"Y","id":"value","name":"value"}]},{"controlId":"price","group":false,"label":"\u0426\u0435\u043d\u0430","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0426\u0435\u043d\u0430"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Great":"\u0431\u043e\u043b\u044c\u0448\u0435","Less":"\u043c\u0435\u043d\u044c\u0448\u0435","EqGr":"\u0431\u043e\u043b\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e","EqLs":"\u043c\u0435\u043d\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0431\u043e\u043b\u044c\u0448\u0435","defaultValue":"Great"},{"type":"input","id":"value","name":"value","show_value":"Y","defaultValue":"0","logictimType":"float"}]},{"controlId":"discount","group":false,"label":"\u041d\u0430\u043b\u0438\u0447\u0438\u0435 \u0441\u043a\u0438\u0434\u043a\u0438","showIn":["conditionGroup","conditionGroup2"],"control":[{"type":"select","id":"value","name":"value","values":{"N":"\u0422\u043e\u043b\u044c\u043a\u043e \u0442\u043e\u0432\u0430\u0440\u044b \u0431\u0435\u0437 \u0441\u043a\u0438\u0434\u043a\u0438","Y":"\u0422\u043e\u043b\u044c\u043a\u043e \u0442\u043e\u0432\u0430\u0440\u044b \u0441\u043e \u0441\u043a\u0438\u0434\u043a\u043e\u0439"},"defaultValue":"N"}]},{"controlId":"discount_size","group":false,"label":"\u0420\u0430\u0437\u043c\u0435\u0440 \u0441\u043a\u0438\u0434\u043a\u0438 (\u043d\u0430 \u0435\u0434\u0438\u043d\u0438\u0446\u0443 \u0442\u043e\u0432\u0430\u0440\u0430)","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0420\u0430\u0437\u043c\u0435\u0440 \u0441\u043a\u0438\u0434\u043a\u0438 (\u043d\u0430 \u0435\u0434\u0438\u043d\u0438\u0446\u0443 \u0442\u043e\u0432\u0430\u0440\u0430)"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Great":"\u0431\u043e\u043b\u044c\u0448\u0435","Less":"\u043c\u0435\u043d\u044c\u0448\u0435","EqGr":"\u0431\u043e\u043b\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e","EqLs":"\u043c\u0435\u043d\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0431\u043e\u043b\u044c\u0448\u0435","defaultValue":"Great"},{"type":"input","id":"value","name":"value","show_value":"Y","defaultValue":"0","logictimType":"float"},{"type":"select","id":"type","name":"type","values":{"P":"%","C":"\u0440\u0443\u0431\u043b\u0435\u0439 (\u0432\u0430\u043b\u044e\u0442\u044b \u0446\u0435\u043d\u044b)"},"defaultValue":"P"}]}]},{"controlgroup":"1","group":false,"label":"\u041f\u0430\u0440\u0430\u043c\u0435\u0442\u0440\u044b \u0432 \u043a\u043e\u0440\u0437\u0438\u043d\u0435","showIn":["conditionGroup","conditionGroup2"],"children":[{"controlId":"product_prop_in_cart","group":false,"label":"\u0421\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0442\u043e\u0432\u0430\u0440\u0430 \u0432 \u043a\u043e\u0440\u0437\u0438\u043d\u0435","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0421\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0442\u043e\u0432\u0430\u0440\u0430 \u0432 \u043a\u043e\u0440\u0437\u0438\u043d\u0435"},{"id":"logic-type","name":"logic-type","type":"select","values":{"xml_id":"\u0441 \u0441\u0438\u043c\u0432\u043e\u043b\u044c\u043d\u044b\u043c \u043a\u043e\u0434\u043e\u043c","name":"\u0441 \u043d\u0430\u0437\u0432\u0430\u043d\u0438\u0435\u043c"},"defaultText":"\u0441 \u0441\u0438\u043c\u0432\u043e\u043b\u044c\u043d\u044b\u043c \u043a\u043e\u0434\u043e\u043c","defaultValue":"xml_id"},{"type":"input","id":"logic-type_value","name":"logic-type_value","show_value":"Y","defaultValue":""},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value","show_value":"Y","defaultValue":""}]}]},{"controlgroup":true,"group":false,"label":"\u0421\u0432\u043e\u0439\u0441\u0442\u0432\u0430 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]","showIn":["conditionGroup","conditionGroup2"],"children":[{"controlId":"CondIBProp:2:2","group":false,"label":"\u0417\u0430\u0433\u043e\u043b\u043e\u0432\u043e\u043a \u043e\u043a\u043d\u0430 \u0431\u0440\u0430\u0443\u0437\u0435\u0440\u0430","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0417\u0430\u0433\u043e\u043b\u043e\u0432\u043e\u043a \u043e\u043a\u043d\u0430 \u0431\u0440\u0430\u0443\u0437\u0435\u0440\u0430 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:3","group":false,"label":"\u041a\u043b\u044e\u0447\u0435\u0432\u044b\u0435 \u0441\u043b\u043e\u0432\u0430","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041a\u043b\u044e\u0447\u0435\u0432\u044b\u0435 \u0441\u043b\u043e\u0432\u0430 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:4","group":false,"label":"\u041c\u0435\u0442\u0430-\u043e\u043f\u0438\u0441\u0430\u043d\u0438\u0435","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041c\u0435\u0442\u0430-\u043e\u043f\u0438\u0441\u0430\u043d\u0438\u0435 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:5","group":false,"label":"\u0411\u0440\u0435\u043d\u0434","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0411\u0440\u0435\u043d\u0434 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"5"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:2:6","group":false,"label":"\u041d\u043e\u0432\u0438\u043d\u043a\u0430","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041d\u043e\u0432\u0438\u043d\u043a\u0430 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"6"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:2:7","group":false,"label":"\u041b\u0438\u0434\u0435\u0440 \u043f\u0440\u043e\u0434\u0430\u0436","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041b\u0438\u0434\u0435\u0440 \u043f\u0440\u043e\u0434\u0430\u0436 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"7"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:2:8","group":false,"label":"\u0421\u043f\u0435\u0446\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u0435","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0421\u043f\u0435\u0446\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u0435 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"8"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:2:9","group":false,"label":"\u0410\u0440\u0442\u0438\u043a\u0443\u043b","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0410\u0440\u0442\u0438\u043a\u0443\u043b \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:10","group":false,"label":"\u041f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0438\u0442\u0435\u043b\u044c","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0438\u0442\u0435\u043b\u044c \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:11","group":false,"label":"\u041c\u0430\u0442\u0435\u0440\u0438\u0430\u043b","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041c\u0430\u0442\u0435\u0440\u0438\u0430\u043b \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:12","group":false,"label":"\u0426\u0432\u0435\u0442","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0426\u0432\u0435\u0442 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:15","group":false,"label":"ID \u043f\u043e\u0441\u0442\u0430 \u0431\u043b\u043e\u0433\u0430 \u0434\u043b\u044f \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e ID \u043f\u043e\u0441\u0442\u0430 \u0431\u043b\u043e\u0433\u0430 \u0434\u043b\u044f \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Great":"\u0431\u043e\u043b\u044c\u0448\u0435","Less":"\u043c\u0435\u043d\u044c\u0448\u0435","EqGr":"\u0431\u043e\u043b\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e","EqLs":"\u043c\u0435\u043d\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:16","group":false,"label":"\u041a\u043e\u043b\u0438\u0447\u0435\u0441\u0442\u0432\u043e \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041a\u043e\u043b\u0438\u0447\u0435\u0441\u0442\u0432\u043e \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Great":"\u0431\u043e\u043b\u044c\u0448\u0435","Less":"\u043c\u0435\u043d\u044c\u0448\u0435","EqGr":"\u0431\u043e\u043b\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e","EqLs":"\u043c\u0435\u043d\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:14","group":false,"label":"\u0421 \u044d\u0442\u0438\u043c \u0442\u043e\u0432\u0430\u0440\u043e\u043c \u0440\u0435\u043a\u043e\u043c\u0435\u043d\u0434\u0443\u0435\u043c","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0421 \u044d\u0442\u0438\u043c \u0442\u043e\u0432\u0430\u0440\u043e\u043c \u0440\u0435\u043a\u043e\u043c\u0435\u043d\u0434\u0443\u0435\u043c \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"popup","popup_url":"\/bitrix\/admin\/iblock_element_search.php","popup_params":{"lang":"ru","IBLOCK_ID":"2","discount":"Y"},"param_id":"n","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:18","group":false,"label":"\u0422\u0440\u0435\u043d\u0434\u044b","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0422\u0440\u0435\u043d\u0434\u044b \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"18"},"id":"value","name":"value"}]}]},{"controlgroup":true,"group":false,"label":"\u0421\u0432\u043e\u0439\u0441\u0442\u0432\u0430 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f) [3]","showIn":["conditionGroup","conditionGroup2"],"children":[{"controlId":"CondIBProp:3:20","group":false,"label":"\u0410\u0440\u0442\u0438\u043a\u0443\u043b","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0410\u0440\u0442\u0438\u043a\u0443\u043b \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f) [3]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:3:21","group":false,"label":"\u0426\u0432\u0435\u0442","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0426\u0432\u0435\u0442 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f) [3]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"21"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:3:22","group":false,"label":"\u0420\u0430\u0437\u043c\u0435\u0440\u044b \u043e\u0431\u0443\u0432\u0438","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0420\u0430\u0437\u043c\u0435\u0440\u044b \u043e\u0431\u0443\u0432\u0438 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f) [3]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"22"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:3:23","group":false,"label":"\u0420\u0430\u0437\u043c\u0435\u0440\u044b \u043e\u0434\u0435\u0436\u0434\u044b ","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0420\u0430\u0437\u043c\u0435\u0440\u044b \u043e\u0434\u0435\u0436\u0434\u044b  \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f) [3]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"23"},"id":"value","name":"value"}]}]},{"controlId":"CondGroup","group":true,"label":"","defaultText":"","showIn":[],"control":["CONDITION_PERFORM_OPERATIONS"]}]';

//        echo "<pre>";
//        print_r(\Bitrix\Main\Web\Json::decode($str)); die();

        if($mode=='json'){
//            return  \Bitrix\Main\Web\Json::encode($params);
            return '[{"controlId":"conditionGroup","group":true,"label":"\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u043f\u0440\u0430\u0432\u0438\u043b\u043e \u043e\u043f\u043b\u0430\u0442\u044b \u0431\u043e\u043d\u0443\u0441\u0430\u043c\u0438","showIn":["CondGroup"],"visual":{"controls":["All","True"],"values":[{"All":"AND","True":"True"},{"All":"OR","True":"True"}],"logic":[{"style":"condition-logic-and","message":"\u0418"},{"style":"condition-logic-or","message":"\u0418\u041b\u0418"}]},"control":["\u0420\u0430\u0437\u0440\u0435\u0448\u0438\u0442\u044c \u043e\u043f\u043b\u0430\u0447\u0438\u0432\u0430\u0442\u044c ",{"id":"bonus","name":"bonus","type":"input","show_value":"Y","defaultValue":"10"},{"id":"bonus_type","name":"bonus_type","type":"select","values":{"percent":"\u043f\u0440\u043e\u0446\u0435\u043d\u0442\u043e\u0432","bonus":"\u0431\u043e\u043d\u0443\u0441\u043e\u0432"},"defaultText":"\u043f\u0440\u043e\u0446\u0435\u043d\u0442\u043e\u0432","defaultValue":"percent"},"\u0441\u0442\u043e\u0438\u043c\u043e\u0441\u0442\u0438 \u0442\u043e\u0432\u0430\u0440\u0430 \u0441 \u043e\u043a\u0440\u0443\u0433\u043b\u0435\u043d\u0438\u0435\u043c \u0431\u043e\u043d\u0443\u0441\u043e\u0432 \u0437\u0430",{"id":"round_type","name":"round_type","type":"select","values":{"UNIT":"\u0435\u0434\u0438\u043d\u0438\u0446\u0443 \u0442\u043e\u0432\u0430\u0440\u0430","POSITION":"\u0432\u0441\u044e \u043f\u043e\u0437\u0438\u0446\u0438\u044e \u0442\u043e\u0432\u0430\u0440\u0430"},"defaultValue":"UNIT","defaultText":"\u0435\u0434\u0438\u043d\u0438\u0446\u0443 \u0442\u043e\u0432\u0430\u0440\u0430"},"\u0434\u043e",{"id":"round","name":"round","type":"select","values":{"A":"0","B":"1","C":"2","D":"3","E":"4"},"defaultValue":"C","defaultText":"2"},"\u0437\u043d\u0430\u043a\u043e\u0432 \u043f\u043e\u0441\u043b\u0435 \u0437\u0430\u043f\u044f\u0442\u043e\u0439, \u0434\u043b\u044f \u043a\u043e\u0442\u043e\u0440\u044b\u0445",{"id":"All","name":"All","type":"select","values":{"AND":"\u0432\u0441\u0435 \u0443\u0441\u043b\u043e\u0432\u0438\u044f","OR":"\u043b\u044e\u0431\u043e\u0435 \u0438\u0437 \u0443\u0441\u043b\u043e\u0432\u0438\u0439"},"defaultText":"\u043b\u044e\u0431\u043e\u0435 \u0438\u0437 \u0443\u0441\u043b\u043e\u0432\u0438\u0439","defaultValue":"OR"},{"id":"True","name":"True","type":"select","values":{"True":"\u0432\u044b\u043f\u043e\u043b\u043d\u0435\u043d\u043e(\u044b)"},"defaultText":"\u0432\u044b\u043f\u043e\u043b\u043d\u0435\u043d\u043e(\u044b)","defaultValue":"True"}],"mess":{"ADD_CONTROL":"\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u0443\u0441\u043b\u043e\u0432\u0438\u0435","SELECT_CONTROL":"\u0412\u044b\u0431\u0435\u0440\u0438\u0442\u0435 \u0443\u0441\u043b\u043e\u0432\u0438\u0435"}},{"controlgroup":"1","group":false,"label":"\u041e\u0441\u043d\u043e\u0432\u043d\u044b\u0435 \u043f\u0430\u0440\u0430\u043c\u0435\u0442\u0440\u044b","showIn":["conditionGroup","conditionGroup2"],"children":[{"controlId":"iblock","group":false,"label":"\u0418\u043d\u0444\u043e\u0431\u043b\u043e\u043a","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0418\u043d\u0444\u043e\u0431\u043b\u043e\u043a"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"select","multiple":"Y","values":{"2":"\u041e\u0434\u0435\u0436\u0434\u0430","3":"\u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f)"},"id":"value","name":"value","show_value":"Y","first_option":"...","defaultText":"...","defaultValue":""}]},{"controlId":"product_categoty","group":false,"label":"\u0420\u0430\u0437\u0434\u0435\u043b","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0420\u0430\u0437\u0434\u0435\u043b"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"popup","popup_url":"iblock_section_search.php","popup_params":{"lang":"ru","discount":"Y","simplename":"Y"},"param_id":"n","multiple":"Y","show_value":"Y","id":"value","name":"value"}]},{"controlId":"product","description":"","group":false,"label":"\u0422\u043e\u0432\u0430\u0440","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0422\u043e\u0432\u0430\u0440"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"multiDialog","popup_url":"cat_product_search_dialog.php","popup_params":{"lang":"ru","caller":"discount_rules","allow_select_parent":"Y"},"param_id":"n","show_value":"Y","id":"value","name":"value"}]},{"controlId":"price","group":false,"label":"\u0426\u0435\u043d\u0430","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0426\u0435\u043d\u0430"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Great":"\u0431\u043e\u043b\u044c\u0448\u0435","Less":"\u043c\u0435\u043d\u044c\u0448\u0435","EqGr":"\u0431\u043e\u043b\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e","EqLs":"\u043c\u0435\u043d\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0431\u043e\u043b\u044c\u0448\u0435","defaultValue":"Great"},{"type":"input","id":"value","name":"value","show_value":"Y","defaultValue":"0","logictimType":"float"}]},{"controlId":"discount","group":false,"label":"\u041d\u0430\u043b\u0438\u0447\u0438\u0435 \u0441\u043a\u0438\u0434\u043a\u0438","showIn":["conditionGroup","conditionGroup2"],"control":[{"type":"select","id":"value","name":"value","values":{"N":"\u0422\u043e\u043b\u044c\u043a\u043e \u0442\u043e\u0432\u0430\u0440\u044b \u0431\u0435\u0437 \u0441\u043a\u0438\u0434\u043a\u0438","Y":"\u0422\u043e\u043b\u044c\u043a\u043e \u0442\u043e\u0432\u0430\u0440\u044b \u0441\u043e \u0441\u043a\u0438\u0434\u043a\u043e\u0439"},"defaultValue":"N"}]},{"controlId":"discount_size","group":false,"label":"\u0420\u0430\u0437\u043c\u0435\u0440 \u0441\u043a\u0438\u0434\u043a\u0438 (\u043d\u0430 \u0435\u0434\u0438\u043d\u0438\u0446\u0443 \u0442\u043e\u0432\u0430\u0440\u0430)","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0420\u0430\u0437\u043c\u0435\u0440 \u0441\u043a\u0438\u0434\u043a\u0438 (\u043d\u0430 \u0435\u0434\u0438\u043d\u0438\u0446\u0443 \u0442\u043e\u0432\u0430\u0440\u0430)"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Great":"\u0431\u043e\u043b\u044c\u0448\u0435","Less":"\u043c\u0435\u043d\u044c\u0448\u0435","EqGr":"\u0431\u043e\u043b\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e","EqLs":"\u043c\u0435\u043d\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0431\u043e\u043b\u044c\u0448\u0435","defaultValue":"Great"},{"type":"input","id":"value","name":"value","show_value":"Y","defaultValue":"0","logictimType":"float"},{"type":"select","id":"type","name":"type","values":{"P":"%","C":"\u0440\u0443\u0431\u043b\u0435\u0439 (\u0432\u0430\u043b\u044e\u0442\u044b \u0446\u0435\u043d\u044b)"},"defaultValue":"P"}]}]},{"controlgroup":"1","group":false,"label":"\u041f\u0430\u0440\u0430\u043c\u0435\u0442\u0440\u044b \u0432 \u043a\u043e\u0440\u0437\u0438\u043d\u0435","showIn":["conditionGroup","conditionGroup2"],"children":[{"controlId":"product_prop_in_cart","group":false,"label":"\u0421\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0442\u043e\u0432\u0430\u0440\u0430 \u0432 \u043a\u043e\u0440\u0437\u0438\u043d\u0435","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0421\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0442\u043e\u0432\u0430\u0440\u0430 \u0432 \u043a\u043e\u0440\u0437\u0438\u043d\u0435"},{"id":"logic-type","name":"logic-type","type":"select","values":{"xml_id":"\u0441 \u0441\u0438\u043c\u0432\u043e\u043b\u044c\u043d\u044b\u043c \u043a\u043e\u0434\u043e\u043c","name":"\u0441 \u043d\u0430\u0437\u0432\u0430\u043d\u0438\u0435\u043c"},"defaultText":"\u0441 \u0441\u0438\u043c\u0432\u043e\u043b\u044c\u043d\u044b\u043c \u043a\u043e\u0434\u043e\u043c","defaultValue":"xml_id"},{"type":"input","id":"logic-type_value","name":"logic-type_value","show_value":"Y","defaultValue":""},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value","show_value":"Y","defaultValue":""}]}]},{"controlgroup":true,"group":false,"label":"\u0421\u0432\u043e\u0439\u0441\u0442\u0432\u0430 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]","showIn":["conditionGroup","conditionGroup2"],"children":[{"controlId":"CondIBProp:2:2","group":false,"label":"\u0417\u0430\u0433\u043e\u043b\u043e\u0432\u043e\u043a \u043e\u043a\u043d\u0430 \u0431\u0440\u0430\u0443\u0437\u0435\u0440\u0430","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0417\u0430\u0433\u043e\u043b\u043e\u0432\u043e\u043a \u043e\u043a\u043d\u0430 \u0431\u0440\u0430\u0443\u0437\u0435\u0440\u0430 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:3","group":false,"label":"\u041a\u043b\u044e\u0447\u0435\u0432\u044b\u0435 \u0441\u043b\u043e\u0432\u0430","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041a\u043b\u044e\u0447\u0435\u0432\u044b\u0435 \u0441\u043b\u043e\u0432\u0430 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:4","group":false,"label":"\u041c\u0435\u0442\u0430-\u043e\u043f\u0438\u0441\u0430\u043d\u0438\u0435","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041c\u0435\u0442\u0430-\u043e\u043f\u0438\u0441\u0430\u043d\u0438\u0435 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:5","group":false,"label":"\u0411\u0440\u0435\u043d\u0434","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0411\u0440\u0435\u043d\u0434 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"5"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:2:6","group":false,"label":"\u041d\u043e\u0432\u0438\u043d\u043a\u0430","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041d\u043e\u0432\u0438\u043d\u043a\u0430 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"6"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:2:7","group":false,"label":"\u041b\u0438\u0434\u0435\u0440 \u043f\u0440\u043e\u0434\u0430\u0436","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041b\u0438\u0434\u0435\u0440 \u043f\u0440\u043e\u0434\u0430\u0436 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"7"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:2:8","group":false,"label":"\u0421\u043f\u0435\u0446\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u0435","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0421\u043f\u0435\u0446\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u0435 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"8"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:2:9","group":false,"label":"\u0410\u0440\u0442\u0438\u043a\u0443\u043b","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0410\u0440\u0442\u0438\u043a\u0443\u043b \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:10","group":false,"label":"\u041f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0438\u0442\u0435\u043b\u044c","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0438\u0442\u0435\u043b\u044c \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:11","group":false,"label":"\u041c\u0430\u0442\u0435\u0440\u0438\u0430\u043b","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041c\u0430\u0442\u0435\u0440\u0438\u0430\u043b \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:12","group":false,"label":"\u0426\u0432\u0435\u0442","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0426\u0432\u0435\u0442 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:15","group":false,"label":"ID \u043f\u043e\u0441\u0442\u0430 \u0431\u043b\u043e\u0433\u0430 \u0434\u043b\u044f \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e ID \u043f\u043e\u0441\u0442\u0430 \u0431\u043b\u043e\u0433\u0430 \u0434\u043b\u044f \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Great":"\u0431\u043e\u043b\u044c\u0448\u0435","Less":"\u043c\u0435\u043d\u044c\u0448\u0435","EqGr":"\u0431\u043e\u043b\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e","EqLs":"\u043c\u0435\u043d\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:16","group":false,"label":"\u041a\u043e\u043b\u0438\u0447\u0435\u0441\u0442\u0432\u043e \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u041a\u043e\u043b\u0438\u0447\u0435\u0441\u0442\u0432\u043e \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Great":"\u0431\u043e\u043b\u044c\u0448\u0435","Less":"\u043c\u0435\u043d\u044c\u0448\u0435","EqGr":"\u0431\u043e\u043b\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e","EqLs":"\u043c\u0435\u043d\u044c\u0448\u0435 \u043b\u0438\u0431\u043e \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:14","group":false,"label":"\u0421 \u044d\u0442\u0438\u043c \u0442\u043e\u0432\u0430\u0440\u043e\u043c \u0440\u0435\u043a\u043e\u043c\u0435\u043d\u0434\u0443\u0435\u043c","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0421 \u044d\u0442\u0438\u043c \u0442\u043e\u0432\u0430\u0440\u043e\u043c \u0440\u0435\u043a\u043e\u043c\u0435\u043d\u0434\u0443\u0435\u043c \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"popup","popup_url":"\/bitrix\/admin\/iblock_element_search.php","popup_params":{"lang":"ru","IBLOCK_ID":"2","discount":"Y"},"param_id":"n","id":"value","name":"value"}]},{"controlId":"CondIBProp:2:18","group":false,"label":"\u0422\u0440\u0435\u043d\u0434\u044b","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0422\u0440\u0435\u043d\u0434\u044b \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 [2]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"18"},"id":"value","name":"value"}]}]},{"controlgroup":true,"group":false,"label":"\u0421\u0432\u043e\u0439\u0441\u0442\u0432\u0430 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f) [3]","showIn":["conditionGroup","conditionGroup2"],"children":[{"controlId":"CondIBProp:3:20","group":false,"label":"\u0410\u0440\u0442\u0438\u043a\u0443\u043b","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0410\u0440\u0442\u0438\u043a\u0443\u043b \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f) [3]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e","Contain":"\u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442","NotCont":"\u043d\u0435 \u0441\u043e\u0434\u0435\u0440\u0436\u0438\u0442"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"input","id":"value","name":"value"}]},{"controlId":"CondIBProp:3:21","group":false,"label":"\u0426\u0432\u0435\u0442","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0426\u0432\u0435\u0442 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f) [3]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"21"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:3:22","group":false,"label":"\u0420\u0430\u0437\u043c\u0435\u0440\u044b \u043e\u0431\u0443\u0432\u0438","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0420\u0430\u0437\u043c\u0435\u0440\u044b \u043e\u0431\u0443\u0432\u0438 \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f) [3]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"22"},"id":"value","name":"value"}]},{"controlId":"CondIBProp:3:23","group":false,"label":"\u0420\u0430\u0437\u043c\u0435\u0440\u044b \u043e\u0434\u0435\u0436\u0434\u044b ","showIn":["conditionGroup","conditionGroup2"],"control":[{"id":"prefix","type":"prefix","text":"\u0441\u0432\u043e\u0439\u0441\u0442\u0432\u043e \u0420\u0430\u0437\u043c\u0435\u0440\u044b \u043e\u0434\u0435\u0436\u0434\u044b  \u0438\u043d\u0444\u043e\u0431\u043b\u043e\u043a\u0430 \u041e\u0434\u0435\u0436\u0434\u0430 (\u043f\u0440\u0435\u0434\u043b\u043e\u0436\u0435\u043d\u0438\u044f) [3]"},{"id":"logic","name":"logic","type":"select","values":{"Equal":"\u0440\u0430\u0432\u043d\u043e","Not":"\u043d\u0435 \u0440\u0430\u0432\u043d\u043e"},"defaultText":"\u0440\u0430\u0432\u043d\u043e","defaultValue":"Equal"},{"type":"lazySelect","load_url":"\/bitrix\/tools\/catalog\/get_property_values.php","load_params":{"lang":"ru","propertyId":"23"},"id":"value","name":"value"}]}]},{"controlId":"CondGroup","group":true,"label":"","defaultText":"","showIn":[],"control":["CONDITION_PERFORM_OPERATIONS"]}]';
        }
        return $params;

    }

}