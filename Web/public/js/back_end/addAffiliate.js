

/**
 * validRules oject contain all the messages that are visible when an elment
 * value is valid
 * structure to define a message for element: key is to be element name and Value is
 * message
 */
var validRules = {
         addNetworkText : __("Network name looks great"),
         subId : __("Network subid looks great")
};

/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element : key is to be element name and Value is
 * message
 */
var focusRules = {
		addNetworkText : __("Please enter network name"),
		subId : __("Enter network subid")
};


/**
 * executes when document is loaded 
 * @author blal updated by kraj
 */
$(document).ready(function(){
	
	$('form#addnewaffiliateNetwork').submit(function(){
		checkValidate();
	});	
	affilateValidation();
});
function checkValidate()
{
	if($("form#addnewaffiliateNetwork").valid())
	{   
		$('#addNetwork').attr('disabled' ,"disabled");
		return true;
		
	}else {
		 return false;
	}
	
}
$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});
var validatorForNewNetwork = null ;
/**
 * apply validation on add new network
 * @author blal updateb by kraj
 */
function affilateValidation(){
	validatorForNewNetwork = $("form#addnewaffiliateNetwork")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				ignore: ":hidden,.ignore",
				errorElement : 'span',
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},


				rules : {
					addNetworkText : {
						 required : true,
						 minlength : 2
						}
				},
				
				messages : {
					addNetworkText : {
					      required : __("Please enter network name"),
					      minlength : __("Please enter atleast 2 characters")
					},
					subId : {
						  required : __("Please enter network subid"),	
					}
				},
				onfocusin : function(element) {
					if (!$(element).parent('div').prev("div")
							.hasClass('success')) {
						this.showLabel(element, focusRules[element.name]);
							
							$(element).parent('div').removeClass(
											this.settings.errorClass)
									.removeClass(
											this.settings.validClass)
									.prev("div")
									.addClass('focus')
									.removeClass(
											this.settings.errorClass)
									.removeClass(
											this.settings.validClass);
			    	 
					}
				},
				highlight : function(element,
						errorClass, validClass) {

					$(element).parent('div')
							.removeClass(validClass)
							.addClass(errorClass).prev(
									"div").removeClass(
									validClass)
							.addClass(errorClass);

				},
				unhighlight : function(element,
						errorClass, validClass) {
					
					if(! jQuery(element).hasClass("ignore")) {
						
						$(element).parent('div')
								.removeClass(errorClass)
								.addClass(validClass).prev(
										"div").addClass(
										validClass)
								.removeClass(errorClass);
						$(
								'span.help-inline',
								$(element).parent('div')
										.prev('div')).text(
								validRules[element.name]);
				
					} else {
						
						var val = jQuery(element).val();
						showError =  jQuery.trim(val).length > 0;
						
						if(! showError )
						{
							// hide errors message and remove highlighted borders 
								jQuery(
										'span.help-inline',
										jQuery(element).parent('div')
										.prev('div')).html('');
								
									jQuery(element).parent('div')
									.removeClass(errorClass)
									.removeClass(validClass)
									.prev("div")
									.removeClass(errorClass)
									.removeClass(validClass) ;
						} else {
							jQuery(element).parent('div')
							.removeClass(errorClass)
							.addClass(validClass).prev(
									"div").addClass(
									validClass)
							.removeClass(errorClass);
							
							jQuery('span.help-inline', jQuery(element).parent('div')
											.prev('div')).text(
								 validRules[element.name] ).show();
						}
					}
				}

			});
}


