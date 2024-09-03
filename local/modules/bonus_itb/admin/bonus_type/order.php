<?php
global $APPLICATION;

if($request->getPost('id') == 'new'){
    $jsonProfileConditions = Itb\Bonus\Conditions\OrderBonus::OrderBaseConditions('json');
}else{
    $arProfileConditions = unserialize($rs["CONDITIONS"]);
    $arProfileConditions = Itb\Bonus\Condition\ConditionBonus::SetLabelsOrder($arProfileConditions);
    $jsonProfileConditions = \Bitrix\Main\Web\Json::encode($arProfileConditions);
//    $jsonOrderBonusConditions = Itb\Bonus\Conditions\OrderBonus::OrderBaseConditions('json');
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

CJSCore::Init(['core', 'ui', 'core_condtree']);
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
        }
        ?>

        <tr><td width="40%"><?=GetMessage("ITB_PROFILE_TYPE")?></td><td><?=$profileTypeName?></td></tr>
        <? if($bonus_id > 0) {?>
            <tr><td width="40%"><?=GetMessage("ITB_PROFILE_ID")?></td><td><?=$bonus_id?></td></tr>
        <? }?>
        <tr><td width="40%"><?=GetMessage("ITB_PROFILE_ACTIVE")?></td><td><input type="checkbox" name="active" value="Y" <? if($active == "Y") echo " checked"?> /></td></tr>
        <tr><td width="40%"><?=GetMessage("ITB_PROFILE_NAME")?></td><td><input type="text" name="name_profile" size="70" value="<?=$profileName?>" /></td></tr>
        <tr><td width="40%"><?=GetMessage("ITB_PROFILE_SORT")?></td><td><input type="text" name="sort" value="<?=$sort?>"></td></tr>

        <? $tabControl->BeginNextTab();?>

        <tr class="heading" id="tr_BT_SALE_DISCOUNT_SECT_APP"><td colspan="2"><?=GetMessage("logictim.balls_PRODUCTS_COND_SECT")?></td></tr>
        <tr><td width="100%" colspan="2">
                <div id="OrderConditions"></div>

                <script>
                    var JSSaleAct=new BX.TreeConditions(<?=\Itb\Bonus\Conditions\OrderBonus::ArrayParams('json');?>, <?=$jsonProfileConditions?>, <?=\Itb\Bonus\Conditions\OrderBonus::BonusOrderControls('json') ?>);
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
