<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * @var string $mid
 * @var CUser $USER
 * @var CMain $APPLICATION
 */
if (!$USER->IsAdmin()) {
    return;
}

$options = [
    'general' => [
        Loc::getMessage('BAARLORD_DONORSHIP_OPTIONS_TAB'),
        [
            'USE_DONOR',
            Loc::getMessage('BAARLORD_DONORSHIP_USE_DEBUG_DONOR'),
            'N',
            ['checkbox', 'Y'],
        ],
        [
            'DONOR',
            Loc::getMessage('BAARLORD_DONORSHIP_DEBUG_DONOR'),
            '',
            ['text', 25]
        ],
    ],
];

$tabs = [
    [
        'DIV' => 'general',
        'TAB' => Loc::getMessage('MAIN_TAB_SET'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'),
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strlen($_REQUEST['save']) > 0 && check_bitrix_sessid()) {
    foreach ($options as $option) {
        __AdmSettingsSaveOptions($mid, $option);
    }
    LocalRedirect($APPLICATION->GetCurPageParam());
}
$tabControl = new CAdminTabControl('tabControl', $tabs);
$tabControl->Begin();
?>
    <form
            method="POST"
            action="<?= $APPLICATION->GetCurPage(); ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>"
    >
        <?= bitrix_sessid_post(); ?>
        <?php $tabControl->BeginNextTab(); ?>
        <?php __AdmSettingsDrawList($mid, $options['general']); ?>
        <?php $tabControl->Buttons(['btnApply' => true, 'btnCancel' => false, 'btnSaveAndAdd' => false]); ?>
    </form>
<?php
$tabControl->End();
