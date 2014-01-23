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
	pwd : "",
	repwd : ""
};
/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {
	pwd : "",
	repwd : ""
	
};
$(document).ready(init);
$(document).ready(function() {
	$(".blue-btn").click(function(event) {
		if($("#resetpwd").valid()==true){
			 return false;
		}
	});
	$('input#uname').bind('keypress', function(event) {
		  if(event.keyCode === 9) {
			    $('#pwd').focus();
	            return false;
		  }
		});
	$("input").keypress(function(event) {
	    if (event.which == 13) {
	    	if($("#resetpwd").valid()==true){
	    		event.preventDefault();
	    		resetPasswordUserOnEnter();
	    	}
	    }
	});
});

/**
 * initialize all the settings after document is ready
 * @author sunny patial
 */
function init()
{
	validateresetpwd() ;
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
function validateresetpwd()
{
	validator  = $("form#resetpwd")
	.validate(
			{
				errorClass : 'input-error-full-new',
				validClass : 'input-success-full-new',
				txtvalidClass:'suctxtcls',
				errorElement : 'span',
				afterReset  : resetBorders,
				errorPlacement : function(error, element) {
					element.next("label")
							.html(error);
				},
				rules : {
					pwd : {
						required : true,
						minlength : 1,
						maxlength :20
					},
					repwd : {
						required : true,
						minlength : 1,
						maxlength :20,
						equalTo : "#pwd"
					}
				},
				messages : {
					 pwd : {
						    required : __("Voer je nieuwe wachtwoord in"),
							minlength : __("Please enter minimum 6 characters"),
							maxlength : __("Please enter maximum 20 characters")
					  },
					  repwd : {
						    required : __("Herhaal je nieuwe wachtwoord"),
							minlength : __("Please enter minimum 6 characters"),
							maxlength : __("Please enter maximum 20 characters")
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
									$(element).removeClass('errtxtcls').removeClass('succtxtcls');	
							 	}
			    			 
			    	     } else {
			    	    	this.showLabel(element, focusRules[element.name]);
							$(element).next("label").removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
							$(element).next("label").children("span").removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
							$(element).removeClass('errtxtcls').removeClass('succtxtcls');		
							
					     }
					}
					else if ($(element).hasClass('succls')){
						this.showLabel(element, focusRules[element.name]);
						$(element).parent('div').removeClass(this.settings.errorClass).removeClass(this.settings.validClass).next("label").removeClass(this.settings.validClass).removeClass(this.settings.errorClass).children("span").removeClass(this.settings.validClass).removeClass("label-right-error");
						$(element).removeClass('errtxtcls').removeClass('succtxtcls');	
					}
				},

				highlight : function(element,
						errorClass, validClass) {
					$(element).next("label").removeClass(validClass).addClass(errorClass).children("span").removeClass(validClass).addClass("label-right-error");
					$(element).removeClass(validClass).removeClass('input-success-text-field').removeClass('succtxtcls').addClass('errtxtcls');
				},
				unhighlight : function(element,
						errorClass, validClass) {
					if(! $(element).hasClass("passwordField"))
					{
						$(element).next("label").removeClass(errorClass).addClass(validClass).children("span").removeClass(errorClass).addClass("label-right-success");
						$(element).removeClass('errtxtcls').addClass('succtxtcls');	
						$(
								'span.help-inline',
								$(element).parent('div')
										.prev('div')).text(
								validRules[element.name]);
					}
				},
				success: function(label , element) {

					$(element).removeClass('input-error-text-field')
							  .addClass('input-success-text-field input-success-full-new');
					$(label).removeClass('input-error-full-new help-inline')
							.html(validRules[element.name]).addClass('input-success-full-new');
				}

			});

}

/**
 *reset the validation border of the input filed
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

/**
 * Function for Reset Password
 * @author Raman
 */

function resetPasswordUser(e, obj) {
	
	if($("#resetpwd").valid()){
		
		var pwd = $("#pwd").val();
		var forgotid = $("#forgotid").val();
		$.ajax({
			url : HOST_PATH_LOCALE + "login/resetpwd",
			type : "post",
			data : {'pwd' : pwd, 'forgotid' : forgotid},
			success : function(data){
				console.log(data);
				
				if(data == 'usernotfound'){ 
					
					$("#usernotfound").show();
					
				} else{
					window.location.href = data;
				}
			}
		});
	}
}


/**
 * Function for Reset Password for Enter Event
 * @author Raman
 */

function resetPasswordUserOnEnter() {
	
	if($("#resetpwd").valid()){
		
		var pwd = $("#pwd").val();
		var forgotid = $("#forgotid").val();
		$.ajax({
			url : HOST_PATH_LOCALE + "login/resetpwd",
			type : "post",
			data : {'pwd' : pwd, 'forgotid' : forgotid},
			success : function(data){
				console.log(data);
				
				if(data == 'usernotfound'){ 
					
					$("#usernotfound").show();
					
				} else{
					window.location.href = data;
				}
			}
		});
	}
}
