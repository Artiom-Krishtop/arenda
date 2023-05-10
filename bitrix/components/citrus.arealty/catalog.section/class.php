<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CitrusArealtyCatalogSectionComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		$this->includeComponentTemplate();

		return $this->arResult['RETURN_VALUE'];
	}
}