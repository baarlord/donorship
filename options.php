<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

global $USER, $APPLICATION;
$moduleId = 'baarlord.donorship';
$moduleName = '[BAARLORD_DONORSHIP]';

if (!$USER->IsAdmin()) {
    return;
}

$options = array(
    'general' => array(
        Loc::getMessage('BAARLORD_DONORSHIP_OPTIONS_TAB'),
        array(
            'USE_DONOR',
            Loc::getMessage('BAARLORD_DONORSHIP_USE_DEBUG_DONOR'),
            'N',
            array('checkbox', 'Y')
        ),
        array(
            'DONOR',
            Loc::getMessage('BAARLORD_DONORSHIP_DEBUG_DONOR'),
            '',
            array('text', 25)
        ),
    )
);

$tabs = array(
    array(
        'DIV' => 'general',
        'TAB' => Loc::getMessage('MAIN_TAB_SET'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET')
    )
);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && strlen($_REQUEST['save']) > 0 && check_bitrix_sessid()) {
    foreach ($options as $option) {
        __AdmSettingsSaveOptions($moduleId, $option);
    }
    LocalRedirect($APPLICATION->GetCurPageParam());
}
$tabControl = new CAdminTabControl('tabControl', $tabs);
$tabControl->Begin();
?>
    <form method='POST'
          action='<?= $APPLICATION->GetCurPage(); ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>'>
        <?= bitrix_sessid_post(); ?>
        <? $tabControl->BeginNextTab(); ?>
        <? __AdmSettingsDrawList($moduleId, $options['general']); ?>
        <? $tabControl->Buttons(array('btnApply' => true, 'btnCancel' => false, 'btnSaveAndAdd' => false)); ?>
    </form>
<?
$tabControl->End();
