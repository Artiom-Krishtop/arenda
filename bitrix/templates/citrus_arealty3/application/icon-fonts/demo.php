<?php

use Spatie\HtmlElement\HtmlElement;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$iconsCss = file_get_contents(__DIR__ . '/icons.css');
if (preg_match_all('/\.(icon-[\w-]+)/m', $iconsCss, $matches))
{
	$icons = '';
	foreach ($matches[1] as $iconClass)
	{
		$icons .= HtmlElement::render('.icon__block.col-sm-2.col-md-3', [
			HtmlElement::render('span.icon.' . $iconClass),
			HtmlElement::render('span.icon__text', '.' . $iconClass),
		]);
	}

	echo HtmlElement::render('.row.icons', $icons);
	?>
	<style>
		.icon__block {
			text-align: center;
		}
		.icon__block .icon {
			font-size: 2em;
		}
		.icon__text {
			display: block;
			margin: .5em 0;
		}
	</style>
	<?
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");