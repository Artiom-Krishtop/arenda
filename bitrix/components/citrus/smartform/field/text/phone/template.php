<input id="<?=$fieldInfo["ID"]?>" class="form-control" type="text" name="<?=$fieldInfo["CODE"]?>" value="<?=$fieldInfo["OLD_VALUE"]?>" placeholder="<?=$fieldInfo['PLACEHOLDER']?>" maxlength="255"/>

<script>
    cui_form.initPlugins($("#<?=$fieldInfo["ID"]?>"), 'phoneMask');
</script>
<? CJSCore::Init('maskedinput')?>