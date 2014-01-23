/**
 * profile.js 1.0
 * @author spsingh
 */





/**
 * validRules oject contain all the messages that are visible when an elment
 * val;ue is valid
 * 
 * structure to define a message for element key is to be element name Value is
 * message
 */
var validRules = {

	acclimit : __("Max free accounts looks great")
};


/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {

	acclimit : __("Enter numbers only")

};




$(document).ready(init);

/**
 * initialize all the settings after document is ready
 * @author spsingh
 */

function init()
{
	// validate account setting form data
	validatespeacialForm() ;
	
}



/**
 * update user profile
 * @author spsingh
 * @param e event
 */



// global validation object  
var validator = null; 

/**
 * form validation during update user 
 * @author spsingh
 */

function validatespeacialForm()
{
	validator  = $("form#speacialForm")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				afterReset  : resetBorders,
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},
				rules : {
					acclimit : {
						required : true,
						maxlength : 10
						//regex : /^[0-9]$/
					}
				},
				messages : {
					acclimit : {
						required : __("Please enter maximum account limit"),
						//regex : "Only digits allowed" ,
						maxlength : __("Please not enter more than 10 digits"),

					}
				},

				onfocusin : function(element) {
					if (!$(element).parent('div').prev("div")
							.hasClass('success')) {
						
						
						
			    		 var label = this.errorsFor(element);
			    		 if( $( label).attr('hasError')  )
			    	     {
			    			 if($( label).attr('remote-validated') != "true")
			    			 	{
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
			    			 
			    	     } else {
			    	    	 
			    	    	 

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
					
					if(! $(element).hasClass("passwordField"))
					{
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
					}
				},
				success: function(label , element) {
					
					$(element).parent('div')
					.removeClass(this.errorClass)
					.addClass(this.validClass).prev(
							"div").addClass(
									this.validClass)
					.removeClass(this.errorClass);
					
				    $(label).append( validRules[element.name] ) ;
				    label.addClass('valid') ;
				   // if ($("form#speacialForm").valid()) {
				    //	$("#acclimit").blur(function(event) {
						//	store_data();
						//});
				     //}
				}

			});

}

/**
 * $("#acclimit").blur(function(event) {
						store_data();
					});
 * reset the validation border of the input filed
 * @param el
 * @author spsingh
 */

function resetBorders(el)
{
	$(el).each(function(i,o){
		
		$(o).parent('div')
		.removeClass("error success")
		.prev("div").removeClass('focus error success') ;
	
	});
	
}

/*$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	
	}

});*/
