/*
 * ready function consists of user login validation through validate.js  
 */
var backlink = null;
$(document).ready(function(){
	
	//$('a#back').click(backLink);
	
	//loginValidation();
	//forgotPassValidation();
	//on submit of the login form
	$('#form1').submit(function() {
		
		//check validatation 
		validateUser();
		
	});
	//on submit of the forgot password form
	$('#forgotpwd').submit(function() {
		
		//check validatation 
		validateFogotPassword();
		
	});
	/**
	 * validRules oject contain all the messages that are visible when an elment
	 * value is valid
	 * structure to define a message for element key is to be element name Value is
	 * message
	 */
	var validRules = {
			
			//uname : "Valid Username. ",
			//pwd : "Valid Password. ",
			//email : "Valid Username. "
		};
	/**
	 * focusRules oject contain all the messages that are visible on focus of an
	 * elelement
	 * structure to define a message for element key is to be element name Value is
	 * message
	 */
		var focusRules = {

				uname : __("Enter your Username"),
				pwd : 	__("Enter your password"),
				email : __("Enter your Username/E-mail Address")
				};
		
/**
 * loginValidation action for login form 
 * validation through validate.js
 */
function loginValidation(){
$("form#form1")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				errorPlacement : function(error,element) {

				  $("#error-msg").html(error);
				  
				},
				rules : {
					uname : {required : true, email : true},
					pwd : {required : function(el){
						
						if($("input[name=uname]").val().length > 0 )
						{
							var regex =  /^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[a-zA-Z]{2,4}$/i ;
							var result = "" ;
							 result = regex.exec($("input[name=uname]").val());
							
							if(result)
							{
								return true;	
							}
							return false ;
						}
						
						return false;
					}
					,minlength: 8},
				},
				messages : {
					uname : {
						required : __("Please enter your Username"),
						email: __("Please enter valid Username")
						
					},
					pwd : {
						required : __("Please enter your  password"),
						minlength : __("Please enter minimum 8 characters")
					},
					
				},

				onfocusin : function(element) {
					
					
				if (!$(element).hasClass('success') && $(element).val()=='') 
				{
						this.showLabel(element,focusRules[element.name]);
						$('span.help-inline').removeClass('error')
						.removeClass('success').addClass('focus');
				}
				else{
					 $("#error-msg").html('');
				}
						$(element).removeClass(this.settings.errorClass)
						.removeClass(this.settings.validClass).addClass('focus');
					
						
				},

				highlight : function(element,errorClass, validClass) {

					
					$(element).removeClass(validClass).addClass(errorClass);
					 $('span.help-inline').removeClass('success').addClass('error');
				},
				unhighlight : function(element,	errorClass, validClass) {

					
					if(element.name == "pwd" && ! this.settings.rules[element.name].required ())
					{
						//$("button[type=submit]").focus();
						$(element).removeClass(errorClass).removeClass(validClass);
						
					} else
					{
						
						$(element).removeClass(errorClass).addClass(validClass);
						$('span.help-inline').html(validRules[element.name]);
						
					}
				},

			});
	}
	
/**
 * forgotPassValidation action for 
 * forgot Password form validation through validate.js
 * @author mkaur
 */
function forgotPassValidation(){
	backlink=$("form#forgotpwd").validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				errorPlacement : function(error,element) {

				  $("#error-msg").html(error);
				  
				},
				rules : {
					email : {required : true,email : true},
				},
				
				messages : {
					email : {
					required : __("Please enter your Username/E-mail Address"),
					
					email: __("Please enter valid Username/E-mail Address")
					},
				},
				
				onfocusin : function(element) {
					if (!$(element).hasClass('success')&& $(element).val()=='') 
					{
						this.showLabel(element,focusRules[element.name]);
						
						$(element).removeClass(this.settings.errorClass)
						.removeClass(this.settings.validClass).addClass('focus');
					
						$('span.help-inline').removeClass('error')
						.removeClass('success').addClass('focus');
					}
					else{
						 $("#error-msg").html('');
					}
				},
				highlight : function(element,errorClass, validClass) {
					 $(element).removeClass(validClass).addClass(errorClass);
					 $('span.help-inline').removeClass('success').addClass('error');
					 
				},
				unhighlight : function(element,	errorClass, validClass) {
					if($(element).val()!=''){
						$("#error-msg").html('');
					}
					$(element).removeClass(errorClass).addClass(validClass);
					$('span.help-inline').html(validRules[element.name]).removeClass(errorClass).addClass(validClass);

				},

			});
	}
});

$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});
/*
function backLink(){
backlink.resetForm();
window.location = HOST_PATH + "admin/auth";
}
*/
/**
 * checks whether username of password correct or 
 * not through validatelogin under auth controller  
 * @author mkaur
 * updated by kraj
 */
function validateUser()
{
	//$("button[type=submit]").focus();
	
	if ($("#form1").valid()) {
		
		return true;
		
	}else {
		
		return false;
	}
}
/**
 * Checks whether UserName valid or 
 * not through validateforgotpassword() 
 * action in auth controller.
 * @author mkaur
 * updated by kraj
 */
function validateFogotPassword(){
	$("button[type=submit]").focus();
	if ($("#forgotpwd").valid()) {
		return true;
		
		}else {
			
			return false;
		}
		
}