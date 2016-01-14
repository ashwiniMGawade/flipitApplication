$(document).ready(function() {
	selectShop();
	$('#dp3').datepicker().on('changeDate');
	validateNewsTicker();
    $("#addOfferBtn, #saveAndAddnew").submit(function(){
        if($("form#createNewstickerForm").valid()){
          return true;
        } else{
          return false;
        }
    });
});

function selectShop(){
$("#whichshop").select2({placeholder: __("Select a Shop")});
   $("#whichshop").change(function(){
		$("#selctedshop").val($(this).val());
	});
}

function newschangelinkStatus(el)
{
	jQuery(el).addClass('btn-primary').siblings('button').removeClass('btn-primary active') ;	
	if (jQuery(el).attr('name') == 'newsdeepLinkOnbtn')
	{
		jQuery("input[type=checkbox]" , jQuery(el).parent("div")).attr('checked' , 'checked').val(1);
		jQuery("#newsrefUrl" , jQuery(el).parent("div")).removeAttr("disabled");
		
	} else
	{
		jQuery("input[type=checkbox]" , jQuery(el).parent("div")).removeAttr('checked').val(0);
		jQuery("#newsrefUrl" , jQuery(el).parent("div")).attr("disabled", "disabled");
	}
}

var validRules = {
    newsTitle : __("looks great"),
	newsDescription : __("looks great"),
	whichshop : __("looks great")
};

var focusRules = {
	newsTitle : __("Please enter news ticker title"),
	newsDescription : __("Please enter description"),
	whichshop : __("Please select a shop")
};
var validator =  null;
function validateNewsTicker() {
    validator = $('form#createNewstickerForm')
    .validate({
        ignore: [],
        errorClass : 'error',
		validClass : 'success',
		errorElement : 'span',
		errorPlacement : function(error, element) {
			element.parent("div").prev("div").html(error);
		},
        rules: {
            newsTitle: {
                required: true
            },
            newsDescription: {
                required: true
            },
            "whichshop": {
                required: true
            }
        },
        messages : {
         	newsTitle: {
                required:__("Please enter news ticker title")
            },
            newsDescription: {
                required:__("Please enter description")
            },
            "whichshop": {
                required:__("Please select a shop")
            }
        },
        onfocusin : function(element) {
			if (!$(element).parent('div').prev("div")
					.hasClass('success')) {
	    		 var label = this.errorsFor(element);   		 
	    		 if( $( label ).attr('hasError')) {
	    			 if ($( label ).attr('remote-validated') != "true") {
						this.showLabel(element, focusRules[element.name]);
						$(element).parent('div').removeClass(this.settings.errorClass)
							.removeClass(this.settings.validClass)
							.prev("div").addClass('focus')
							.removeClass(this.settings.errorClass)
							.removeClass(this.settings.validClass);
    			 	}
	    	     } else { 	 
					this.showLabel(element, focusRules[element.name]);	
					$(element).parent('div').removeClass(this.settings.errorClass)
						.removeClass(this.settings.validClass)
						.prev("div").addClass('focus')
						.removeClass(this.settings.errorClass)
						.removeClass(this.settings.validClass);
	    	     }
			}
		},

		highlight : function(element, errorClass, validClass) {
			$(element).parent('div')
				.removeClass(validClass)
				.addClass(errorClass).prev(
						"div").removeClass(
						validClass)
				.addClass(errorClass);
			$('span.help-inline', $(element).parent('div').prev('div')).removeClass(validClass) ;
							
		},
		unhighlight : function(element, errorClass, validClass) {
			if(! $(element).hasClass("ignore")) {				
				$(element).parent('div')
					.removeClass(errorClass)
					.addClass(validClass).prev(
							"div").addClass(
							validClass)
					.removeClass(errorClass);
				$('span.help-inline', $(element).parent('div').prev('div')).text(validRules[element.name]);
			} else {
				var showError = false ;
				switch( element.nodeName.toLowerCase() ) {
				case 'input':
					if ( this.checkable(element) ) {						
						showError = this.getLength(element.value, element) > 0;						
					} else if($.trim(element.value).length > 0) {						
							showError =  true ;							
						} else {							
							showError = false ;
						}							
					break ; 
				default:
					var val = $(element).val();
					showError =  $.trim(val).length > 0;
				}				
				if(! showError ) {
					$('span.help-inline', $(element).parent('div').prev('div')).hide();					
					$(element).parent('div')
					.removeClass(errorClass)
					.removeClass(validClass)
					.prev("div")
					.removeClass(errorClass)
					.removeClass(validClass);
				} else  {
					if(element.type !== "file") {
						$(element).parent('div')
						.removeClass(errorClass)
						.addClass(validClass).prev(
								"div").addClass(
								validClass)
						.removeClass(errorClass);
						
						$('span.help-inline', $(element).parent('div')
										.prev('div')).text(
							 validRules[element.name] ).show();
					} else {
						$(element).parent('div')
						.removeClass(errorClass)
						.removeClass(validClass)
						.removeClass("focus")
						.prev("div")
						.removeClass("focus")
						.removeClass(errorClass)
						.removeClass(validClass) ;
					}
				}
			} 
		},
		success: function(label , element) {			
			$(element).parent('div')
			.removeClass(this.errorClass)
			.addClass(this.validClass).prev(
					"div").addClass(
							this.validClass)
			.removeClass(this.errorClass)
			.removeClass("focus");
		    $(label).append( validRules[element.name] ) ;
		    label.addClass('valid') ;
		    
		}
	});
}
jQuery.validator.setDefaults({
		onkeyup : false,
		onfocusout : function(element) {
			jQuery(element).valid() ;
		}

});
