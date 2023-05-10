<?php

/**
 * @var $fieldInfo
 */

array_push($templateData['cjscore'], 'maskedinput');

//������ ������� ��������� ��� ���������� ������ � �������� �����
$fieldInfo['ADDITIONAL']['trigger'] = 'change';
?>
<input id="<?=$fieldInfo["ID"]?>" class="form-control" type="text" name="<?=$fieldInfo["CODE"]?>" value="<?=$fieldInfo["OLD_VALUE"]?>" placeholder="<?=$fieldInfo['PLACEHOLDER']?>" maxlength="255"/>

<script>
	;(function () {
	    $("#<?=$fieldInfo["ID"]?>").mask("+375 (99) 999-99-99");
    }());
</script>
