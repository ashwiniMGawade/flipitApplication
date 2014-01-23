/**
 * profile.js 1.0
 * @author sunny patial
 */





/**
 * validRules oject contain all the messages that are visible when an elment
 * val;ue is valid
 * 
 * structure to define a message for element key is to be element name Value is
 * message
 */
var validRules = {
	acti_email : __("Email looks great"),
	codetxt: __("Actiecode looks great")
};
/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {
	acti_email : __("Enter valid email address"),
	codetxt: __("Enter valid actiecode")
};
$(document).ready(init);
$(document).ready(function() {
	$("#submitemail").click(function(event) {
		if($("#emaillink").valid()==true){
			$("#emaillink").submit();
		}
	});
	$("#submitactiecode").click(function(event) {
		if($("#codelink").valid()==true){
			$("#codelink").submit();
		}
	});	
});

/**
 * initialize all the settings after document is ready
 * @author sunny patial
 */
function init()
{
	validateemaillink() ;
	validactiecode();
}
/**
 * update user profile
 * @author sunny patial
 * @param e event
 */
// global validation object  
var validator = null; 
/**
 * form validation during update user 
 * @author sunny patial
 */
function validateemaillink()
{
	validator  = $("form#emaillink")
	.validate(
			{
				errorClass : 'errcls',
				validClass : 'succls',
				txtvalidClass:'suctxtcls',
				errorElement : 'span',
				afterReset  : resetBorders,
				errorPlacement : function(error, element) {
					element.next("label")
							.html(error);
				},
				rules : {
					acti_email : {
						required : true,
						minlength : 6,
						email : true
					}
				},
				messages : {
					acti_email : {
						required : __("Please enter your email address"),
						email : __("Please enter valid email address"),
						minlength: __("Please enter valid email address")
					}
				},

				onfocusin : function(element) {
					if (!$(element).hasClass('succls')) {
						 var label = this.errorsFor(element);
						 if( $( label).attr('hasError')  )
			    	     {
			    			 if($( label).attr('remote-validated') != "true")
			    			 	{
									this.showLabel(element, focusRules[element.name]);
									$(element).next("label").removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
									$(element).next("label").children("span").removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
									$(element).removeClass('errtxtcls2').removeClass('succtxtcls2');	
							 	}
			    			 
			    	     } else {
			    	    	this.showLabel(element, focusRules[element.name]);
							$(element).next("label").removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
							$(element).next("label").children("span").removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
							$(element).removeClass('errtxtcls2').removeClass('succtxtcls2');		
							
					     }
					}
					else if ($(element).hasClass('succls')){
						this.showLabel(element, focusRules[element.name]);
						$(element).removeClass('errtxtcls2').removeClass('succtxtcls2');	
					}
				},

				highlight : function(element,
						errorClass, validClass) {
					$(element).next("label").removeClass(validClass).addClass(errorClass).children("span").removeClass(validClass).addClass("label-right-error");
					$(element).removeClass(validClass).addClass('errtxtcls2');	
				},
				unhighlight : function(element,
						errorClass, validClass) {
					if(! $(element).hasClass("passwordField"))
					{
						$(element).next("label").removeClass(errorClass).addClass(validClass).children("span").removeClass(errorClass).addClass("label-right-success");
						$(element).removeClass('errtxtcls2').addClass('succtxtcls2');	
						$(
								'span.help-inline',
								$(element).parent('div')
										.prev('div')).text(
								validRules[element.name]);
					}
				},
				success: function(label , element) {
				    $(label).append( validRules[element.name] ) ;
				    label.addClass('valid') ;
				}

			});

}

/***
 * function for valid actiecode......
 * **/
function validactiecode(){

	validator  = $("form#codelink")
	.validate(
			{
				errorClass : 'errcls',
				validClass : 'succls',
				txtvalidClass:'suctxtcls',
				errorElement : 'span',
				afterReset  : resetBorders,
				errorPlacement : function(error, element) {
					element.next("label")
							.html(error);
				},
				rules : {
					codetxt : {
						required : true
					}
				},
				messages : {
					codetxt : {
						required : __("Please enter your actiecode")
					}
				},

				onfocusin : function(element) {
					if (!$(element).hasClass('succls')) {
						 var label = this.errorsFor(element);
						 if( $( label).attr('hasError')  )
			    	     {
			    			 if($( label).attr('remote-validated') != "true")
			    			 	{
									this.showLabel(element, focusRules[element.name]);
									$(element).removeClass('errtxtcls2').removeClass('succtxtcls2');	
							 	}
			    			 
			    	     } else {
			    	    	this.showLabel(element, focusRules[element.name]);
							$(element).removeClass('errtxtcls2').removeClass('succtxtcls2');		
							
					     }
					}
					else if ($(element).hasClass('succls')){
						this.showLabel(element, focusRules[element.name]);
						$(element).removeClass('errtxtcls2').removeClass('succtxtcls2');	
					}
				},

				highlight : function(element,
						errorClass, validClass) {
					$(element).removeClass(validClass).addClass('errtxtcls2');	
				},
				unhighlight : function(element,
						errorClass, validClass) {
					if(! $(element).hasClass("passwordField"))
					{
						$(element).removeClass('errtxtcls2').addClass('succtxtcls2');	
						$(
								'span.help-inline',
								$(element).parent('div')
										.prev('div')).text(
								validRules[element.name]);
					}
				},
				success: function(label , element) {
				    $(label).append( validRules[element.name] ) ;
				    label.addClass('valid') ;
				}

			});
}

/**
 * reset the validation border of the input filed
 * @param el
 * @author sunny patial
 */

function resetBorders(el)
{
	$(el).each(function(i,o){
		
		$(o).parent('div')
		.removeClass("error success")
		.prev("div").removeClass('focus error success') ;
	
	});
	
}

