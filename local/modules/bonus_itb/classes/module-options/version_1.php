<style type="text/css">
    div.descrioption {
        width:500px;
        margin:auto;
        background-color: #FEFDEA;
        font-size: 12px;
        color: #333333;
        border:1px solid #D7D6BA;
        padding:10px;
        line-height:16px;
        margin-bottom: 15px;
    }
    .hidden {
        display:none;
    }
    .comment {
        font-size:12px;
        color:#ccc;
        font-style:italic;
    }

    .lb_descript {
        font-size:14px;
        font-weight:bold;
        margin-bottom:10px;
        text-align:center;
    }
    .lb_description p {
        color:grey !important;
        font-style:italic !important;
    }
    .lang_table {
        width: 100%
    }
    .lang_hide {
        opacity: 0;
        width: 0px;
        height: 0px;
        overflow: hidden;
        display: block;
    }
</style>

<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/bonus_itb/classes/module-options/options_version_1.php');
$module_id = 'bonus_itb';
IncludeModuleLangFile(__FILE__);

global $APPLICATION;
$rights = $APPLICATION->GetGroupRight($module_id);

$tabControl = new CAdminTabControl("tabControl", $arTabs);

//echo '<pre>'; print_r($_REQUEST); echo '</pre>';
if($_REQUEST['Update'] == 'Y' && check_bitrix_sessid())
{
    foreach($arOptions as $opt => $arOptParams):
        $val = $_REQUEST[$opt];

        if($arOptParams['TYPE'] == 'CHECKBOX' && $val != 'Y')
            $val = 'N';
        elseif(is_array($val))
            $val = serialize($val);

        COption::SetOptionString($module_id, $opt, $val);
    endforeach;

}

require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/bonus_itb/admin/header.php');
?>

<form name="bonus_itb" id="bonus_itb_form" method="POST" action="<?=$APPLICATION->GetCurPage().'?mid=bonus_itb&mid_menu=1&'.$tabControl->ActiveTabParam().'&lang='.LANGUAGE_ID?>" enctype="multipart/form-data">

    <? $tabControl->Begin();?>

    <? $tabControl->BeginNextTab();?>
    <tr class="heading" id="tr_BT_SALE_DISCOUNT_SECT_APP"><td colspan="2"><?=$arGroups["MAIN"]["TITLE"]?></td></tr>

    <? 	$option = "MODULE_VERSION";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
