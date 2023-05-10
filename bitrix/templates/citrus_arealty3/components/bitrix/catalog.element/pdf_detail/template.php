<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

use	Bitrix\Main\Localization\Loc;
use	Bitrix\Main\Web\Json;
use Citrus\Arealty\Yamap;
use	Citrus\Arealty\Entity\SettingsTable;

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$theme = new \Citrus\Arealty\Theme(SITE_ID);

// nook add image title from SEO module
$addImage = function($id, $title='', $alt='') use (&$images, $arResult)
{
	$pic = CFile::GetFileArray($id);
	if ($pic)
	{
		$preview = CFile::ResizeImageGet($id, Array('width' => 600, 'height' => 600), BX_RESIZE_IMAGE_PROPORTIONAL, $bInitSizes = true);
		$preview["src"] = $_SERVER["DOCUMENT_ROOT"] . $preview["src"]; // FIX dompdf needs full path
		$images[] = array(
			'id' => 'img' . $id,
			'src' => $_SERVER["DOCUMENT_ROOT"] . $pic["SRC"], // FIX dompdf needs full path
			'width' => $pic['WIDTH'],
			'height' => $pic['HEIGHT'],
			'preview' => $preview,
			'alt' => strlen($alt) ? $alt : $arResult['NAME'],
			'title' => strlen($title) ? $title : $arResult['NAME'],
		);
	}
};

$images = array();
if (is_array($arResult["DETAIL_PICTURE"]))
	$addImage($arResult["DETAIL_PICTURE"]["ID"], $arResult["DETAIL_PICTURE"]["TITLE"], $arResult["DETAIL_PICTURE"]["ALT"]);
elseif (is_array($arResult["PREVIEW_PICTURE"]))
	$addImage($arResult["PREVIEW_PICTURE"]["ID"], $arResult["PREVIEW_PICTURE"]["TITLE"], $arResult["PREVIEW_PICTURE"]["ALT"]);
if (is_array($arResult["PROPERTIES"]["photo"]['VALUE']))
	foreach ($arResult["PROPERTIES"]["photo"]['VALUE'] as $key => $id)
	{
		$addImage($id, $arResult["PROPERTIES"]["photo"]['DESCRIPTION'][$key]);
	}

$detailText = strlen(trim(strip_tags($arResult["DETAIL_TEXT"]))) ? $arResult["DETAIL_TEXT"] : (strlen(trim(strip_tags($arResult["PREVIEW_TEXT"]))) ? $arResult["PREVIEW_TEXT"] : false);
$logo = SettingsTable::getLogoPath(array('height' => 64));
$showText = SettingsTable::getValue('LOGO_SHOW_TEXT') == 'Y';
$siteName = SettingsTable::getValue('SITE_NAME');
if (trim($siteName) == "")
{
	$siteName = Loc::getMessage("CITRUS_AREALTY_PDF_SEND_ELEMENT_TITLE");
}

?>

<style>
	.object-top {
		width: 100%;
	}
	.object-top .col-lt.left{
		width: 500px;
	}
	.object-top .col-lt.right{
		width: 200px;
	}
	.col-t {
		width: 50%;
	}
	.object-top .col-t {
		vertical-align: top;
	}
	.object-content .col-t {
		vertical-align: top;
	}
	.logo-image  {
		/* display: inline-block; */
	}
	.logo-image img{
		height: 64px;
		width: auto;
	}
	.logo-text{
		text-transform: uppercase;
		display: inline-block;
	}
	.header-phone-number {
		font-size: 20px;
		display: block;
		color: #333;
		text-align: right;
	}
	.content-title {
		font-size: 20px;
		font-weight: 500;
	}
	.object-gallery {
		padding-right: 10px;
		height: 290px;
		overflow: hidden;
	}
	.object-gallery img{
        width: 400px;
   }
   .object-gallery-previews {
       text-align: center;
       margin: 25px 5px 0;
       display:inline-block;
   }
   .object-gallery-preview {
       text-align: center;
        display:inline-block;
        width: 125px;
        height:85px;
        overflow: hidden;
   }
   .object-gallery-preview img{
       width: 125px;
          display:inline-block;
   }
	.object-text {
		font-size: 12px;
	}
	.object-address {
		margin-bottom: 10px;
		font-size: 16px;
	}
	.object-price {
		color: #fff;
		padding: 10px 15px;
		height: 40px;
		text-align: center;
		background-color: #<?= $theme->getColor() ?>;
		margin-bottom: 10px;
	}
	.object-price span {
		font-size: 24px;
	}
	.object-option {
		font-size: 13px;
		color: #333;
	}
	.object-option div > span:first-child{
		font-weight:700;
	}
	.wrapper {
		page-break-inside: avoid;
	}
	.col-lt-size {
		margin-bottom: 20px;
	}
	.bottom-title {
		font-size: 18px;
		margin-top: 0;
		font-weight: 500;
	}
	.ava {
		width: 200px;
	}
	.ava-info {
		height: 200px;
	}
	.object-manager-ava {
		overflow: hidden;
		width: 200px;
		height: 200px;
		border-radius: 100px;
	}
	.object-manager-ava img{
		height: 200px;
	}
	.object-manager-info {
		padding-left: 30px;
		font-size: 1rem;
	}
	.object-manager-name {
		display: block;
		font-size: 16px;
		line-height: 1.2;
	}
	.object-manager-status {
		display: block;
		color: #999;
		font-size: 13px;
		margin-top: 5px;
	}
	.object-manager-meta {
		margin: 15px 0 0;
	}
	.object-manager-meta .meta_link {
		display: block;
		margin-bottom: 10px;
	}
	.object-manager-meta span.label {
		color: #ce3a12
	}
	.object-manager-timing {
		margin-top: 15px;
		font-size: 13px;
		color: #999;
	}
	.svg-logo {
		width: 50px;
	}
