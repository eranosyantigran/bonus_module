<?php
if (CModule::IncludeModule("sale"))
{

    $db_props = CSaleOrderProps::GetList(
        array("ID" => "ASC"),
        array("CODE" => 'ITB_PAYMENT_BONUS'),
        false,
        false,
        array()
    );
    while ($props = $db_props->Fetch())
    {
        CSaleOrderProps::Delete($props["ID"]);
    }

    $db_props = CSaleOrderProps::GetList(
        array("ID" => "ASC"),
        array("CODE" => 'ITB_ADD_BONUS'),
        false,
        false,
        array()
    );
    $PropsGroupID = array();
    while ($props = $db_props->Fetch())
    {
        $PropsGroupID[] = $props["PROPS_GROUP_ID"];
        CSaleOrderProps::Delete($props["ID"]);
    }
    //Delette groups of Bonus
    foreach($PropsGroupID as $PropGroup) {
        CSaleOrderPropsGroup::Delete($PropGroup);
    }
}