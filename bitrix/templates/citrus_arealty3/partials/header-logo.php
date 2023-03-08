<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Citrus\Arealty\Entity\SettingsTable;

/** @var \Citrus\Core\Theme $theme */

$classes = [
	'header-logo',
	SettingsTable::getValue('LOGO_SHOW_TEXT') === 'Y' ? "with_desc" : "",
];

$logoSize = SettingsTable::getValue('LOGO_SHOW_TEXT') === 'Y'
	? [240, 70]
	: [70, 70];

?>
<a href="<?=SITE_DIR?>" data-settings="LOGO_SHOW_TEXT"
   class="<?=implode(' ', array_filter($classes))?>"
>
    <span class="logo-image logo-image--min">
        <img data-settings="SCHEME_LOGO"
             data-settings-rel="logo"
             src="<?=SettingsTable::getLogoPath($logoSize) ?: $theme->getPath() . 'logo.png'?>"
             alt="">
    </span>

	<span class="logo-text">
	    <?=preg_replace(
		    '#^([^\s]+?)\s(.*)$#',
		    '
			<span 
                data-settings="SITE_NAME"
                data-settings-rel="text1"
                class="logo-text__first"
            	>$1</span>
			<span 
				data-settings="SITE_NAME"
	            data-settings-rel="text2"
				class="logo-text__second theme--color"
				>$2</span>',
		    SettingsTable::getValue('SITE_NAME')
	    )?>
	</span>
</a>