</style>

<div class="object">
	<table class="object-top">
		<tr>
			<td class="col-t left">
                <div class="header-logo">
					<span class="logo-image">
						<?php if ($logo) { ?>
							<img src="<?= $_SERVER["DOCUMENT_ROOT"] . $logo ?>" alt="">
						<?php } ?>
					</span>
	                <?if($showText):?>
		                <? $formatSiteName = preg_replace(
			                '#^([^\s]+?)\s(.*)$#',
			                '<b>$1</b><br>$2',
			                $siteName
		                )?>
		                <span class="logo-text"><?=$formatSiteName?></span>
	                <?endif;?>
                </div>
			</td>
			<td class="col-t right">
				<div class="header-phone-number"><?= SettingsTable::getValue("PHONE") ?></div>
			</td>
		</tr>
	</table>
	<hr>
	<div class="wrapper">
	<table class="object-content">
        <tr>
            <td colspan="2">
                <div class="content-title"><?= $arResult["NAME"] ?></div>
            </td>
        </tr>
        <tr class="row-wrapper">
            <td class="col-t">
                <div class="object-gallery">
                    <img src="<?= $images[0]["preview"]["src"] ?>">
				</div>
				<div class="object-gallery-previews">
					<?php foreach (array_slice($images, 0, 3) as $image)
					{
						?>
							<div class="object-gallery-preview">
								<img src="<?= $image["preview"]["src"] ?>">
							</div>
						<?php
					}
					?>
                </div>
            </td>
            <td class="col-t">
	            <?if ((string)$arResult['ADDRESS']) { ?>
                	<div class="object-address"><?= (string)$arResult['ADDRESS']?></div>
				<?php } ?>
				<?php if (!empty($arResult["PROPERTIES"]["cost"]["VALUE"])) { ?>
					<div class="object-price">
						<span class=""><?=number_format($arResult["PROPERTIES"]["cost"]["VALUE"], 0, ',', ' ')?></span>
						<span><?=$arResult["PROPERTIES"]["cost"]['CURRENCY']['SIGN']?></span>
					</div>
				<?php } ?>
                <div class="object-option">
					<?php
					$skipProperties = array("cost", "address", "photo", "contact");
					if (count($arResult["DISPLAY_PROPERTIES"]) > 0)
					{
						foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty)
						{
							if (array_search($pid, $skipProperties) !== false)
								continue;

							if ($arProperty["PROPERTY_TYPE"] == 'F')
							{
								if (!is_array($arProperty['VALUE'])) {
									$arProperty['VALUE'] = array($arProperty['VALUE']);
									$arProperty['DESCRIPTION'] = array($arProperty['DESCRIPTION']);
								}
								$arProperty["DISPLAY_VALUE"] = Array();
								foreach ($arProperty["VALUE"] as $idx=>$value) {
									$path = CFile::GetPath($value);
									$desc = strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? $arProperty["DESCRIPTION"][$idx] : bx_basename($path);
									if (strlen($path) > 0)
									{
										$ext = pathinfo($path, PATHINFO_EXTENSION);
										$fileinfo = '';
										if ($arFile = CFile::GetByID($value)->Fetch())
											$fileinfo .= ' (' . $ext . ', ' . round($arFile['FILE_SIZE']/1024) . GetMessage('FILE_SIZE_Kb') . ')';
										$arProperty["DISPLAY_VALUE"][] = "<a href=\"{$path}\" class=\"file file-{$ext}\">" . $desc . "</a>" . $fileinfo;
									}
								}
								$val = is_array($arProperty["DISPLAY_VALUE"]) ? implode(', ', $arProperty["DISPLAY_VALUE"]) : $arProperty['DISPLAY_VALUE'];
							}
							else
							{
								switch ($arProperty["CODE"])
								{
									case "cost":
										$arProperty["DISPLAY_VALUE"] = number_format($arProperty["VALUE"], 0, ',', ' ') . GetMessage("CITRUS_REALTY_CURRENCY");
										break;
									case "address":
										$arProperty["DISPLAY_VALUE"] .= '<div class="on-map"><a href="javascript:void(0)" class="map-link" data-address="' . $arProperty["VALUE"] . '">' . GetMessage("CITRUS_REALTY_ON_MAP") . '</a></div>';
										break;
								}

								if (!is_array($arProperty["DISPLAY_VALUE"]))
									$arProperty["DISPLAY_VALUE"] = Array($arProperty["DISPLAY_VALUE"]);

								array_map(function (&$v) {
									$v = strip_tags($v);
								}, $arProperty["DISPLAY_VALUE"]);

								// добавим обознвчение единиц для площади
								if (stripos($pid, 'land_area') !== false)
									foreach ($arProperty["DISPLAY_VALUE"] as &$val)
										$val .= GetMessage("CITRUS_REALTY_HUNDRED_SQR_METERS");
								elseif (stripos($pid, '_area') !== false)
									foreach ($arProperty["DISPLAY_VALUE"] as &$val)
										$val .= GetMessage("CITRUS_REALTY_SQR_METERS");

								$ar = array();
								foreach ($arProperty["DISPLAY_VALUE"] as $idx=>$value)
									$ar[] = $value . (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? ' (' . $arProperty["DESCRIPTION"][$idx] . ')': '');

								$val = implode(', ', $ar);
							}

							?>
							<div>
								<span><?= $arProperty["NAME"] ?></span>
								<span>:&nbsp;</span><span><?= $val ?></span>
							</div>
							<?
						}
					}
					?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="object-text"><?= $detailText ?></div>
            </td>
        </tr>
    </table>
	</div>
	<?php if (!empty($arResult["CONTACT"]))
	{
		if (empty($arResult["CONTACT"]['NAME']))
		{
			$arResult["CONTACT"]['NAME'] = SettingsTable::getValue('SITE_NAME');
		}
	?>
	<div class="wrapper">
		<table class="object-bottom">
			<tr>
				<td colspan="2" class="col-lt-size">
					<div class="bottom-title"><?=
						Loc::getMessage(isset($arResult['CONTACT']['ID']) ?
							"CITRUS_TEMPLATE_CONTACT_PERSON"
							: 'CITRUS_TEMPLATE_CONTACT_INFO')
					?></div>
				</td>
			</tr>
			<tr class="bottom-ava">
				<?php if (!empty($arResult["CONTACT"]["PREVIEW_PICTURE"]))
				{
					?>
					<td class="col-lt ava">
						<div class="object-manager-ava">
							<img src="<?= $_SERVER["DOCUMENT_ROOT"]
								. $arResult["CONTACT"]["PREVIEW_PICTURE"] ?>" alt="">
						</div>
					</td>
					<?php
				} ?>
				<td class="col-lt ava-info">
					<div class="object-manager-info">
						<div class="object-manager-name"><?= $arResult["CONTACT"]["NAME"] ?></div>
						<div class="object-manager-status"><?= $arResult["CONTACT"]["PROPERTY_POSITION_VALUE"] ?></div>
						<div class="object-manager-meta">
							<?php if (!empty($arResult["CONTACT"]["PROPERTY_EMAIL_VALUE"])) { ?>
								<div class="meta_link">
									<span><?= Loc::getMessage("CITRUS_AREALTY_PDF_SEND_ELEMENT_EMAIL") ?></span><span class="label"><?=
										$arResult["CONTACT"]["PROPERTY_EMAIL_VALUE"] ?></span>
								</div>
							<?php } ?>
							<?php if (!empty($arResult["CONTACT"]["PROPERTY_PHONE_VALUE"])) { ?>
								<div class="meta_link">
									<span><?= Loc::getMessage("CITRUS_AREALTY_PDF_SEND_ELEMENT_PHONE") ?></span><span class="label"><?=
										is_array($arResult["CONTACT"]["PROPERTY_PHONE_VALUE"])?
											implode(", ", $arResult["CONTACT"]["PROPERTY_PHONE_VALUE"])
											: $arResult["CONTACT"]["PROPERTY_PHONE_VALUE"] ?></span>
								</div>
							<?php } ?>
						</div>
						<div class="object-manager-timing">
							<?php if (!empty($arResult["CONTACT"]["PROPERTY_SCHEDULE_VALUE"])) { ?>
								<?php foreach ($arResult["CONTACT"]["PROPERTY_SCHEDULE_VALUE"] as $i => $v) { ?>
									<?= $arResult["CONTACT"]["PROPERTY_SCHEDULE_DESCRIPTION"] ?>:
										<?= $arResult["CONTACT"]["PROPERTY_SCHEDULE_VALUE"] ?><br>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?php } ?>
</div>
