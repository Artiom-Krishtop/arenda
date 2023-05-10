function OnCitrusSmartFormSettingsEdit(arParams) {
	if(false === BX.type.isNotEmptyString(arParams.propertyParams.JS_COMPONENT_PATH))
		return false;

	var componentPath = arParams.propertyParams.JS_COMPONENT_PATH;
	BX.loadCSS('//maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css');
	BX.loadCSS(componentPath + '/settings/settings.css');
	BX.loadScript(
		[
			'//ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js',
			componentPath + '/settings/angular/drag-and-drop/angular-drag-and-drop-lists.min.js',
			componentPath + '/settings/angular/translate/angular-translate.min.js',
			componentPath + '/settings/angular/app.js'
		],
		BX.delegate(function() {
			var btn = document.createElement('setting-btn');
			btn.setAttribute('src',componentPath);

			arParams.oCont.innerHTML = '<setting-btn src="' + componentPath + '"></setting-btn>';
			arParams.dialog = BX.WindowManager.Get();

			angular.module('smartform.settings').value('$arParams',arParams);
            angular.module('smartform.settings').value('$msg',arParams.propertyParams.MESSAGE);
			angular.module('smartform.settings').config([
				'$translateProvider',
				function ($translateProvider) {
					$translateProvider.translations('ru', arParams.propertyParams.MESSAGE);
					$translateProvider.preferredLanguage('ru');
				}
			]);
			angular.module('smartform.settings.btn').value('$arParams',arParams);
            angular.module('smartform.settings.btn').value('$msg',arParams.propertyParams.MESSAGE);
			angular.module('smartform.settings.btn').value('$formValue',arParams.propertyParams.JS_FORM_DATA);
			angular.bootstrap(arParams.oCont, ['smartform.settings.btn']);
		}, this)
	);
};

