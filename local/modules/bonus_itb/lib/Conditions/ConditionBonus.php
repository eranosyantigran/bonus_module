<?php
namespace Itb\Bonus\Condition;

class ConditionBonus
{

    public static function SetLabelsOrder($arProfileConditions)
    {
        if(empty($arProfileConditions["children"]))
            return $arProfileConditions;

        $usersId = array();	 $arUsers = array();
        foreach($arProfileConditions["children"] as $condition):
            if($condition["controlId"] == 'MainUserId' && !empty($condition["values"]["value"]) || $condition["controlId"] == 'PartnerUserId' && !empty($condition["values"]["value"]))
                $usersId = array_merge($usersId, $condition["values"]["value"]);
        endforeach;
        if(!empty($usersId))
        {
            $by="ID";
            $order="desc";
            $DBUser = \CUser::GetList($by,$order,array("ID" => implode('|', $usersId)), array());
            while($arUser = $DBUser->Fetch())
            {
                $userName = trim(($arUser["LAST_NAME"] != '' ? $arUser["LAST_NAME"] : '').($arUser["NAME"] != '' ? ' '.$arUser["NAME"] : ''));
                $userLogin = $arUser["LOGIN"];
                $arUsers[$arUser["ID"]]['LABEL'] = ($userName == '' ? $userLogin : $userName);
            }
        }
        foreach($arProfileConditions["children"] as $keyCondition => $condition):
            if($condition["controlId"] == 'MainUserId' && !empty($condition["values"]["value"]) || $condition["controlId"] == 'PartnerUserId' && !empty($condition["values"]["value"]))
            {
                foreach($condition["values"]["value"] as $val):
                    $arProfileConditions["children"][$keyCondition]["labels"]["value"][] = $arUsers[$val]["LABEL"];
                endforeach;
            }
        endforeach;

        return $arProfileConditions;
    }

}