<? /**
 * @var $arItem
 */
use Bitrix\Main\Localization\Loc;
use Citrus\Arealty\Helper;

$arItem['lang'] = array(
	'from' => Loc::getMessage('CT_BCSF_FILTER_FROM'),
	'to' => Loc::getMessage('CT_BCSF_FILTER_TO')
);

if ($arItem['CODE'] === 'cost') {
	$currencyIcon = '<span class="currency-icon" data-currency-icon=""></span>';
	$arItem['NAME'] .= '<span data-currency-factor=""></span>';
}
?>

<div class="citrus-sf-label" onclick="smartFilter.toggleValues(this, event)">
	<span class="citrus-sf-label_name"><?=$arItem["NAME"]?></span><?=$arItem['HINT'] ? ', '.$arItem['HINT'] : ''?>
	<span class="citrus-sf-label_value"></span>
	<span class="citrus-sf-label_close"><i class="icon-close" aria-hidden="true"></i></span>
</div>

<div class="citrus-sf-values">
	<div class="filter-numbers" data-property-id="<?=$arItem['ID']?>">
	    <?if($arItem['DISPLAY_TYPE'] == 'A'):?>
	        <div class="filter-slider">
	            <input type="hidden" class="range-slider-input" >
	        </div><!-- .filter-slider -->
		    <?CJSCore::Init(array('rangeSlider'));?>
	    <?endif;?>

	    <div class="filter-numbers_input-wrap">
	        <input
	                class="filter-numbers_input _min"
	                type="text"
	                name="<? echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"] ?>"
	                id="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"] ?>"
	                value=""
	                size="12"
	                data-template = "<?=$arItem['TEMPLATE']['NAME']?>"
	                data-real-value = ""
	        />
	        <input
	                class="filter-numbers_input _max"
	                type="text"
	                name="<? echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"] ?>"
	                id="<? echo $arItem["VALUES"]["MAX"]["CONTROL_ID"] ?>"
	                value=""
	                size="12"
	                data-template = "<?=$arItem['TEMPLATE']['NAME']?>"
	                data-real-value = ""
	        />
	    </div>
		<?CJSCore::Init(array('autoNumeric'));?>
	</div>

	<script>
	    ;(function(){
	        new citrusSmartFilterNumbers(<?=\Bitrix\Main\Web\Json::encode($arItem);?>);
	    }());
	</script>
</div>