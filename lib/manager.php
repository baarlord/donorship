<?php

namespace Baarlord\DonorShip;

use Bitrix\Main\Config\Option;
use Baarlord\DonorShip\Handler;
use Bitrix\Main\Loader;

class Manager
{
    public static $moduleId = 'baarlord.donorship';
    public static $pathToModule = false;

    public static function donorshipInit()
    {
        $val = Option::get(self::$moduleId, 'USE_DONOR', 'N');
        if ($val == 'Y') {

            $eventManager = EventManager::getInstance();
            $eventManager->addEventHandler(
                'main',
                'OnEndBufferContent',
                array(
                    Handler::class,
                    'replacePathImgFromDonor'
                )
            );
        }
    }

    public static function getPathToModule()
    {
        if (!self::$pathToModule) {
            self::$pathToModule = dirname(dirname(__FILE__));
        }
        return self::$pathToModule;
    }
}