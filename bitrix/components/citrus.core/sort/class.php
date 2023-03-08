<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;


class CitrusSort extends CBitrixComponent {

	protected function getSortList(){
		global $APPLICATION;

		$arFields = array();
		if ($this->arParams["SORT_FIELDS"]) {
			$arFields = $this->arParams["SORT_FIELDS"];
		} elseif($this->arParams["SORT_FIELD_CODE"]) {
			foreach ($this->arParams["SORT_FIELD_CODE"] as $key => $fieldCode) {
				if (!strlen($fieldCode)) continue;
				$arFields[$fieldCode] = $this->arParams["SORT_FIELD_NAMES"][$key];
			}
		}

		// get sort & order
		$request = Application::getInstance()->getContext()->getRequest();

		$sort_order = array("DESC", "ASC");
		$get_sort = htmlspecialcharsEx(stripslashes(trim($request->get("sort"))));
		$get_order = htmlspecialcharsEx(stripslashes(trim($request->get("order"))));

		if (!$get_order) $get_order = $this->arParams["DEFAULT_SORT_ORDER"];
		if (!$get_sort) $get_sort = ($this->arParams["DEFAULT_SORT_FIELD"]) ?: key($arFields);

		$this->arResult["SORT"] = $get_sort;
		$this->arResult["ORDER"] = $get_order;

		$sortItems = array();
		foreach ( $arFields as $fieldCode => $fieldName) {
			$isSelected = (bool) ($fieldCode == $get_sort);
			$arFieldSort = array(
				"NAME" => $fieldName,
				"CODE" => $fieldCode,
				"SELECTED" => $isSelected,
				"REVERSE_SORTING_LINK" => '',
				"LINKS" => array()
			);

			foreach ($sort_order as $order) {
				$isLinkSelected = (bool)($isSelected && $order == $get_order);
				$arLink = array(
					"HREF" => $APPLICATION->GetCurPageParam("sort={$fieldCode}&order={$order}", array("sort", "order")),
					"ORDER" => $order,
					"SELECTED" => $isLinkSelected
				);
				$arFieldSort["LINKS"][] = $arLink;
				if (!$isLinkSelected && !$arFieldSort['REVERSE_SORTING_LINK']) $arFieldSort['REVERSE_SORTING_LINK'] = $arLink['HREF'];
			}
			$sortItems[$fieldCode] = $arFieldSort;
		}

		return $sortItems;
	}

	protected function getViewList(){
		global $APPLICATION;

		$arViewList = array_filter ($this->arParams["VIEW_LIST"] , function ($code){
			return strlen($code) > 0;
		});
		$request = Application::getInstance()->getContext()->getRequest();

		$getView = htmlspecialcharsEx(stripslashes(trim($request->get("view"))));
		if (!$getView || !in_array($getView, $arViewList)) $getView = $this->arParams["VIEW_DEFAULT"];
		$this->arResult["VIEW"] = $getView;

		$viewItems = array();
		foreach ($arViewList as $viewCode) {
			$viewItems[] = array(
				"CODE" => $viewCode,
				"SELECTED" => (bool) ($viewCode == $getView),
				"HREF" => $APPLICATION->GetCurPageParam("view={$viewCode}", array("view")),
			);
		}

		return $viewItems;
	}

	public function executeComponent() {
		if($this->startResultCache()) {
			$this->arResult["SORT_ITEMS"] = $this->getSortList();
			$this->arResult["VIEW_ITEMS"] = $this->getViewList();

			$this->includeComponentTemplate();
		}
		return array(
			"SORT" => array(
				"CODE" => $this->arResult["SORT"],
				"ORDER" => $this->arResult["ORDER"],
			),
			"VIEW" => $this->arResult["VIEW"]
		);
	}
}