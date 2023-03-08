<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $USER;

?>

<nav class="header-auth">
	<a href="<?=SITE_DIR?>kabinet/"
	   class="header-btn header-auth__icon"
	   aria-label="<?= Loc::getMessage("CITRUS_AREALTY3_PERSONAL") ?>"
	>
		<i class="icon-user" aria-hidden="true"></i>
	</a>
	<?php

	if ($USER->IsAuthorized())
	{
		?>
		<a href="?logout=yes"
		   class="header-auth__link"><?= Loc::getMessage("CITRUS_AREALTY3_LOGOUT") ?></a>
		<?php
	}
	else
	{
		?>
		<a href="<?=SITE_DIR?>kabinet/"
		   class="header-auth__link"><?= Loc::getMessage("CITRUS_AREALTY3_LOGIN") ?></a>
		<?php
	}

	?>
</nav><!-- .header-auth -->

