<?php

global $APPLICATION;
CJSCore::Init(['core', 'ui', 'core_condtree']);
\Bitrix\Main\UI\Extension::load('sale.discount.Conditions');

if($request->getPost('id') == 'new'){
    $jsonProfileConditions = Itb\Bonus\Conditions\OrderBonus::OrderBaseConditions('json');
}else{
    $arProfileConditions = unserialize($rs["CONDITIONS"]);
    $otherConditions = unserialize($rs["CONDITIONS_PRICE"]);
    $arProfileConditions = \Itb\Bonus\Condition\ConditionBonus::SetLabelsOrder($arProfileConditions);
    $jsonProfileConditions = \Bitrix\Main\Web\Json::encode($arProfileConditions);
}

if($bonus_type == 'order')
    $APPLICATION->SetTitle(GetMessage("ITB_BONUS_FROM_ORDER"));
else
    $APPLICATION->SetTitle(GetMessage("ITB_BONUS_FROM_ORDER_NO"));

$aTabs = array(
    array("DIV" => "itb_bonus_tab_1", "TAB" => GetMessage("ITB_PROFILE_ORDER_TAB_1"), "TITLE" => GetMessage("ITB_PROFILE_ORDER_TAB_1")),
    array("DIV" => "itb_bonus_tab_2", "TAB" => GetMessage("ITB_PROFILE_ORDER_TAB_2"), "TITLE" => GetMessage("ITB_PROFILE_ORDER_TAB_2")),
//    array("DIV" => "itb_bonus_tab_3", "TAB" => GetMessage("ITB_PROFILE_ORDER_TAB_3"), "TITLE" => GetMessage("ITB_PROFILE_ORDER_TAB_3"))
);

$tabControl = new CAdminTabControl("tabControl".$bonus_id, $aTabs);

require_once(dirname(__FILE__).'/save_bonus.php');

?>

