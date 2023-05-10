<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
if(!$arParams["MIN_LETTER_COUNT"]) $arParams["MIN_LETTER_COUNT"] = 0;
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);?>


<div id="<?echo $CONTAINER_ID?>" class="">
    <form  class="header-search js-search is-empty" autocomplete="off">
        <div class="search-extra">
            <input type="text" placeholder="<?=GetMessage("CT_BST_SEARCH_PLACEHOLDER")?>" id="<?echo $INPUT_ID?>" value="<?htmlspecialcharsbx($_REQUEST["q"])?>" name="q" autocomplete="off">
            <div class="spinner">
                <span class="bounce1"></span>
                <span class="bounce2"></span>
                <span class="bounce3"></span>
            </div>
            <button class="btn btn-link btn-header-search" type="submit" name="s" tabindex="-1">
                <span class="btn-icon fa fa-search"></span>
            </button>
            <button class="btn btn-link btn-header-search-cancel js-search-cancel">
                <span class="btn-icon fa fa-times"></span>
            </button>
        </div>
        <div class="search-result">
            <div class="search-result-description">
                <?=GetMessage("CT_BST_SEARCH_DESCRIPTION", array("#NUM#" => $arParams["MIN_LETTER_COUNT"]));?>
            </div>
            <div class="search-no-result js-search-result-no hidden">
                <span><?=GetMessage("CT_BST_SEARCH_EMPTY_RESULT");?></span>
            </div>
            <div class="js-search-result-yes">
                <div class="search-result-list js-search-result-list"></div>
            </div>
        </div>
    </form>
</div>
<script>
    $(function(){
        new citrusLiveSearch({
            'AJAX_PAGE': '<?=CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
            'CONTAINER_ID': '<?=$CONTAINER_ID?>',
            'INPUT_ID': '<?=$INPUT_ID?>',
            'MIN_QUERY_LEN': <?=$arParams["MIN_LETTER_COUNT"]?>,
            "DELAY": 300
        });
    });
</script>
