<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;
use Baarlord\DonorShip\Handler;

Class baarlord_donorship extends CModule
{
    var $MODULE_ID = 'baarlord.donorship';
    var $MODULE_NAME;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_DESCRIPTION;

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('BAARLORD_DONORSHIP_REG_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BAARLORD_DONORSHIP_REG_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('BAARLORD_DONORSHIP_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('BAARLORD_DONORSHIP_PARTNER_URI');
    }

    function DoInstall()
    {
        try {
            $this->registerEvents();
            ModuleManager::registerModule($this->MODULE_ID);
            Option::set($this->MODULE_ID, 'VERSION_DB', $this->versionToInt());
        } catch (\Exception $e) {
            global $APPLICATION;
            $APPLICATION->ThrowException($e->getMessage());
            return false;
        }
    }

    function DoUninstall()
    {
        try {
            $this->UnInstallEvents();
            $this->unRegisterEvents();
            Option::delete($this->MODULE_ID, array('VERSION_DB', SITE_ID));
            ModuleManager::unRegisterModule($this->MODULE_ID);
        } catch (\Exception $e) {
            global $APPLICATION;
            $APPLICATION->ThrowException($e->getMessage());
            return false;
        }
    }

    function registerEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandlerCompatible(
            'main',
            'OnProlog',
            $this->MODULE_ID,
            Manager::class,
            'donorshipInit',
            1
        );
        $eventManager->registerEventHandlerCompatible(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            Handler::class,
            'replacePathImgFromDonor',
            1
        );
    }

    function unRegisterEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main',
            'OnProlog',
            $this->MODULE_ID,
            Manager::class,
            'donorshipInit'
        );
    }

    private function versionToInt()
    {
        return intval(preg_replace('/[^0-9]+/i', '', $this->MODULE_VERSION_DATE));
    }
}
