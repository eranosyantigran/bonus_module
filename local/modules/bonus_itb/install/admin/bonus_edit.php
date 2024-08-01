<?php
if (is_dir($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/bonus_itb/'))
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/bonus_itb/admin/bonus_edit.php");
else
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/bonus_itb/admin/bonus_edit.php");