<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

if($USER->IsAuthorized()){
    $userData = CUser::GetById($USER->GetId())->fetch();
    
    $arFilter = array(
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'PROPERTY_RENTAL_COMPANY' => intval($userData['UF_RENTAL_COMPANY']),
        array(
            'LOGIC' => 'OR',
            array('ACTIVE' => 'N'),
            array('!ACTIVE_DATE' => 'Y')
        )
    );
    
    $dbRes = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, array('*'));

    while ($res = $dbRes->Fetch()) {
        $dbPropRes = CIBlockElement::GetProperty($res['IBLOCK_ID'], $res['ID']);

        while ($propRes = $dbPropRes->Fetch()) {
            $res['PROPERTIES'][$propRes['CODE']] = $propRes;
        }

        $arResult['ITEMS'][] = $res;
    }
}

