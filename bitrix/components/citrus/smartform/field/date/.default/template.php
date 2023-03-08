<?/**
 * @var $fieldInfo
 * [data-min],
 * [data-max],
 * [data-format] : L = date, LT = datetime
 * */?>
<?$dateFormat = $fieldInfo["USER_TYPE"] == "DateTime" ? "L LT" : "L";?>
<input class="form-control" readonly="readonly" type="text" name="<?=$fieldInfo["CODE"]?>" value="<?=$fieldInfo["OLD_VALUE"]?>" data-format="<?=$dateFormat?>" id="<?=$fieldInfo["ID"]?>"/>
<i class="fa fa-calendar calendar-icon" aria-hidden="true"></i>


<? CJSCore::Init('bootstrap_datetimepicker')?>
<script>
    cui_form.initPlugins($('#<?=$fieldInfo["ID"]?>'), "datetimepicker");
</script>