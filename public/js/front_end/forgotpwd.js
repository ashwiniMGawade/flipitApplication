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
	email : " "
};
/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {
	email : " "
};
$(document).ready(init);
$(document).ready(function() {
	$("#notfound").hide();
	$("#emailsent").hide();
	$("#submitforgotpwd").click(function(event) {
		if($("#forgotpwd").valid()==true){
			return false;
		}
	});
	$('input#email').bind('keypress', function(event) {
		  if(event.keyCode === 9) {
			    $('#pwd').focus();
	            return false;
		  }
		});
	$("input").keypress(function(event) {
	    if (event.which == 13) {
	    	//$("#notfound").hide();
    		//$("#emailsent").hide();
	        event.preventDefault();
	        validateforgotpwd();
	    	if($("#forgotpwd").valid()==true){
	    		sendMailUser();
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
	validateforgotpwd() ;
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
function validateforgotpwd()
{
	validator  = $("form#forgotpwd")
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
					email : {
						required : true,
						minlength : 6,
						email : true
						//regex : /^[0-9]$/
					},
					pwd : {
						required : true,
						minlength : 6,
						maxlength :20
					}
				},
				messages : {
					email : {
						required : " ",
						email : __("Voer een geldig email adres in"),
						minlength: __("Voer een geldig email adres in")
					},
				  pwd : {
					    required : " ",
						minlength : __("Voer minimaal 6 karakters in"),
						maxlength : __("Voer maximaal 20 karakters in")
				  }
				},

				onfocusin : function(element) {
					//alert("jshrjsm");
					if (!$(element).hasClass('succls')) {
						 var label = this.errorsFor(element);
						 if( $( label).attr('hasError')  )
			    	     {
			    			 if($( label).attr('remote-validated') != "true")
			    			 	{
									this.showLabel(element, focusRules[element.name]);
									//$(element).next("label").removeClass(this.settings.validClass).removeClass(this.settings.errorClass).children("span").removeClass(this.settings.validClass).removeClass("label-right-error");
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

				highlight : function(element,errorClass, validClass) {
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
					//console.log('eee');
				},
				success: function(label , element) {

					$(element).removeClass('input-error-text-field')
							  .addClass('input-success-text-field input-success-full-new');
					$(label).removeClass('input-error-full-new help-inline')
							.html(validRules[element.name]).addClass('input-success-full-new');
					
					//console.log('ffff');
				}

			});

}

/**
 * $("#email").blur(function(event) {
						store_data();
					});
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

/**
 * sendMailUser
 * 
 * Function for Login Check
 * 
 * @author Raman
 */
function sendMailUser(e, obj) {
	
	if($("#forgotpwd").valid()){
		
		var overlay = $("<div id='againSet'><a><img src='/public/images/ajax-loader2.gif' id='img-load'/></a></div>");
		$('div#changeWhenLogin').replaceWith(overlay);
		
		var email = $("#email").val();
		$.ajax({
			url : HOST_PATH_LOCALE + "login/forgotpwd",
			type : "post",
			data : {'email' : email},
			success : function(data){
				if(data == 'emailnotfound'){
					$("#emailsent").hide();
					$("#notfound").show(); 
					var againButton = '<div id="changeWhenLogin" class="blue_btn"><a href="javascript:void(0);"  tabindex="3" onclick="sendMailUser();">' + __('verstuur') + '</a></div>';
					$('div#againSet').replaceWith(againButton);
					
				} else if(data == 'emailsent'){  
					$("#emailsent").show();
					$("#notfound").hide();
					var againButton = '<div id="changeWhenLogin" class="blue_btn"><a href="javascript:void(0);" onclick="sendMailUser();" tabindex="2">' + __('verstuur') + '</a></div>';
					$('div#againSet').replaceWith(againButton);
					
				}
					
				
			}
		});
	}
}
