<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

/** @var array $arCurrentValues */

$arType = array("page" => GetMessage("MAIN_INCLUDE_PAGE"), "sect" => GetMessage("MAIN_INCLUDE_SECT"));
if ($GLOBALS['USER']->CanDoOperation('edit_php'))
{
	$arType["file"] = GetMessage("MAIN_INCLUDE_FILE");
}
$arType['view_content'] = Loc::getMessage("MAIN_INCLUDE_VIEW_CONTENT");
$arType['html'] = Loc::getMessage("MAIN_INCLUDE_HTML");
$arType['component'] = Loc::getMessage("MAIN_INCLUDE_COMPONENT");

$site_template = false;
$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
if ($_REQUEST['template_id'])
{
	$site_template = $_REQUEST['template_id'];
}
elseif($site !== false)
{
	$rsSiteTemplates = CSite::GetTemplateList($site);
	while($arSiteTemplate = $rsSiteTemplates->Fetch())
	{
		if(strlen($arSiteTemplate["CONDITION"])<=0)
		{
			$site_template = $arSiteTemplate["TEMPLATE"];
			break;
		}
	}
}
if (CModule::IncludeModule('fileman'))
{
	$arTemplates = CFileman::GetFileTemplates(LANGUAGE_ID, array($site_template));
	$arTemplatesList = array();
	foreach ($arTemplates as $key => $arTemplate)
	{
		$arTemplateList[$arTemplate["file"]] = "[".$arTemplate["file"]."] ".$arTemplate["name"];
	}
}
else
{
	$arTemplatesList = array("page_inc.php" => "[page_inc.php]", "sect_inc.php" => "[sect_inc.php]");
}