<section class="">

    <form class="itb_bonus" id="itb_bonus_form" name="itb_bonus" method="post" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>">
        <input type="hidden" name="id" value="<?=$bonus_id?>" />
        <input type="hidden" name="type" value="<?=$bonus_type?>" />
        <? if($request['action']) {?>
            <input type="hidden" name="action" value="<?=$request['action']?>" />
        <? }?>

        <?$tabControl->Begin();?>

        <? if(!empty($_SESSION['LT_WARNING_SAVE_ERRORS'])):
			$arErrors = $_SESSION['LT_WARNING_SAVE_ERRORS'];
			unset($_SESSION['LT_WARNING_SAVE_ERRORS']);
		?>
			<div class="lt_otstup"></div>
			<div class="lt_warning">
				<? foreach($arErrors['TEXT'] as $errorType => $errorText):?>
					<div class="lt_warning_text"><?=$errorText?></div>
				<? endforeach;?>
			</div>
		<? endif;?>

        <? $tabControl->BeginNextTab();?>
        <?
        if($bonus_id == 'new' && $request['action'] != 'copy')
        {
            $active = 'Y';
            $profileName = GetMessage("ITB_BONUS_FROM_ORDER");
            $sort = 100;

            if($bonus_type == 'order')
                $profileName = $profileTypeName = GetMessage("ITB_BONUS_FROM_ORDER");
            else
                $profileName = $profileTypeName = GetMessage("ITB_BONUS_FROM_ORDER_NO");

            $min_payment = 0;
            $min_payment_type = 'bonus';
            $min_payment_include_shipping = 'N';
            $max_payment = 100;
            $max_payment_type = 'percent';
            $max_payment_include_shipping = 'N';
            $payment_round = 2;
            $min_product_price = 0;
        }
        else
        {
            $active = $rs["ACTIVE"];
            $profileName = $rs["NAME"];
            $sort = $rs["SORT"];
//            $arOptions = unserialize($arProfile["other_conditions"]);
            if($bonus_type == 'order')
                $profileTypeName = GetMessage("ITB_BONUS_FROM_ORDER");
            else
                $profileTypeName = GetMessage("ITB_BONUS_FROM_ORDER_NO");

            $min_payment = $otherConditions["MIN_PAYMENT_BONUS"];
            $min_payment_type = $otherConditions["MIN_PAYMENT_TYPE"];
            $min_payment_include_shipping = $otherConditions["MIN_PAYMENT_INCLUDE_SHIPPING"];
            $max_payment = $otherConditions["MAX_PAYMENT_BONUS"];
            $max_payment_type = $otherConditions["MAX_PAYMENT_TYPE"];
            $max_payment_include_shipping = $otherConditions["MAX_PAYMENT_INCLUDE_SHIPPING"];
            $payment_round = isset($otherConditions["PAYMENT_ROUND"]) ? $otherConditions["PAYMENT_ROUND"] : 2;
            $min_product_price = isset($otherConditions["MIN_PRODUCT_PRICE"]) ? $otherConditions["MIN_PRODUCT_PRICE"] : 0;
        }
        ?>

        <tr><td width="40%"><?=GetMessage("ITB_PROFILE_TYPE")?></td><td><?=$profileTypeName?></td></tr>
        <? if($bonus_id > 0) {?>
            <tr><td width="40%"><?=GetMessage("ITB_PROFILE_ID")?></td><td><?=$bonus_id?></td></tr>
        <? }?>
        <tr><td width="40%"><?=GetMessage("ITB_PROFILE_ACTIVE")?></td><td><input type="checkbox" name="active" value="Y" <? if($active == "Y") echo " checked"?> /></td></tr>
        <tr><td width="40%"><?=GetMessage("ITB_PROFILE_NAME")?></td><td><input type="text" name="name_profile" size="70" value="<?=$profileName?>" /></td></tr>
        <tr><td width="40%"><?=GetMessage("ITB_PROFILE_SORT")?></td><td><input type="text" name="sort" value="<?=$sort?>"></td></tr>


        <tr class="heading" id="tr_BT_SALE_DISCOUNT_SECT_APP"><td colspan="2"><?=GetMessage("ITB_BONUS_PAY_LIMIT")?></td></tr>
        <tr>
            <td width="40%"><?=GetMessage("ITB_BONUS_PAY_MIN")?></td>
            <td>
                <input type="text" size="3" name="min_payment_bonus" value="<?=$min_payment?>">

                <select name="min_payment_type" style="margin-left:5px;">
                    <option value="bonus" <? if($min_payment_type == 'bonus') echo 'selected="selected"'?>><?=GetMessage("ITB_BONUS_BONUS")?></option>
                    <option value="percent" <? if($min_payment_type == 'percent') echo 'selected="selected"'?>><?=GetMessage("ITB_BONUS_PERCENT")?></option>
                </select>
                <?=GetMessage("ITB_COND_PART_ORDER")?>
                <select name="min_payment_include_shipping" style="margin-left:5px;">
                    <option value="Y" <? if($min_payment_include_shipping == 'Y') echo 'selected="selected"'?>><?=GetMessage("ITB_BONUS_INCLUDE")?></option>
                    <option value="N" <? if($min_payment_include_shipping == 'N') echo 'selected="selected"'?>><?=GetMessage("ITB_BONUS_NO_INCLUDE")?></option>
                </select>
                <?=GetMessage("ITB_COND_PART_INCLUDE_DELIVERY")?>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("ITB_BONUS_PAY_MAX")?></td>
            <td>
                <input type="text" size="3" name="max_payment_bonus" value="<?=$max_payment?>">

                <select name="max_payment_type" style="margin-left:5px;">
                    <option value="bonus" <? if($max_payment_type == 'bonus') echo 'selected="selected"'?>><?=GetMessage("ITB_BONUS_BONUS")?></option>
                    <option value="percent" <? if($max_payment_type == 'percent') echo 'selected="selected"'?>><?=GetMessage("ITB_BONUS_PERCENT")?></option>
                </select>
                <?=GetMessage("ITB_COND_PART_ORDER")?>
                <select name="max_payment_include_shipping" style="margin-left:5px;">
                    <option value="Y" <? if($max_payment_include_shipping == 'Y') echo 'selected="selected"'?>><?=GetMessage("ITB_BONUS_INCLUDE")?></option>
                    <option value="N" <? if($max_payment_include_shipping == 'N') echo 'selected="selected"'?>><?=GetMessage("ITB_BONUS_NO_INCLUDE")?></option>
                </select>
                <?=GetMessage("ITB_COND_PART_INCLUDE_DELIVERY")?>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("ITB_BONUS_MIN_PRODUCT_PRICE")?></td>
            <td>
                <input type="text" size="3" name="min_product_price" value="<?=$min_product_price?>">
                <?=GetMessage("ITB_BONUS_SITE_CURRENCY")?>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("ITB_BONUS_PAY_ROUND")?></td>
            <td>
                <select name="payment_round" style="margin-left:5px;">
                    <option value="0" <? if($payment_round == 0) echo 'selected="selected"'?>>0</option>
                    <option value="1" <? if($payment_round == 1) echo 'selected="selected"'?>>1</option>
                    <option value="2" <? if($payment_round == 2) echo 'selected="selected"'?>>2</option>
                </select>
                <?=GetMessage("ITB_BONUS_PAY_ROUND_1")?>
            </td>
        </tr>


        <? $tabControl->BeginNextTab();?>

        <tr class="heading" id="tr_BT_SALE_DISCOUNT_SECT_APP"><td colspan="2"><?=GetMessage("ITB_PRODUCTS_COND_SECT")?></td></tr>
        <tr><td width="100%" colspan="2">
                <div id="ProfileConditions"></div>

                <script>
                    var JSSaleAct=new BX.TreeConditions(
                            <?=\Itb\Bonus\Conditions\OrderBonus::ArrayParams('json');?>,
                            <?=$jsonProfileConditions?>,
                            <?=\Itb\Bonus\Conditions\OrderBonus::BonusOrderControls('json') ?>);
                </script>
            </td></tr>

        <?
        $tabControl->Buttons(array(
            "back_url" => $APPLICATION->GetCurPage().'?lang='.LANGUAGE_ID
        ));
        ?>
        <?echo bitrix_sessid_post();?>
        <?$tabControl->End();?>
    </form>

</section>


<? \Bitrix\Main\UI\Extension::load("ui.hint");?>
<script type="text/javascript">
    BX.ready(function() {
        BX.UI.Hint.init(BX('itb_bonus_form'));
    })
</script>
