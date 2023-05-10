<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
//delayed function must return a string
if (empty($arResult))
{
	return "";
}

$strReturn = '<div class="nav"><div class="w">';

$strReturn .= '<div class="nav-panel">';
$strReturn .= '<div class="nav-breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">';

$rsSites = CSite::GetByID(SITE_ID);
$arSite = $rsSites->Fetch();
$siteName = $arSite['SITE_NAME'];
/*
$strReturn = $strReturn . '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                <a class="breadcrumbs-item" href="' . SITE_DIR . '" itemprop="url">
                                    <span class="btn-label" itemprop="name">' . GetMessage("CITRUS_AREALTY3_BREADCRUBM_MAINLINK") .'</span>
                                </a>
                                <meta itemprop="position" content="1" />
                            </span>';
*/
foreach ($arResult as $idx => $item)
{
	/* $strReturn .= '<span class="btn-icont">-</span>'; */

	$title = htmlspecialcharsex($item["TITLE"]);
	if ($APPLICATION->GetCurPage() == $item["LINK"])
	{
		$strReturn = $strReturn . '<span class="breadcrumbs-item-current">
                                            <span class="btn-label">' . $title . '</span>
                                        </span>';
	}
	elseif ($item["LINK"] <> "" && $idx != count($arResult) - 1)
	{
		$strReturn = $strReturn . '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                            <a href="' . $item["LINK"] . '" itemprop="url" class="breadcrumbs-item">
                                                <span itemprop="name" class="btn-label">' . $title . '</span>
                                            </a>
                                            <meta itemprop="position" content="' . ($idx + 2) . '" />
                                        </span> - ';
	}
	else
	{
		$strReturn = $strReturn . '<span class="breadcrumbs-item-current">' .
			'<span class="btn-label">' . $title . '</span>' .
			'</span>';
	}
}
$strReturn .= '</div>'; //.nav-breadcrumbs
$strReturn .= '</div>'; // .nav-panel
$strReturn .= '</div>'; // .w, .nav
$strReturn .= '</div>'; // .nav

return $strReturn;

