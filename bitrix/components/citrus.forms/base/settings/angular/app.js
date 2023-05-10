(function (windows, angular) {
	'use strict';

	angular.module('smartform.settings.directive',[])
		.directive('validNumber', function() {
			return {
				require: '?ngModel',
				link: function(scope, element, attrs, ngModelCtrl) {

					element.bind('keypress', function(event) {
						var chr = String.fromCharCode(event.keyCode);
						if (chr < '0' || chr > '9')
							return false;

						var clean = ngModelCtrl.$modelValue;
						clean = parseInt(clean) <= 0 ? 1 : parseInt(clean);

						ngModelCtrl.$setViewValue(clean);
						ngModelCtrl.$render();
					});

					element.bind('change', function(event) {
						if(
							null === ngModelCtrl.$modelValue || ngModelCtrl.$modelValue <= 0
						) {
							ngModelCtrl.$setViewValue(1);
							ngModelCtrl.$render();
						}
					});
				}
			};
		})
		.directive('settingBtn', [
			'$q','$arParams','$msg','$templateCache','$rootScope','$http',
			function($q,$arParams,$msg,$templateCache,$rootScope,$http) {
				return {
					restrict: 'E',
					replace: true,
					transclude: true,
					template: function(element, attrs) {
						return '<input ' +
									'type="button" ' +
									'class="adm-btn-save" ' +
									'ng-click="showSettingDialog(\'' + attrs.src + '\')" ' +
									'value="' + $msg.SETTINGS_BTN + '" ' +
								'/>'
						;
					},
					controller:['$scope',function($scope) {
						var _this = {};

						_this.dialog = null;

						_this.loadTemplateUrl = function(tmpl, config) {
							$rootScope.$broadcast('dialog.templateLoading', tmpl);
							return $http.get(tmpl, (config || {})).then(function(res) {
								$rootScope.$broadcast('dialog.templateLoaded', tmpl);
								return res.data || '';
							});
						};

						_this.loadTemplate = function(tmpl) {
							return _this.loadTemplateUrl(tmpl, {cache: $templateCache});
						};

						$scope.showSettingDialog = function(url) {
							$q.all({
								template: _this.loadTemplate(url + '/settings/template.html')
							}).then(function(respons) {
								var params = {
									title: $msg.SETTINGS_FORM_TITLE,
									content: respons.template,
									height: 600,
									width: 800,
									draggable: true,
									resizable: true,
									min_height: 500,
									min_width: 500,
								};

								if(null === _this.dialog) {
									_this.dialog = new BX.CDialog(params);
									_this.dialog.Show();
									angular.bootstrap(_this.dialog.PARTS.CONTENT, ['smartform.settings']);
								}
								else {
									_this.dialog.Show();
								}
							});
						};
					}]
				}
			}])
		.directive('fieldDetail', ['$templateCache','$msg', function($templateCache,$msg) {
			return {
				scope: {
					field: '=fieldData',
					group : '=fieldGroup'
				},
				restrict: 'E',
				replace: true,
				transclude: true,
				template: function(element, attrs) {
					return $templateCache.get('fieldDetailTemplate.html');
				},
				controller:['$scope',function($scope) {
					$scope.validrultab = false;
					$scope.newRule = "";
					$scope.rules = {
						email: {title:'email'},
						file: {title:'file'},
						important: {title:$msg.SETTINGS_FIELD_IMPORTANT},
						length: {title:$msg.SETTINGS_FIELD_LENGTH},
						main_password: {title:$msg.SETTINGS_FIELD_MAIN_PASSWORD},
						confirm_password: {title:$msg.SETTINGS_FIELD_CONFIRM_PASSWORD},
						number: {title:$msg.SETTINGS_FIELD_NUMBER},
						phone: {title:$msg.SETTINGS_FIELD_PHONE},
						url: {title:$msg.SETTINGS_FIELD_LINK},
					};

					if(false === angular.isUndefined($scope.field.VALIDRULE)) {
						var arSelectedRule = $scope.field.VALIDRULE.split('|');
						angular.forEach(arSelectedRule,function(ruleCode,index) {
							if($scope.rules.hasOwnProperty(ruleCode))
								$scope.rules[ruleCode].selected = true;
						})
					}

					/**
					 * функйия которая позволяет получить список доступных правил валидации
					 */
					$scope.setAvilibalValidateRul = function() {
						this.rule.selected = !this.rule.selected;

						var curRule = []
						angular.forEach($scope.rules,function(val, index) {
							if(val.selected)
								$scope.field.VALIDRULE = curRule.push(index);
						});

						$scope.field.VALIDRULE = curRule.join('|');
					};

					$scope.addNewRuleItem = function() {
						if(angular.isUndefined(this.newRule) || this.newRule == "")
							return false;

						if(!$scope.rules.hasOwnProperty(this.newRule)) {
							$scope.rules[this.newRule] = {
								title: this.newRule,
								selected: true
							};

							this.newRule = '';
						};
					};
				}]
			}
		}])
		.directive('field', [
			'$templateCache', '$activeField','$rootScope','$compile',
			function($templateCache,$activeField,$rootScope,$compile) {
				return {
					scope: {
						field: '=fieldData',
						index: '=fieldIndex',
						group : '=fieldGroup'
					},
					restrict: 'E',
					replace: true,
					transclude: true,
					template: function(element, attrs) {
						return $templateCache.get('fieldTemplate.html');
					},
					controller:['$scope',function($scope) {
						$scope.removeField = function() {
							var field = angular.copy($scope.field);

							field.selected = false;

							$rootScope.models.show.items.splice(this.$parent.index,1);
							$rootScope.models.hide.items.push(field);

							if(this.field.id == $activeField.getActiveField()) {
								$activeField.setDefaultValue();
							}
						};

						$scope.setFieldChecked = function() {
							$activeField.setActiveField(this.field.id);
						};
					}]
				}
			}
		]);

	angular.module('smartform.settings.controller',['dndLists'])
		.controller('settingsFormCtrl',[
			'$arParams','$msg','$scope','$http','$q','$rootScope','$activeField','$filter',
			function($arParams,$msg,$scope,$http,$q,$rootScope,$activeField,$filter) {
				var _this = this;


				_this.arSaveData = {};
				_this.dialog = $arParams.dialog;

				_this.prepeaData = function() {
					angular.forEach($rootScope.models.show.items,function(item) {
						var tmpItem = angular.copy($scope.arField[item.id]);
						tmpItem.ACTIVE = true;
						_this.arSaveData[item.id] = _this.prepeaFieldData(tmpItem);
					});

					BX.onCustomEvent('public.event:onSaveFormDialogSettings',[angular.copy(_this.arSaveData)]);
				};

				$rootScope.$on('dialog.settings:onSaveForm',function() {
					_this.prepeaData();
				});

				_this.prepeaFieldData = function(item) {
					if(true == item.hasOwnProperty('id'))
						delete item.id;

					if(true == item.hasOwnProperty('selected'))
						delete item.selected;
					return item;
				};

				_this.componentPath = $arParams.propertyParams.JS_COMPONENT_PATH;

				_this.prepeaOnBeforeGetField = function(field) {
					angular.forEach(field,function(value, index) {
						value.IS_REQUIRED = (undefined != value.IS_REQUIRED && value.IS_REQUIRED == 'Y' ? true : false);
						value.HIDE_FIELD = (undefined != value.HIDE_FIELD && value.HIDE_FIELD == 'Y' ? true : false);
						value.ACTIVE = (undefined == value.ACTIVE || value.ACTIVE == 'Y' ? true : false);

						if(!angular.isUndefined(value.GROUP_FIELD) && value.GROUP_FIELD == 'Y' ) {
							value.GROUP_FIELD = true;
							if(!angular.isUndefined(value.DEPTH_LAVEL))
								value.DEPTH_LAVEL = parseInt(value.DEPTH_LAVEL);
							else
								value.DEPTH_LAVEL = 1;
						}
					});
					return field;
				};

				_this.getFields = function() {
					var deferred = $q.defer();
					var arP = angular.copy($arParams.data);
					arP = _this.prepeaOnBeforeGetField(arP);
					deferred.resolve(arP);
					return deferred.promise;
				};

				_this.getFields()
					.then(function($respons) {
						angular.forEach($respons, function(item, key) {
							item.id = key
							if(angular.isUndefined(item.ACTIVE) || true === item.ACTIVE)
								$rootScope.models.show.items.push(item);
							else
								$rootScope.models.hide.items.push(item);

							$respons[key] = item;
						});
						$scope.arField = $respons;
						$activeField.setDefaultValue();
					})
					.catch(function(arError) {
						$scope.error = arError.message;
					});

				$scope.path = "";
				$scope.error = false;
				$scope.moveElement = false;

				$scope.arField = {};

				$rootScope.models = {
					show: {
						dragging: false,
						items: []
					},
					hide: {
						dragging: false,
						items: []
					},
				};

				$scope.addNewGroup = function() {
					var newGroupId = 'group_' + Date.now();
					var newGroup = {
						ORIGINAL_TITLE: $msg.SETTINGS_F_NEW_GROUP + ' (' + newGroupId + ')',
						TITLE: $msg.SETTINGS_F_NEW_GROUP,
						GROUP_FIELD: true,
						selected: false,
						DEPTH_LAVEL: 1,
						id: newGroupId
					};

					$scope.arField[newGroup.id] = newGroup;
					$rootScope.models.show.items.push(newGroup);
				};

				$scope.showHideFieldForm = function() {
					if(false === $activeField.getActiveField())
						$activeField.setDefaultValue();
					else
						$activeField.setActiveField(false);
				};

				$scope.addField = function() {
					var field = angular.copy(this.hideField);

					$rootScope.models.show.items.push(field);
					$rootScope.models.hide.items.splice(this.$index,1);
				};

				$scope.onMoved = function(list) {
					list.items = $filter('filter')(list.items,function(item) {
						return !item.selected;
					});
				};

				$scope.getSelectedItemsIncluding = function(list, item) {
					item.selected = true;
					$scope.moveElement = $filter('filter')(list.items,function(item) {
						return item.selected;
					});
					return $scope.moveElement;
				};

				$scope.onDrop = function(list,items,index) {

					var newItem = angular.copy(items);
					angular.forEach(newItem, function(item) {
						item.selected = false;
					});

					list.items = list.items.slice(0, index)
						.concat(newItem)
						.concat(list.items.slice(index));
					return true;
				};

				$scope.onDragstart = function(list, event) {
					list.dragging = true;
				};

				$scope.show = function() {
					return angular.equals([],$rootScope.models.hide.items);
				};
			}
		]);

	angular.module('smartform.settings.service',[])
		.service('$activeField',['$rootScope',function($rootScope) {
			var _this = this;

			$rootScope.curFieldIndex = false;

			_this.setActiveField = function(index) {
				$rootScope.curFieldIndex = index;
			};

			_this.getActiveField = function() {
				return $rootScope.curFieldIndex;
			};

			_this.setDefaultValue = function() {
				if(false == angular.isUndefined($rootScope.models.show.items[0]))
					_this.setActiveField($rootScope.models.show.items[0].id);
				else
					_this.setActiveField(false);
			};

			return _this;
		}]);

	angular.module('smartform.settings',
		['pascalprecht.translate','smartform.settings.controller','smartform.settings.directive','smartform.settings.service']
	)
		.run(['$rootScope','$msg',function($rootScope,$msg) {
			var _this = {};

			_this.dialog = BX.WindowManager.Get();
			_this.dialog.SetButtons([
				{
					title: $msg.SETTINGS_FORM_SAVE,
					name: 'save',
					id: 'save',
					className: 'adm-btn-save',
					action: function () {
						BX.onCustomEvent('dialog.settings:onSaveForm',_this);
						$rootScope.$emit('dialog.settings:onSaveForm',_this);

						_this.dialog.Close();
					}
				},{
					id: 'cancel',
					title: $msg.SETTINGS_FORM_CANCEL,
					name: 'cancel',
					className: 'cry-btn-cancel',
					action: function() {
						$rootScope.$emit('dialog.settings:onCloseForm');
						_this.dialog.Close();
					}
				}
			]);

			angular.element(_this.dialog.DIV).removeClass('bx-core-adm-dialog');
			angular.element(_this.dialog.DIV).addClass('cry-form-dialog');

			BX.addCustomEvent('onWindowExpand',function() {
				angular.element(_this.dialog.DIV).addClass('cry-dialog-size');
			});

			BX.addCustomEvent('onWindowNarrow',function() {
				angular.element(_this.dialog.DIV).removeClass('cry-dialog-size');
			});
		}]);

	angular.module('smartform.settings.btn',['smartform.settings.directive','smartform.settings.service'])
		.run(['$arParams',function($arParams) {
			var _this = {};

            _this.fieldPrefix = 'FIELDS';
			_this.fieldMap = [];
			_this.arSaveData = null;
			_this.componentPath = $arParams.propertyParams.JS_COMPONENT_PATH;

			_this.prepeaOnBeforeSaveField = function(field) {
				var copyFields = angular.copy(field);
				angular.forEach(copyFields,function(value, index) {
					var cur = field[index];
					if(!angular.isUndefined(cur.ACTIVE) &&
						('Y' != cur.ACTIVE && true !== cur.ACTIVE)
					) {
						delete field[index];
						return true;
					}

					cur.IS_REQUIRED = (cur.IS_REQUIRED === true || cur.IS_REQUIRED == 'Y' ? 'Y' : 'N');
					cur.HIDE_FIELD = (cur.HIDE_FIELD === true || cur.HIDE_FIELD == 'Y' ? 'Y' : 'N');

					if(!angular.isUndefined(cur.GROUP_FIELD) && true === cur.GROUP_FIELD)
						cur.GROUP_FIELD = 'Y';

					if(!angular.isUndefined(cur.ACTIVE))
						delete cur.ACTIVE;

					if(!angular.isUndefined(cur.TEMPLATES) || false === cur.TEMPLATES)
						delete cur.TEMPLATES;
				});
				return field;
			};

			_this.pasteDataOnPage = function(arFields) {
				arFields = _this.prepeaOnBeforeSaveField(arFields);

				var fieldWrElement = angular.element($arParams.oCont);
				_this.cleanDataOnPage();

				angular.forEach(arFields,function(arVariant, index) {
					var fName = _this.fieldPrefix + '[' + index + ']';
					angular.forEach(arVariant,function(value, fCode) {
						var subfName = fName + '[' + fCode + ']';
						var curElement = angular.element('<input type="hidden" name="' + subfName + '"/>');
						curElement.val(value);
						fieldWrElement.append(curElement);
					})
				});
			};

			_this.cleanDataOnPage = function() {
				var fieldWrElement = angular.element($arParams.oCont);
				angular.element(fieldWrElement).find('input[type=hidden]').remove();
			};

			_this.pasteDataOnPage(angular.copy($arParams.data));

			BX.addCustomEvent('public.event:onSaveFormDialogSettings',function(data) {
				_this.arSaveData = data;
				_this.pasteDataOnPage(data);
			});
		}]);
})(window, window.angular);
