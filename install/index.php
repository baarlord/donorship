<?php
defined('B_PROLOG_INCLUDED') || die;

use Baarlord\DonorShip\Manager;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;
use Baarlord\DonorShip\Handler;

class baarlord_donorship extends CModule
{
    public function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_ID = 'baarlord.donorship';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('BAARLORD_DONORSHIP_REG_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BAARLORD_DONORSHIP_REG_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('BAARLORD_DONORSHIP_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('BAARLORD_DONORSHIP_PARTNER_URI');
    }

    public function DoInstall()
    {
        try {
            $this->InstallEvents();
            ModuleManager::registerModule($this->MODULE_ID);
            Option::set($this->MODULE_ID, 'VERSION_DB', $this->versionToInt());
        } catch (ArgumentOutOfRangeException $e) {
            global $APPLICATION;
            $APPLICATION->ThrowException($e->getMessage());
        }
    }

    public function DoUninstall()
    {
        try {
            $this->UnInstallEvents();
            $this->UnInstallEvents();
            Option::delete($this->MODULE_ID, array('VERSION_DB', SITE_ID));
            ModuleManager::unRegisterModule($this->MODULE_ID);
        } catch (ArgumentNullException $e) {
            global $APPLICATION;
            $APPLICATION->ThrowException($e->getMessage());
        }
    }

    public function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            Handler::class,
            'replacePathImgFromDonor',
            1
        );
    }

    public function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            Handler::class,
            'replacePathImgFromDonor',
            1
        );
    }

    private function versionToInt(): int
    {
        return intval(preg_replace('/[^0-9]+/i', '', $this->MODULE_VERSION_DATE));
    }
}
