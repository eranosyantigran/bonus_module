<?php
use Bitrix\Main\EventManager;
use Itb\Bonus\Event\OrderResultPrepared;

EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleComponentOrderResultPrepared',
    [OrderResultPrepared::class, 'OnSaleComponentOrderResultPrepared']
);