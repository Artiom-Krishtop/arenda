if(typeof $useJquery !=="undefined" && $useJquery === true) {
	$(function() {
		var $rule = {};
	
		if(typeof $arValidateParam !=="undefined") {
			for (index in $arValidateParam) {
				$val = $arValidateParam[index];
				$.validator.addMethod(
				    "validate"  + $val.VALID_FUNC + "Func", 
				    function(value, element) {
				    	$strRuel = $arValidateParam[element.name].RUL;
				    	regex = new RegExp($strRuel);
				    	return regex.test(value);
				    },
				    $val.ERROR_MSG
				);
				
				$rule[$val.CODE] = {};
				$rule[$val.CODE]["validate"  + $val.VALID_FUNC + "Func"] = true;
			}
	
			$.validator.addMethod(
		        "numberFormat", 
		        function(value, element) {
		        	return /^\d{3}-\d{2}-\d{2}$/.test(value);
		        },
		    	$errorMsgNumFormat
		   );
		}
	
	
	
	    $(".ciee-form").validate({
	        rules:$rule,
	        errorElement: 'div',
	        errorClass: 'b-f-error'
	    });
	    
		$('.field-form-input .f-line-input-val').hover(
		    function(){
	 		 	__showErrorBlock(this);
		    }, 
		    function(){
		        $(this).next('div.b-f-error').hide();
		    }
		);
	
		$('.b-btn-submit-button').click(function() {
			var $form = $(this).parents('form');
			$form.submit();
			return false;
		});
	
		function __showErrorBlock($this) {
	        if ($($this).hasClass('b-f-error')){
	            w = $($this).outerWidth();
	            pos = $($this).position();
	
	            var wBxError = $($this).next('div.b-f-error').outerWidth();
	            var wBxWin = $(window).outerWidth();
	            var $curPos = $this.getBoundingClientRect();
	            
	            if($curPos.right + wBxError < wBxWin)
	            	$($this).next('div.b-f-error').css('left', pos.left+w+5).show();
	            else
	            	$($this).next('div.b-f-error').css('right', pos.left+w+15).show();
	            
	            h = $($this).next('div.b-f-error').height();
	          	if (h > 20){
	                w_err = $($this).next('div.b-f-error').outerWidth();
	                str = Math.round(h / 18);
	            }
	        }
		}
	});
}

if(typeof $ !=="undefined") {
	$(function() {
		if(typeof $('.bx-js-update-button') !== "undefined") {
			$('.bx-js-update-button').click(function() {
				var curElemetn = $(this);
				var $form = $(this).parents('form');
				var curUrl = $form.attr('action');
				var arUpdateForm = $form.parents('.bx-js-update-block').parent();
				var formValue = $($form).serialize();
				var dataField = 'ajax_param=true&' + formValue;

				BX.showWait(BX($($form).attr('id')));

				$.ajax({
					type: "POST",
					url: curUrl,
					dataType: 'html',
					data:dataField,
					error: function(responseText){
						arUpdateForm.html(responseText);
					},
					success: function(responseText) {
						BX.closeWait();
						arUpdateForm.html(responseText);
					}
				});
		
				return false;
			});
		}
	})
}