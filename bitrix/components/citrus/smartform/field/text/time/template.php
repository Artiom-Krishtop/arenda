<?/**
 * @var $fieldInfo
 * [data-min],
 * [data-max],
 * [data-format] : L LT = date, LT = time
 * */?>
<input class="form-control" readonly="readonly" type="text" name="<?=$fieldInfo["CODE"]?>" value="<?=$fieldInfo["OLD_VALUE"]?>" data-format="LT" id="<?=$fieldInfo["ID"]?>"/>
<i class="fa fa-calendar calendar-icon" aria-hidden="true"></i>

<? CJSCore::Init('bootstrap_datetimepicker')?>
<script>
    cui_form.initPlugins($('#<?=$fieldInfo["ID"]?>'), "datetimepicker", {
	    showClose: true,
    });
</script>