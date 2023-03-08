<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();?>

<section class="account-content">
	<section class="account-content__inner account-content__inner-visible">
		<?
			$APPLICATION->IncludeComponent(
				"bitrix:breadcrumb",
				"account",
				Array(
					"START_FROM" => "0", 
					"PATH" => "", 
					"SITE_ID" => SITE_ID 
				)
			);
		?>
		<h1 class="account-content__title"><?= GetMessage('PERSONAL_DATA_TITLE') ?></h1>
		<form class="form" method="post" action="<?=$arResult["FORM_TARGET"]?>">
			<?=$arResult["BX_SESSION_CHECK"]?>
			<input type="hidden" name="lang" value="<?=LANG?>" />
			<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
			<? ShowError($arResult["strProfileError"]);?>
			<? if ($arResult['DATA_SAVED'] == 'Y')
				ShowNote(GetMessage('PROFILE_DATA_SAVED'));
			?>
			<div class="form__inner">
				<div class="form__column">
					<div class="form__item form__item--edit">
						<label class="form__label" for="login"><?=GetMessage('LOGIN')?></label>
						<input class="form__input" type="text" value="<?= $arResult["arUser"]["LOGIN"]?>" name="LOGIN" id="login">
					</div>
					<div class="form__item form__item--edit">
						<label class="form__label" for="fio"><?=GetMessage('NAME')?></label>
						<input class="form__input" type="text" value="<?= $arResult["arUser"]["NAME"]?>" name="NAME" id="fio">
					</div>
					<div class="form__item form__item--edit">
						<label class="form__label" for="org_name"><?=GetMessage('USER_COMPANY')?></label>
						<input class="form__input" type="text" value="<?= $arResult["arUser"]["WORK_COMPANY"]?>" name="WORK_COMPANY" id="org_name">
					</div>
					<div class="form__item form__item--edit">
						<label class="form__label" for="tel"><?=GetMessage('USER_PHONE')?></label>
						<input class="form__input js-phone-mask" type="text" value="<?= $arResult["arUser"]["PERSONAL_PHONE"]?>" name="PERSONAL_PHONE" id="tel">
					</div>
				</div>
				<div class="form__column">
					<div class="form__item form__item--edit">
						<label class="form__label" for="email"><?=GetMessage('EMAIL')?></label>
						<input class="form__input" type="EMAIL" value="<?= $arResult["arUser"]["EMAIL"]?>" name="EMAIL" id="email">
					</div>
					<div class="form__item form__item--password">
						<span class="form__item--password-icon"></span>
						<label class="form__label" for="password"><?=GetMessage('NEW_PASSWORD_REQ')?></label>
						<input class="form__input" type="password" value="" name="NEW_PASSWORD" id="password">
						<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
					</div>
					<div class="form__item form__item--password">
						<span class="form__item--password-icon"></span>	
						<label class="form__label" for="password"><?=GetMessage('NEW_PASSWORD_CONFIRM')?></label>
						<input class="form__input" type="password" value="" name="NEW_PASSWORD_CONFIRM" id="confirm-password">
					</div>
				</div>
			</div>
			<input class="form__btn account-content__btn" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>">
		</form>
	</section>
</section>