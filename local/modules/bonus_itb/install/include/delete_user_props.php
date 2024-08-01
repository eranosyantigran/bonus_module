<?php
if (CModule::IncludeModule("main")){

    $userPropBonus = CUserTypeEntity::GetList(array(), array("FIELD_NAME" => "UF_BONUS_COUNT"));
    while($prop = $userPropBonus->Fetch())
    {
        $userTypeEn    = new CUserTypeEntity();
        $userTypeEn->Delete($prop["ID"]);
    }

}