//    $path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/ITB/classes/module-options/version_access.txt';
//    if(file_exists($path))
//        $access_v_3 = file_get_contents($path);
//    ?>
    <tr <? if($access_v_3 != 'Y') echo 'class="hidden"';?>>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <? echo SelectBoxFromArray($option, $arOption['VALUES'], $val, '', 'class="'.$arOption['CLASS'].'"', false, $module_id);?>
        </td>
    </tr>
    <tr <? if($access_v_3 != 'Y') echo 'class="hidden"';?>>
        <td colspan="2">
            <div class="descrioption" style="margin-bottom:40px;"><?=$arOption["NOTES"]?></div>
        </td>
    </tr>


    <? 	$option = "BONUS_BILL";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td colspan="2">
            <div class="descrioption"><?=$arOptions["BONUS_BILL_DESCRIPTION"]["NOTES"]?></div>
        </td>
    </tr>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <? echo SelectBoxFromArray($option, $arOption['VALUES'], $val, '', 'class="'.$arOption['CLASS'].'"', false, $module_id);?>
        </td>
    </tr>

    <? 	$option = "BONUS_CURRENCY";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr class="<?=$arOption['CLASS']?>">
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <? echo SelectBoxFromArray($option, $arOption['VALUES'], $val, '', $classSel, false, $module_id);?>
        </td>
    </tr>

    <? 	$option = "DISCOUNT_TO_PRODUCTS";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <? echo SelectBoxFromArray($option, $arOption['VALUES'], $val, '', $classSel, false, $module_id);?>
        </td>
    </tr>



    <tr class="heading" id="tr_BT_SALE_DISCOUNT_SECT_APP"><td colspan="2"><?=$arGroups["EVENTS_ORDER_TO_BONUS"]["TITLE"]?></td></tr>

    <? 	$option = "ORDER_STATUS";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <? echo SelectBoxFromArray($option, $arOption['VALUES'], $val, '', $classSel, false, $module_id);?>
        </td>
    </tr>

    <? 	$option = "EVENT_ORDER_PAYED";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>

    <tr class="heading" id="tr_BT_SALE_DISCOUNT_SECT_APP"><td colspan="2"><?=GetMessage("ITB_EVENT_USER_REGISTER")?></td></tr>
    <? 	$option = "USER_REGISTER";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <? echo SelectBoxFromArray($option, $arOption['VALUES'], $val, '', $classSel, false, $module_id);?>
        </td>
    </tr>

    <? $tabControl->BeginNextTab();?>

    <tr class="heading" id="tr_BT_SALE_DISCOUNT_SECT_APP"><td colspan="2"><?=$arGroups["REFERAL_SYSTEM"]["TITLE"]?></td></tr>

    <? 	$option = "REFERAL_LEVELS";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="number" name="<?=$option?>" id="<?=$option?>" size="5" value="<?=$val?>" min="0">
            <? if($rights >= 'W') {?>
                <input type="submit" name="refresh" value="OK">
            <? }?>
        </td>
    </tr>

    <? 	$option = "REFERAL_USE_COUPONS";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <? echo SelectBoxFromArray($option, $arOption['VALUES'], $val, '', 'class="'.$arOption['CLASS'].'"', false, $module_id);?>
        </td>
    </tr>

    <? 	$option = "REFERAL_COUPON_DISCOUNT";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr class="<?=$arOption['CLASS']?>">
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <? echo SelectBoxFromArray($option, $arOption['VALUES'], $val, '', $classSel, false, $module_id);?>
        </td>
    </tr>

    <? 	$option = "REFERAL_COUPON_DISCOUNT_IN_PROFILE";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>

    <tr class="<?=$arOption['CLASS']?>">

        <td width="40%">
            <span data-hint="<?=$arOption["NOTES"]?>"></span>
            <?=$arOption["TITLE"]?>
        </td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>

    <? 	$option = "REFERAL_COUPON_PREFIX";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr class="<?=$arOption['CLASS']?>">
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="text" id="<?=$option?>" name="<?=$option?>" size="25" maxlength="255" value="<?=$val?>">
        </td>
    </tr>
    <tr class="<?=$arOption['CLASS']?>">
        <td colspan="2"><div class="descrioption"><?=$arOption["NOTES"]?></div></td>
    </tr>

    <? 	$option = "REFERAL_COUPON_CAN_USER";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr class="<?=$arOption['CLASS']?>">
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>

    <? 	$option = "PARTNER_GROUPS";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    $val = unserialize($val);
    ?>
    <tr class="<?=$arOption['CLASS']?>">
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <? echo SelectBoxMFromArray($option.'[]', $arOption['VALUES'], $val);?>
        </td>
    </tr>



    <? $tabControl->BeginNextTab();?>

    <? 	$option = "COUNT_DAY_WARNING";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="text" id="<?=$option?>" name="<?=$option?>" size="25" maxlength="255" value="<?=$val?>">
            <? if($rights >= 'W') {?>
                <input type="submit" name="refresh" value="OK">
            <? }?>
        </td>
    </tr>

    <? 	$option = "AGENTS_WORK_TIME_FROM";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <?=GetMessage("ITB_AGENTS_WORK_TIME_FROM")?>
            <?
            CJSCore::Init(array('masked_input'));
            $APPLICATION->IncludeComponent("bitrix:main.clock","",Array(
                    "INPUT_ID" => "AGENTS_WORK_TIME_FROM",
                    "INPUT_NAME" => "AGENTS_WORK_TIME_FROM",
                    "INPUT_TITLE" => "",
                    "INIT_TIME" => $val,
                    "STEP" => "0"
                )
            );?>
            <?=GetMessage("ITB_AGENTS_WORK_TIME_TO")?>
            <?
            $option = "AGENTS_WORK_TIME_TO";
            $arOption = $arOptions[$option];
            $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
            $APPLICATION->IncludeComponent("bitrix:main.clock","",Array(
                    "INPUT_ID" => "AGENTS_WORK_TIME_TO",
                    "INPUT_NAME" => "AGENTS_WORK_TIME_TO",
                    "INPUT_TITLE" => "",
                    "INIT_TIME" => $val,
                    "STEP" => "0"
                )
            );?>
            <? if($rights >= 'W') {?>
                <input type="submit" name="refresh" value="OK">
            <? }?>
            <script>
                BX.ready(function() {
                    var result = new BX.MaskedInput({
                        mask: '99:99',
                        input: BX('AGENTS_WORK_TIME_FROM'),
                        onChange: function(e) {
                            console.log('test');
                        }
                    });
                    var result = new BX.MaskedInput({
                        mask: '99:99',
                        input: BX('AGENTS_WORK_TIME_TO'),
                        onChange: function(e) {
                            console.log('test');
                        }
                    });
                });
            </script>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="descrioption"><?=GetMessage("ITB_AGENTS_WORK_DESCRIPTION")?></div>
        </td>
    </tr>

    <tr class="heading">
        <td colspan="2"><?=$arGroups["EVENTS_MAIL"]["TITLE"]?></td>
    </tr>
    <tr>
        <td width="40%"></td>
        <td colspan="2" class="descrioption"><?=$arOptions["EVENTS_MAIL_DESCRIPTION"]["VALUE"]?></td>
    </tr>

    <tr class="heading">
        <td colspan="2"><?=GetMessage("ITB_EVENTS_SMS_TITLE")?></td>
    </tr>


    <tr>
        <td width="40%"></td>
        <td colspan="2" class="descrioption">
            <?
            if($info = CModule::CreateModuleObject('main'))
            {
                $testVersion = '17.0.18';
                if(CheckVersion($testVersion, $info->MODULE_VERSION))
                {
                    echo GetMessage("ITB_EVENTS_SMS_OLD_BITRIX");
                }
                else
                {
                    if(CModule::IncludeModule("messageservice"))
                    {
                        if($_REQUEST['install_sms'] == 'Y')
                        {
                            $rsET = CEventType::GetList(array("EVENT_TYPE" => "sms"));
                            $arSmsEvents = array();
                            while ($arET = $rsET->Fetch())
                            {
                                if(strpos($arET['EVENT_NAME'], 'LOGICTIM_BONUS') !== false)
                                    $arSmsEvents = $arET;
                            }
                            if(empty($arSmsEvents))
                            {
                                require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/'.$module_id.'/install/include/sms_events_install.php');
                            }

                        }

                        $rsET = CEventType::GetList(array("EVENT_TYPE" => "sms"));
                        $arSmsEvents = array();
                        while($arET = $rsET->Fetch())
                        {
                            if(strpos($arET['EVENT_NAME'], 'LOGICTIM_BONUS') !== false)
                                $arSmsEvents = $arET;
                        }

                        if(!empty($arSmsEvents))
                            echo GetMessage("ITB_EVENTS_SMS_DESCRIPTION");
                        else
                            echo GetMessage("ITB_EVENTS_SMS_INSTALL_TEMPLATES");

                    }
                    else
                    {
                        echo GetMessage("ITB_EVENTS_SMS_INSTALL_MODULE");
                    }
                }
            }
            ?>

        </td>
    </tr>



    <? $tabControl->BeginNextTab();?>

    <tr class="heading">
        <td colspan="2"><?=$arGroups["ORDER_FORM"]["TITLE"]?></td>
    </tr>

    <? 	$option = "INTEGRATE_IN_SALE_ORDER_AJAX";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>
    <tr>
        <td colspan="2"><div class="descrioption"><?=$arOption["NOTES"]?></div></td>
    </tr>

    <? 	$option = "ORDER_TOTAL_BONUS";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>

    <? 	$option = "ORDER_PAY_BONUS_AUTO";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>


    <tr class="heading">
        <td colspan="2"><?=$arGroups["BASKET_INTAGRATE"]["TITLE"]?></td>
    </tr>

    <? 	$option = "INTEGRATE_IN_SALE_BASKET";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>
    <tr>
        <td colspan="2"><div class="descrioption"><?=$arOption["NOTES"]?></div></td>
    </tr>

    <tr class="heading">
        <td colspan="2"><?=$arGroups["CATALOG_INTAGRATE"]["TITLE"]?></td>
    </tr>
    <? 	$option = "AJAX_IN_CATALOG";
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>



    <tr class="heading">
        <td colspan="2"><?=$arGroups["TEXT"]["TITLE"]?> <?=CLanguage::SelectBox("LANGUAGE_LOGICTIM", LANGUAGE_ID, '', '', 'id="LANGUAGE_LOGICTIM"')?></td>
    </tr>

    <tr>
        <td colspan="2" align="center"><div class="descrioption"><?=GetMessage("ITB_TEMPLATE_VIEW_BONUS_COMMENT");?></div></td>
    </tr>


    <tr>
        <td colspan="2">

            <? $rsLang = \CLanguage::GetList($by='sort', $order='asc', []);
            $currentLang = '';
            $arLangs = [];
            while($arLang = $rsLang->Fetch()) {
                $arLangs[] = $arLang;
                if($arLang['LID'] == LANGUAGE_ID)
                    $currentLang = $arLang['LID'];
            }
            if($currentLang == '' && !empty($arLangs))
                $currentLang = $arLangs[0]['LID'];
            ?>

            <? foreach($arLangs as $arLang):
                if($arLang['LID'] == 'ru')
                    $langSufix = '';
                else
                    $langSufix = '_'.$arLang['LID'];
                ?>
                <table id="lang_text_<?=$arLang['LID']?>" class="lang_table <?=($arLang['LID'] == $currentLang ? '' : 'lang_hide')?>">

                    <tr><td colspan="2" align="center";><h3><?=GetMessage("ITB.BONUS_TEXT_CART");?></h3></td></tr>

                    <? 	$option = "TEMPLATE_BONUS_FOR_CART_ITEM";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>
                    <?	$option = "TEMPLATE_BONUS_FOR_CART";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>

                    <tr><td colspan="2" align="center";><h3><?=GetMessage("ITB.BONUS_TEXT_CATALOG");?></h3></td></tr>
                    <? 	$option = "TEXT_BONUS_FOR_ITEM";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>


                    <tr><td colspan="2" align="center";><h3><?=GetMessage("ITB.BONUS_TEXT_ORDER");?></h3></td></tr>

                    <? 	$option = "TEXT_BONUS_BALLS";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);

                    $option_2 = "TEMPLATE_BONUS_FOR_ORDER";
                    $arOption_2 = $arOptions[$option_2];
                    $option_2 = $option_2.$langSufix;
                    $val_2 = COption::GetOptionString($module_id, $option_2, $arOption_2['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="27" maxlength="255" value="<?=$val?>">
                            <input type="text" id="<?=$option_2?>" name="<?=$option_2?>"  size="17" maxlength="255" value="<?=$val_2?>">
                        </td>
                    </tr>

                    <? 	$option = "HAVE_BONUS_TEXT";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>

                    <? 	$option = "CAN_BONUS_TEXT";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>

                    <? 	$option = "MIN_BONUS_TEXT";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>

                    <? 	$option = "MAX_BONUS_TEXT";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>

                    <? 	$option = "PAY_BONUS_TEXT";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>

                    <? 	$option = "TEXT_BONUS_PAY";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>

                    <? 	$option = "ERROR_1_TEXT";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>


                    <? 	$option = "TEXT_BONUS_FOR_PAYMENT";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>
                    <? 	$option = "TEXT_BONUS_USE_BONUS_BUTTON";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>
                    <? 	$option = "TEXT_BONUS_ERROR_MIN_BONUS";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>
                    <? 	$option = "TEXT_BONUS_PAYMENT_COMMENT";
                    $arOption = $arOptions[$option];
                    $option = $option.$langSufix;
                    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?=$arOption["TITLE"]?></td>
                        <td>
                            <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
                        </td>
                    </tr>

                </table>

            <? endforeach;?>
        </td>
    </tr>




    <?php /*?> <? $option = "TEMPLATE_BONUS_FOR_CATALOG_SECTION";
			$arOption = $arOptions[$option];
			$val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
		?>
         <tr>
            <td width="40%"><?=$arOption["TITLE"]?></td>
            <td>
                <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
            </td>
        </tr>
        <? $option = "TEMPLATE_BONUS_FOR_CATALOG_ELEMENT";
			$arOption = $arOptions[$option];
			$val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
		?>
         <tr>
            <td width="40%"><?=$arOption["TITLE"]?></td>
            <td>
                <input type="text" id="<?=$option?>" name="<?=$option?>"  size="50" maxlength="255" value="<?=$val?>">
            </td>
        </tr><?php */?>


    <? $tabControl->BeginNextTab();?>

    <tr class="heading">
        <td colspan="2"><?=GetMessage("ITB_REST_GET_REQ")?></td>
    </tr>

    <?
    $url = (CMain::IsHTTPS()) ? "https://" : "http://";
    $url .= $_SERVER["HTTP_HOST"].'/bitrix/tools/bonus_itb/rest/';
    ?>
    <tr>
        <td width="40%"><b><?=GetMessage("ITB_REST_GET_REQ_URL")?></b></td>
        <td><?=$url;?></td>
    </tr>

    <tr>
        <td width="40%"><b><?=GetMessage("ITB_REST_GET_REQ_TITLE_INFO")?></b></td>
        <td><a href="https://logictim.ru/marketplace/nakopitelnaya_referalnaya_sistema_versii_4_0_i_vyshe/vneshniy_obmen/priem_dannykh/" target="_blank"><?=GetMessage("ITB_REST_GET_REQ_INFO")?></a></td>
    </tr>

    <? 	$option = 'REST_USER_IDENT_FIELD';
    $arOption = $arOptions[$option];

    $arOption['VALUES'] = [
        'SYSTEM' => [
            'ID' => ['NAME'=> GetMessage('ITB_FIELDS_USER_ID'), 'CODE'=>'ID'],
            'EXTERNAL_ID' => ['NAME'=>GetMessage('ITB_FIELDS_USER_EXTERNAL_ID'), 'CODE'=>'EXTERNAL_ID'],
            'LOGIN' => ['NAME'=>GetMessage('ITB_FIELDS_USER_LOGIN'), 'CODE'=>'LOGIN'],
            'EMAIL' => ['NAME'=>GetMessage('ITB_FIELDS_USER_EMAIL'), 'CODE'=>'EMAIL'],
            'PHONE_NUMBER' => ['NAME'=>GetMessage('ITB_FIELDS_USER_REGISTER_PHONE'), 'CODE'=>'PHONE_NUMBER'],
            'PHONE_NUMBER_DIGITS' => ['NAME'=>GetMessage('ITB_FIELDS_USER_REGISTER_PHONE_DIGITS'), 'CODE'=>'PHONE_NUMBER_DIGITS'],
        ]
    ];
    global $GLOBALS;
    $UserProps = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", 0, LANGUAGE_ID);
    if(!empty($UserProps)):
        foreach($UserProps as $keyProp => $arProp):
            if($arProp['USER_TYPE_ID'] == 'string' || $arProp['USER_TYPE_ID'] == 'double')
                $arOption['VALUES']['USER_PROPS'][] = ['NAME'=>'['.$arProp['FIELD_NAME'].'] '.$arProp['EDIT_FORM_LABEL'], 'CODE'=>$arRes['FIELD_NAME']];
        endforeach;
    endif;
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><b><?=$arOption["TITLE"]?></b></td>
        <td>
            <select name="<?=$option?>" id="<?=$option?>" class="field_code">
                <optgroup label="<?=GetMessage("ITB_FIELDS_BITRIX")?>">
                    <? foreach($arOption['VALUES']['SYSTEM'] as $arProp):?>
                        <option value="<?=$arProp['CODE']?>" <?=$arProp['CODE']==$val ? 'selected' : ''?>><?=$arProp['NAME']?></option>
                    <? endforeach;?>
                </optgroup>
                <? if(!empty($arOption['VALUES']['USER_PROPS'])):?>
                    <optgroup label="<?=GetMessage("ITB_FIELDS_BITRIX_USER")?>">
                        <? foreach($arOption['VALUES']['USER_PROPS'] as $arProp):?>
                            <option value="<?=$arProp['CODE']?>" <?=$arProp['CODE']==$val ? 'selected' : ''?>><?=$arProp['NAME']?></option>
                        <? endforeach;?>
                    </optgroup>
                <? endif;?>
            </select>
        </td>
    </tr>

    <?
    $option = 'REST_SEND_MESSAGES';
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>

    <tr class="heading">
        <td colspan="2"><?=GetMessage("ITB_REST_SITE_TITLE")?></td>
    </tr>

    <?
    $option = 'REST_DEACTIVATE_ADD_BONUS';
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>

    <?
    $option = 'REST_DEACTIVATE_MINUS_BONUS';
    $arOption = $arOptions[$option];
    $val = COption::GetOptionString($module_id, $option, $arOption['DEFAULT']);
    ?>
    <tr>
        <td width="40%"><?=$arOption["TITLE"]?></td>
        <td>
            <input type="checkbox" name="<?=$option?>" id="<?=$option?>" value="Y" <? if($val == 'Y') echo ' checked';?> />
        </td>
    </tr>

    <tr>
        <td colspan="2">
            <div class="descrioption"><?=GetMessage("ITB_REST_DEACTIVATE_DESCRIPTION")?></div>
        </td>
    </tr>


    <? $tabControl->BeginNextTab();?>
    <? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>

    <? $tabControl->Buttons();
    if($rights >= 'W') {
        echo 	'<input type="hidden" name="Update" value="Y" />';
        $tabControl->Buttons(array(
            "back_url" => $APPLICATION->GetCurPage().'?lang='.LANGUAGE_ID
        ));
    }
    $tabControl->End();
    ?>

    <?echo bitrix_sessid_post();?>
</form>

<? CJSCore::Init(array('jquery2','core_condtree'));?>
<script type="text/javascript">
    BX.ready(function(){
        $("select.LGB_PARENT_SELECT").change(function() {
            var select_name = $(this).attr('name');
            var select_val = $(this).val();
            $("tr."+select_name).each(function(i,elem){
                if($(this).hasClass(select_name+'_'+select_val))
                    $(this).show();
                else
                    $(this).hide();
            });

        });

        $("select.LGB_PARENT_SELECT").each(function(i,elem) {
            var select_name = $(this).attr('name');
            var select_val = $(this).val();
            $("tr."+select_name).each(function(i,elem){
                if($(this).hasClass(select_name+'_'+select_val))
                    $(this).show();
                else
                    $(this).hide();
            });


        });

        $("#LANGUAGE_LOGICTIM").change(function() {
            var select_val = $(this).val();
            $('.lang_table').addClass('lang_hide');
            $('#lang_text_'+select_val).removeClass('lang_hide');
        });
    });

</script>

<? \Bitrix\Main\UI\Extension::load("ui.hint");?>
<script type="text/javascript">
    BX.ready(function() {
        BX.UI.Hint.init(BX('bonus_itb_form'));
    })
</script>
