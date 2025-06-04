<?php

global $USER;

if ((!empty($request['apply']) || !empty($request->getPost('save'))) && !empty($request['id']) && check_bitrix_sessid()):
    $arErrors = [];
    $arSaveFields = [];

    $arSaveFields['NAME'] = $request->getPost('name_profile');
    $arSaveFields['ACTIVE'] =  $request->getPost('active') ;
    $arSaveFields['SORT'] = ((int)$request->getPost('sort') ?  $request->getPost('sort') : 100);
    $arSaveFields['TYPE'] =   $request->getPost('type') ;
    $arSaveFields['USER'] =   $USER->GetID();


    $priceConditions = array(
        "MIN_PAYMENT_BONUS" => $request->getPost('min_payment_bonus') ? $request->getPost('min_payment_bonus') : 0,
        "MIN_PAYMENT_TYPE" => $request->getPost('min_payment_type'),
        "MIN_PAYMENT_INCLUDE_SHIPPING" => $request->getPost('min_payment_include_shipping'),
        "MAX_PAYMENT_BONUS" => $request->getPost('max_payment_bonus') ? $request->getPost('max_payment_bonus') : 0,
        "MAX_PAYMENT_TYPE" => $request->getPost('max_payment_type'),
        "MAX_PAYMENT_INCLUDE_SHIPPING" => $request->getPost('max_payment_include_shipping')
    );

    $saveConditions = [];
    if (!empty($request->getPost("profileProductsCond")))
        $saveConditions = \Itb\Bonus\Conditions\OrderBonus::SaveConditions($request["profileProductsCond"]);

    $arSaveFields['CONDITIONS'] = serialize($saveConditions);
    $arSaveFields['CONDITIONS_PRICE'] = serialize($priceConditions);


    if (!empty($request->getPost("add_bonus")))
        $arSaveFields['ADD_BONUS'] = (float)str_replace(',', '.', $request->getPost("add_bonus"));

    if ($request->getPost('id') == 'new')
        $id =   \Itb\Bonus\Entity\BonusEventTable::add($arSaveFields);
    elseif ($request->getPost('action') == 'copy')
        $id =  \Itb\Bonus\Entity\BonusEventTable::add($arSaveFields);
    else {
        \Itb\Bonus\Entity\BonusEventTable::update($request->getPost('id'), $arSaveFields);
        $id = $request->getPost('id');
    }

    if (!empty($arErrors))
        $_SESSION['LT_WARNING_SAVE_ERRORS'] = $arErrors;

    if (!empty($request->getPost('apply')))
        LocalRedirect($APPLICATION->GetCurPage() . '?id=' . $id . '&' . $tabControl->ActiveTabParam() . '&lang=' . LANGUAGE_ID);
    if (!empty($request->getPost('save'))) {
        if (!empty($arErrors))
            LocalRedirect($APPLICATION->GetCurPage() . '?id=' . $id . '&' . $tabControl->ActiveTabParam() . '&lang=' . LANGUAGE_ID);
        else
            LocalRedirect($APPLICATION->GetCurPage() . '?save=Y&lang=' . LANGUAGE_ID);
    }

endif;
