<?php
IncludeModuleLangFile(__FILE__);
if (CModule::IncludeModule("main")){

    $arrPropsNameLang['en'] = "Bonus count";
    $arrPropsNameLangErrorMess['en'] = 'An error in completing the user field';
    $arrPropsNameLang['ru'] = GetMessage('BONUS_PROPS_NAME');
    $arrPropsNameLangErrorMess['ru'] = GetMessage('BONUS_PROPS_ERROR_MESS');


    $userTypeEn = new CUserTypeEntity();

    $arrUserFields = [
        'ENTITY_ID'         => 'USER',
        'FIELD_NAME'        => 'UF_BONUS_COUNT',
        'USER_TYPE_ID'      => 'double',
        'XML_ID'            => '',
        'SORT'              => 1,
        'MULTIPLE'          => 'N',
        'MANDATORY'         => 'N',
        'SHOW_FILTER'       => 'I',
        'SHOW_IN_LIST'      => '',
        'EDIT_IN_LIST'      => '',
        'IS_SEARCHABLE'     => 'N',
        'SETTINGS'          => array(
            'DEFAULT_VALUE' => '0',
            'SIZE'          => '20',
            'PRECISION'     => '2',
            'MIN_VALUE'    => '0',
            'MAX_VALUE'    => '0',
        ),
        'EDIT_FORM_LABEL'   => $arrPropsNameLang,
        'LIST_COLUMN_LABEL' => $arrPropsNameLang,
        'LIST_FILTER_LABEL' => $arrPropsNameLang,
        'ERROR_MESSAGE'     => $arrPropsNameLangErrorMess,
        'HELP_MESSAGE'      => $arrPropsNameLang,
    ];

    $userTypeEn->Add($arrUserFields);
}