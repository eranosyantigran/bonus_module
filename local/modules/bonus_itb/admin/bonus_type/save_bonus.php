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


    $saveConditions = [];
    if (!empty($request->getPost("profileProductsCond")))
        $saveConditions = \Itb\Bonus\Conditions\OrderBonus::SaveConditions($request["profileProductsCond"]);

    $arSaveFields['CONDITIONS'] = serialize($saveConditions);


    if (!empty($request->getPost("add_bonus")))
        $arSaveFields['ADD_BONUS'] = (float)str_replace(',', '.', $request->getPost("add_bonus"));

    if ($request->getPost('id') == 'new')
        $id =   \Itb\Entity\BonusEventTable::add($arSaveFields);
    elseif ($request->getPost('action') == 'copy')
        $id =  \Itb\Entity\BonusEventTable::add($arSaveFields);
    else {
        \Itb\Entity\BonusEventTable::update($request->getPost('id'), $arSaveFields);
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