$arComponentParameters = array(
	"GROUPS" => array(
		"_SOURCE" => array(
			"NAME" => GetMessage("MAIN_INCLUDE_PARAMS"),
		),
		"_BLOCK" => array(
			"NAME" => Loc::getMessage("CITRUS_CORE_INCLUDE_BLOCK_PARAMS"),
		),
	),
	
	"PARAMETERS" => array(
		"AREA_FILE_SHOW" => array(
			"NAME" => GetMessage("MAIN_INCLUDE_AREA_FILE_SHOW"), 
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arType,
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => "page",
			"PARENT" => "_SOURCE",
			"REFRESH" => "Y",
		),
		"TITLE"=>array(
			"NAME" => GetMessage("INCLUDE_TPL_SETTINGS_TITLE"),
			"PARENT" => "_BLOCK",
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"DESCRIPTION"=>array(
			"NAME" => GetMessage("INCLUDE_TPL_SETTINGS_DESCRIPTION"),
			"PARENT" => "_BLOCK",
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"PAGE_SECTION"=>array(
			"NAME" => GetMessage("INCLUDE_TPL_SETTINGS_PAGE_SECTION"),
			"PARENT" => "_BLOCK",
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"WIDGET_REL" => array(
			"NAME" => Loc::getMessage("INCLUDE_TPL_SETTINGS_WIDGET_REl"),
			"PARENT" => "_BLOCK",
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"h" => array (
			"NAME" => Loc::getMessage("INCLUDE_TPL_SETTINGS_H"),
			"PARENT" => "_BLOCK",
			"TYPE" => "STRING",
			"DEFAULT" => ".h2",
		),
		"PADDING" => array(
			"NAME" => Loc::getMessage("INCLUDE_TPL_SETTING_PADDING"),
			"PARENT" => "_BLOCK",
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);

if ($GLOBALS['USER']->CanDoOperation('edit_php') && $arCurrentValues["AREA_FILE_SHOW"] == "file")
{
	$arComponentParameters["PARAMETERS"]["PATH"] = array(
		"NAME" => GetMessage("MAIN_INCLUDE_PATH"), 
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"ADDITIONAL_VALUES" => "N",
		"PARENT" => "_SOURCE",
	);
}
elseif ($arCurrentValues["AREA_FILE_SHOW"] === 'view_content')
{
	$arComponentParameters["PARAMETERS"]["VIEW_CONTENT_ID"] = array(
		"NAME" => Loc::getMessage("CITRUS_CORE_INCLUDE_VIEW_CONTENT_ID"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"ADDITIONAL_VALUES" => "N",
		"PARENT" => "_SOURCE",
	);
}
elseif ($arCurrentValues["AREA_FILE_SHOW"] === 'component')
{
	$arComponentParameters["PARAMETERS"]["_COMPONENT"] = array(
		"NAME" => Loc::getMessage("CITRUS_CORE_INCLUDE_COMPONENT"),
		"PARENT" => "_SOURCE",
		"TYPE" => "STRING",
		"REFRESH" => 'Y',
	);
	$arComponentParameters["PARAMETERS"]["_COMPONENT_TEMPLATE"] = array(
		"NAME" => Loc::getMessage("CITRUS_CORE_INCLUDE_COMPONENT_TEMPLATE"),
		"PARENT" => "_SOURCE",
		"TYPE" => "STRING",
		"REFRESH" => 'Y',
	);

	if (($componentName = $arCurrentValues['_COMPONENT'])
		&& CComponentEngine::checkComponentName($componentName))
	{
		$componentTemplate = $arCurrentValues['_COMPONENT_TEMPLATE'] ?: ".default";
		$templateProperties = CComponentUtil::GetTemplateProps($componentName, $componentTemplate, $site_template, $arCurrentValues);

		// используются в .parameters.php у компонентов форм
		$_REQUEST['component_name'] = $componentName;
		$_REQUEST['component_template'] = $componentTemplate;

		$arProps = CComponentUtil::GetComponentProps($componentName, $arCurrentValues, $templateProperties);
		if (isset($arProps['GROUPS']))
		{
			$arComponentParameters['GROUPS'] += $arProps['GROUPS'];
		}
		if (isset($arProps['PARAMETERS']))
		{
			$arComponentParameters['PARAMETERS'] += $arProps['PARAMETERS'];
		}
		$arComponentParameters += $arProps;
	}
}
elseif ($arCurrentValues["AREA_FILE_SHOW"] === 'html')
{
	$arComponentParameters["PARAMETERS"]["_HTML"] = array(
		"NAME" => 'HTML',
		"PARENT" => "_SOURCE",
		"TYPE" => "STRING",
	);
}
elseif (in_array($arCurrentValues["AREA_FILE_SHOW"], ['sect', 'page']))
{
	// @todo Выбирать из выпадающего списка
	$arComponentParameters["PARAMETERS"]["AREA_FILE_SUFFIX"] = array(
		"NAME" => GetMessage("MAIN_INCLUDE_AREA_FILE_SUFFIX"), 
		"TYPE" => "STRING",
		"DEFAULT" => "inc",
		"PARENT" => "_SOURCE",
	);

	if ($arCurrentValues["AREA_FILE_SHOW"] == "sect")
	{
		$arComponentParameters["PARAMETERS"]["AREA_FILE_RECURSIVE"] = array(
			"NAME" => GetMessage("MAIN_INCLUDE_AREA_FILE_RECURSIVE"), 
			"TYPE" => "CHECKBOX",
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => "Y",
			"PARENT" => "_SOURCE",
		);
	}
}

if (!in_array($arCurrentValues["AREA_FILE_SHOW"], ['view_content', 'html', 'component']))
{
	$arComponentParameters["PARAMETERS"]["EDIT_TEMPLATE"] = array(
		"NAME" => GetMessage("MAIN_INCLUDE_EDIT_TEMPLATE"),
		"TYPE" => "LIST",
		"VALUES" => $arTemplateList,
		"DEFAULT" => "",
		"ADDITIONAL_VALUES" => "Y",
		"PARENT" => "_SOURCE",
	);
}