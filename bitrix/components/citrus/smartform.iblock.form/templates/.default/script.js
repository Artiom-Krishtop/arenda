;(function(){
    window.citrusForm = function ( formId, items, params) {
        "use strict";
        if (typeof $ === "undefined") { console.error("jQuery not defined"); return;}
        if( !formId || !items ) { console.error("Ошибка в аргументах"); return; }

        var self = this;
	    self.params = $.extend({
		    'JQUERY_VALID': true,
		    'AJAX': true
		}, params || {});

        self.$form = $("#" + formId);
	    self.resultWrapper = self.$form.find('[cui-form="message-block"]');
	    self.$btn = self.$form.find(':submit');
	    self.$btnReplace = self.$form.find('[cui-submit-replace]');

	    //antispam
	    if (params.HIDDEN_ANTI_SPAM) self.$form.find('[name="GIFT"]').remove();

	    self.clearMessages = function(){
		    self.resultWrapper.addClass('hidden').html('');
	    };
	    self.showSuccessBlock = function(message) {
		    var html = '<div class="message-block bg-success">' +
						    '<div class="message-block-icon"></div>'+
						    '<div class="message-block-icon-txt">'+
						        '<p>' + message + '</p>' +
						    '</div>'+
			            '</div>';

		    self.resultWrapper.removeClass('hidden')
		                      .html(html);
		    //TODO scrollIntoViewIfNeeded
	    };
	    self.showErrorBlock = function($arMessage) {
		    var html = '<div class="message-block bg-danger">' +
					        '<div class="message-block-icon"></div>' +
				                '<div class="message-block-txt">'+
							    '<ul>';
							    $.each($arMessage, function(i,message) {
								    html += '<li>' + message +'</li>';
							    });
						    html += '</ul>' +
						    '</div>'+
					    '</div>';
		    self.resultWrapper.removeClass('hidden')
			                  .html(html);
	    };
	    self.addLoading = function () {
		    self.$btn.attr('disabled', 'disabled');
		    self.$btnReplace.attr('disabled', 'disabled');
		    if (cui_btn !== undefined) cui_btn.addStatus(self.$btnReplace, 'loading-spinner');

		    self.$form.trigger('addLoading');
	    };
	    self.removeLoading = function () {
		    self.$btn.removeAttr('disabled');
		    self.$btnReplace.removeAttr('disabled');
		    if (cui_btn !== undefined) cui_btn.clearStatus(self.$btnReplace);

		    self.$form.trigger('removeLoading');
	    };

	    self.addSuccess = function () {
	    	//btn status
		    self.removeLoading();
		    if (cui_btn !== undefined){
			    cui_btn.addStatus(self.$btnReplace, 'success');
		    }
		    //reset form
		    if (!!self.validator) {
			    self.validator.callEvent('reset');
		    } else {
		    	self.$form.get(0).reset();
		    }
		    //blur inputs after reset
		    self.$form.find('input, select, textarea').trigger('blur');
			self.$form.trigger('addSuccess');
			
			// FIX hide form inputs
			if (self.params.HIDE_INPUTS_ON_SUCCESS) {
				self.$form.find('.citrus-form__fields').hide();
				self.$form.find('.citrus-form__footer').hide();
			}
	    };

	    // validate
	    if (self.params.JQUERY_VALID) {
		    if (typeof citrusValidator !== "undefined") {
			    self.validator = new citrusValidator( self.$form );
			    for ( var fieldCode in items ) {
				    var fieldInfo = items[fieldCode];
				    //add to validate
				    if ((fieldInfo["VALIDRULE"] && fieldInfo["VALIDRULE"].length) || fieldInfo["ADDITIONAL"]) {
				    	var $input = self.$form.find("#"+fieldInfo['ID']).length ?
								    self.$form.find("#"+fieldInfo['ID']):
								    self.$form.find("[NAME='" + fieldInfo["CODE"] + "']");
				    	
				    	if($input.length) {
						    self.validator.addField(
							    $input,
							    fieldInfo["VALIDRULE"] || [],
							    fieldInfo["ADDITIONAL"] || {},
							    fieldInfo["VALID_ERROR_MSG"] || {}
						    );
					    }
				    }
			    }
		    } else {
			    console.error("citrusValidator not defined");
		    }
	    }

	    // AJAX
	    if (self.params.AJAX) {
		    self.$form.on('submit', function (event) {
			    event.preventDefault();

				self.addLoading();
				var data = new FormData(self.$form.get(0));
				$.ajax({
					type: "POST",
					enctype: 'multipart/form-data',
					url: self.$form.data('ajax-action'),
					data: data,
					dataType: 'JSON',
					processData: false,
					contentType: false,
					cache: false,
					timeout: 600000,
				})
				.done(function(response) {
					if(undefined !== response.message)
						self.showSuccessBlock(response.message);
					else
						self.showSuccessBlock('Сообщение успешно добавлено!');

					self.addSuccess();
				})
				.fail(function(response) {
					self.removeLoading();
					var arRespons = JSON.parse(response.responseText);
					if(undefined !== arRespons.message)
						self.showErrorBlock(arRespons.message);
				});
		    });
	    }

	    self.$form.on('keyup change focus', 'input, select, textarea', function (event) {
		   //self.clearMessages();
		    if (cui_btn !== undefined) cui_btn.clearStatus(self.$btnReplace);
	    });

        //красивое переключение лейблов
        cui_form.labelMaterialSwitch(".js_material_switch_container");
    }
})();