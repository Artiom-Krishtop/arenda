<?php
/** @var CBCitrusIBAddFormComponent $this Текущий вызванный компонент */
/** @var array $arResult Массив результатов работы компонента */
/** @var array $arParams Массив входящих параметров компонента, может использоваться для учета заданных параметров при выводе шаблона (например, отображении детальных изображений или ссылок). */
/** @var string $componentName Имя вызванного компонента
/** @var string $componentPath Путь к папке с компонентом от DOCUMENT_ROOT
/** @var string $componentTemplate Шаблон вызванного компонента
/** @var string $parentComponentName
/** @var string $parentComponentPath
/** @var string $parentComponentTemplate
/** @var string $templateFile Путь к шаблону относительно корня сайта, например /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateName Имя шаблона компонента (например: .dеfault) */
/** @var string $templateFolder Путь к папке с шаблоном от DOCUMENT_ROOT (например /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData Массив для записи, обратите внимание, таким образом можно передать данные из template.php в файл component_epilog.php, причем эти данные попадают в кеш, т.к. файл component_epilog.php исполняется на каждом хите */

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */

// TODO проверять доступ

/*
 * Данный блок обрабатывает данные диалогового окна для добавления нового
 * типа почтового события. 
 * */

Loc::loadMessages(__FILE__);

if (check_bitrix_sessid() && isset($_REQUEST['showDialog']) && $_REQUEST['showDialog'] == 1)
{
    $GLOBALS["APPLICATION"]->RestartBuffer();
    
    if (!$USER->IsAdmin())
    {
        // если пользователь не админ и не демо-админ, то не может добавлять почтовые события
        $by="c_sort"; $order="desc";
        $arDemoAdminsGroup = CGroup::GetList($by, $order, array("STRING_ID" => "DEMO_ADMINS"))->Fetch();
        if (!$arDemoAdminsGroup["ID"] || !in_array($arDemoAdminsGroup["ID"], $USER->GetUserGroupArray()))
            $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"), false, false, "N", true);
    }

    $arResilt = array();

    $arResult['FORM_DATA'] = array();

    // обработка данных полученных с формы добавления
    if(isset($_REQUEST['ajax-form']) && $_REQUEST['ajax-form'] == "Y")
    {
        CUtil::JSPostUnescape();
        $errorStr = array();
        if(strlen($_REQUEST['EVENT_NAME']) <= 0)
        {
            $errorStr[] = GetMessage('CIEE_ADD_EVENT_NAME_ERROR');
        }
        else
        {
            $arResult['FORM_DATA']['EVENT_NAME'] = $_REQUEST['EVENT_NAME'];
        }

        $arResult['FORM_DATA']['NAME'] = $_REQUEST['NAME'];

        if(count($errorStr) > 0)
        {
            $arResult['ERROR'] = implode("<br />",$errorStr);
        }
        else
        {
            $arAddFieldMailEventType = array(
                'LID' => LANGUAGE_ID,
                'EVENT_NAME' => $arResult['FORM_DATA']['EVENT_NAME'],
                'NAME' => $arResult['FORM_DATA']['NAME'],
                'DESCRIPTION' => ''
            );

            foreach($arParams['FIELDS'] as $propCode => $propInfo)
            {
            	if ($propCode == 'CAPTCHA')
					continue;
                $arAddFieldMailEventType['DESCRIPTION'] .= "#" . $propCode . "# - " . $propInfo['TITLE'] . "\n";
                $arMailMessageFields .= $propInfo['TITLE'] . ": #" . $propCode . "#" . "\n";
            }

            if($type_id = CEventType::Add($arAddFieldMailEventType))
            {
                $arAddFieldMailEventTemplate = array(
                    'ACTIVE' => "Y",
                    'EVENT_NAME' => $arResult['FORM_DATA']['EVENT_NAME'],
                    'LID' => SITE_ID,
                    'EMAIL_FROM' => "#DEFAULT_EMAIL_FROM#",
                    'EMAIL_TO' => "#DEFAULT_EMAIL_FROM#",
                    'SUBJECT' => "#SITE_NAME#: " . $arResult['FORM_DATA']['NAME'],
                    'BODY_TYPE' => 'text',
                    'MESSAGE' => GetMessage('CIEE_MESSAGE_SUBJECT',array("#FIELDS_LIST#" => $arMailMessageFields))
                );

                $emess = new CEventMessage;
                if($emess->Add($arAddFieldMailEventTemplate))
                {
                    echo '<script type="text/javascript">';
                    echo 'BX.WindowManager.Get().Close();';
                    echo '</script>';
                }
                else
                {
                    $et = new CEventType;
                    $et->Delete($arResult['FORM_DATA']['EVENT_NAME']);
                }
            }
        }
    }
    
    require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/interface/admin_lib.php');
    $obJSPopup = new CJSPopup('',
        array(
            'TITLE' => GetMessage('TPL_POPUP_TITLE'),
            'SUFFIX' => 'tpl_add_mail_events_form',
            'ARGS' => ''
        )
    );
    
    if(strlen($arResult['ERROR']) > 0):
        ShowMessage(array("TYPE" => "ERROR","MESSAGE" => $arResult['ERROR']));
    endif;
    
    $obJSPopup->ShowTitlebar();?>
    
    <script type="text/javascript">
        BX.loadCSS('/bitrix/components/citrus/iblock.element.form/templates/showPop/style.css');
    </script>
    
    <?$obJSPopup->StartDescription('bx-edit-menu');?>
    <div class="b-form-title"><?=GetMessage('ADD_FORM_FORM_TITLE')?></div>
    <?$obJSPopup->StartContent();?>
    <div class="b-add-mail-event-form">
        <form action="<?=$APPLICATION->GetCurPageParam()?>" method="POST">
            <input name="ajax-form" type="hidden" value="Y"/>
            <input name="back_url" type="hidden" value="Y"/>
            
            <table class="b-form-content">
                <tr>
                    <td class="b-form-field-title"><span class="required">*</span><?=GetMessage('ADD_FORM_EVENT_NAME')?></td>
                    <td class="b-form-field-content">
                        <input name="EVENT_NAME" type="text" size="30" value="<?=$arResult['FORM_DATA']['EVENT_NAME']?>"/>
                    </td>
                </tr>
                
                <tr>
                    <td class="b-form-field-title"><?=GetMessage('ADD_FORM_NAME')?></td>
                    <td class="b-form-field-content">
                        <input class="field-fullscrin" name="NAME" type="text" size="30" value="<?=$arResult['FORM_DATA']['NAME']?>"/>
                    </td>
                </tr>
            </table>
            <?$obJSPopup->StartButtons();
            $obJSPopup->ShowStandardButtons(array('save','cancel'));
            $obJSPopup->EndButtons();
            ?>
        </form> 
    </div>
    <?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
}

?>