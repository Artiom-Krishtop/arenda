<?/**
 * @var $fieldInfo
 * [data-min],
 * [data-max],
 * [data-format] : L LT = date, LT = time
 * */

array_push($templateData['cjscore'], 'bootstrap_datetimepicker');

?>
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
		    format: "LT",
		    //minDate: 0,
		    //maxDate: 0,
		    toolbarPlacement: 'bottom',
		    showClose: true, //закрыть окно
		    //showClear: true, //очистить поле
		    //toolbarPlacement: 'top', //расположение поля со значками
		    //debug: true //не закрывает окно по фокусаут
	    })
	    .on("dp.change", function(e){
		    $(this).trigger("validate");
	    });
    }());
</script>