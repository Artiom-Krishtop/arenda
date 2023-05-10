(function (windows, angular) {
	'use strict';

	angular.module('smartform.settings.directive',[])
		.directive(
			'settingBtn', ['$q','$arParams','$templateCache','$rootScope','$http',
				function($q,$arParams,$templateCache,$rootScope,$http) {
					return {
						restrict: 'E',
						replace: true,
						transclude: true,
						template: function(element, attrs) {
							return '<input type="button" class="adm-btn-save" ng-click="showSettingDialog(\'' + attrs.src + '\')" value="' + BX.message('citrus_iblock_element_form_js') + '" />';
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
										title: BX.message('citrus_iblock_element_form_title_js'),
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

								/*
								 ngDialog.open({
								 closeByDocument: false,
								 template: url + '/settings/template.html',
								 className: 'ngdialog-theme-default',
								 controller: 'settingsFormCtrl'
								 });*/
							};
						}]
					}
				}])
		.directive('fieldDetail', ['$templateCache', function($templateCache) {
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
						important: {title:BX.message('citrus_iblock_element_form_important_js')},
						length: {title:BX.message('citrus_iblock_element_form_length_js')},
						main_password: {title:BX.message('citrus_iblock_element_form_main_password_js')},
						confirm_password: {title:BX.message('citrus_iblock_element_form_confirm_password_js')},
						number: {title:BX.message('citrus_iblock_element_form_number_js')},
						phone: {title:BX.message('citrus_iblock_element_form_phone_js')},
						url: {title:BX.message('citrus_iblock_element_form_url_js')}
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

						var curRule = [];
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
						}
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
			'$arParams','$scope','$http','$q','$rootScope','$activeField','$filter',
			function($arParams,$scope,$http,$q,$rootScope,$activeField,$filter) {
				var _this = this;

				_this.arSaveData = {};
				_this.dialog = $arParams.dialog;

				_this.prepeaData = function() {
					angular.forEach($rootScope.models.show.items,function(item,index) {
						var tmpItem = angular.copy($scope.arField[item.id]);
						tmpItem.ACTIVE = true;
						_this.arSaveData[item.id] = _this.prepeaFieldData(tmpItem);
					});

					BX.onCustomEvent('public.event:onSaveFormDialogSettings',[angular.copy(_this.arSaveData)]);
				};

				$rootScope.$on('dialog.settings:onSaveForm',function(data) {
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

				_this.getFields = function() {
					var deferred = $q.defer();
					var params = _this.dialog.PARAMS.content_url.replace(/.*\?/,'') + '&iblock_id=' + BX.message('IBLOCK_ID');
					$http({
						method: 'POST',
						url: _this.componentPath + '/settings/settings.php',
						data: params,
						cache: true,
						headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					}).then(function(respons) {
						deferred.resolve(respons.data);
					}, function(respons) {
						deferred.reject(respons.data);
					});

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
						ORIGINAL_TITLE: BX.message('citrus_iblock_element_form_new_group_js') + ' (' + newGroupId + ')',
						TITLE: BX.message('citrus_iblock_element_form_new_group_js'),
						GROUP_FIELD: true,
						selected: false,
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

				$rootScope.$on('dialog.save.settings.form',function() {
					console.log('sdvsdvsd');
				});
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
		['smartform.settings.controller','smartform.settings.directive','smartform.settings.service']
	)
		.run(['$rootScope',function($rootScope) {
			var _this = {};

			_this.dialog = BX.WindowManager.Get();
			_this.dialog.SetButtons([
				{
					title: BX.message('citrus_iblock_element_form_save_js'),
					name: 'save',
					id: 'save',
					// className: 'cry-btn-save',
					className: 'adm-btn-save',
					action: function () {
						BX.onCustomEvent('dialog.settings:onSaveForm',_this);
						$rootScope.$emit('dialog.settings:onSaveForm',_this);

						_this.dialog.Close();
					}
				},
				{
					id: 'cancel',
					title: BX.message('citrus_iblock_element_form_cancel_js'),
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

	var serialize = function( mixed_val ) {

			// Generates a storable representation of a value
			//
			// +   original by: Ates Goral (http://magnetiq.com)
			// +   adapted for IE: Ilia Kantor (http://javascript.ru)

			switch (typeof(mixed_val)){
				case "number":
					if (isNaN(mixed_val) || !isFinite(mixed_val)){
						return false;
					} else{
						return (Math.floor(mixed_val) == mixed_val ? "i" : "d") + ":" + mixed_val + ";";
					}
				case "string":
					return "s:" + mixed_val.length + ":\"" + mixed_val + "\";";
				case "boolean":
					return "b:" + (mixed_val ? "1" : "0") + ";";
				case "object":
					if (mixed_val == null) {
						return "N;";
					} else if (mixed_val instanceof Array) {
						var idxobj = { idx: -1 },
							map = []
						for(var i=0; i<mixed_val.length;i++) {
							idxobj.idx++;
							var ser = serialize(mixed_val[i]);

							if (ser) {
								map.push(serialize(idxobj.idx) + ser)
							}
						}

						return "a:" + mixed_val.length + ":{" + map.join("") + "}"

					}
					else {
						var props = [];
						for (var prop in mixed_val) {
							if (mixed_val.hasOwnProperty(prop)) {
								var ser = serialize(mixed_val[prop]);

								if (ser) {
									props.push(serialize(prop) + ser);
								}
							}
						}
						return "a:" + props.length + ":{" + props.join("") + "}";
					}
				case "undefined":
					return "N;";
			}

			return false;
		};

	angular.module('smartform.settings.btn',['smartform.settings.directive'])
		.run(['$arParams',function($arParams) {
			var _this = {
				componentPath: $arParams.propertyParams.JS_COMPONENT_PATH
			};

			BX.loadCSS([_this.componentPath + '/settings/settings.css']);

			BX.addCustomEvent('public.event:onSaveFormDialogSettings',function(data) {
				$arParams.oInput.value = serialize(data);
			});
		}]);
})(window, window.angular);
