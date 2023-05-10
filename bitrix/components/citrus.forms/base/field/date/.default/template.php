<?/**
 * @var $fieldInfo
 * [data-min],
 * [data-max],
 * [data-format] : L = date, LT = datetime
 * */

array_push($templateData['cjscore'], 'bootstrap_datetimepicker');

?>
<?$dateFormat = $fieldInfo["USER_TYPE"] == "DateTime" ? "L LT" : "L";?>
<input class="form-control" readonly="readonly" type="text" name="<?=$fieldInfo["CODE"]?>" value="<?=$fieldInfo["OLD_VALUE"]?>" id="<?=$fieldInfo["ID"]?>"/>
<i class="fa fa-calendar calendar-icon" aria-hidden="true"></i>

<script>
	;(function () {
		$('#<?=$fieldInfo["ID"]?>').datetimepicker({
			icons: {
				time: 'fa fa-clock-o',
				date: 'fa fa-calendar',
				up: 'fa fa-chevron-up',
				down: 'fa fa-chevron-down',
				previous: 'fa fa-chevron-left',
				next: 'fa fa-chevron-right',
				today: 'fa fa-compass',
				clear: 'fa fa-trash',
				close: 'fa fa-remove'
			},
			ignoreReadonly: true,
			format: "<?=$dateFormat?>",
			//minDate: 0,
			//maxDate: 0,
			toolbarPlacement: 'bottom',
			showClose: true, //������� ����
			showClear: true, //�������� ����
			//toolbarPlacement: 'top', //������������ ���� �� ��������
			//debug: true //�� ��������� ���� �� ��������
		})
		.on("dp.change", function(e){
			$(this).trigger("validate");
		});
	}());
</script>

<?
/* ��������� �� ��������, �������� ���� �����������
global $APPLICATION;
$APPLICATION->IncludeComponent(
	'bitrix:main.calendar',
	'',
	array(
		'FORM_NAME' => $arResult['FORM_ID'],
		'INPUT_NAME' => $fieldInfo["CODE"],
		'INPUT_VALUE' => $fieldInfo['OLD_VALUE'],
		'SHOW_INPUT' => 'Y'
	),
	null,
	array('HIDE_ICONS' => 'Y')
);*/
?>