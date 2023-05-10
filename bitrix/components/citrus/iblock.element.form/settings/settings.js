function OnCitrusIBlockElementFormSettingsEdit(arParams) {
	if(false === BX.type.isNotEmptyString(arParams.propertyParams.JS_COMPONENT_PATH))
		return false;

	if (arParams.propertyParams.JS_DATA) {
		BX.message(arParams.propertyParams.JS_DATA);
	}

	var componentPath = arParams.propertyParams.JS_COMPONENT_PATH;

	BX.loadCSS('//maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css');
	BX.loadScript(
		[
			'//ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js',
			componentPath + '/settings/angular/drag-and-drop/angular-drag-and-drop-lists.min.js',
			componentPath + '/settings/angular/app.js'
		],
		BX.delegate(function() {
			var btn = document.createElement('setting-btn');
			btn.setAttribute('src',componentPath);
			arParams.oCont.appendChild(btn);
			arParams.dialog = BX.WindowManager.Get();

			angular.module('smartform.settings').value('$arParams',arParams);
			angular.module('smartform.settings.btn').value('$arParams',arParams);
			angular.bootstrap(arParams.oCont, ['smartform.settings.btn']);
		}, this)
	);
};
