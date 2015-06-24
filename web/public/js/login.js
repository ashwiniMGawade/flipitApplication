/*
 * ready function consists of user login validation through validate.js  
 */
$(document).ready(function(){
	loginValidation();
	forgotPassValidation();
	
	$('#form1').submit(function() {
		validateUser();
		return false;
	});
	$('#forgotpwd').submit(function() {
		validateFogotPassword();
		return false;
	});
	/**
	 * validRules oject contain all the messages that are visible when an elment
	 * value is valid
	 * 
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
	 * 
	 * structure to define a message for element key is to be element name Value is
	 * message
	 */
		var focusRules = {

				
				};
		
/**
 * loginValidation action for login form validation through validate.js
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
						required : __("Please enter your Username."),
						email: __("Please enter valid Username.")
						
					},
					pwd : {
						required : __("Please enter your  password."),
						minlength : __("Please enter minimum 8 characters")
					},
					
				},

				onfocusin : function(element) {
					if (!$(element).hasClass('success') && $(element).val()=='') 
					{
						this.showLabel(element,focusRules[element.name]);
						$(element).removeClass(this.settings.errorClass)
						.removeClass(this.settings.validClass).addClass('focus');
					
						$('span.help-inline').removeClass('error')
						.removeClass('success').addClass('focus');
						
					}
				},

				highlight : function(element,errorClass, validClass) {

					 $(element).removeClass(validClass).addClass(errorClass);
					 $('span.help-inline').removeClass('success').addClass('error');
				},
				unhighlight : function(element,	errorClass, validClass) {

					
					if(element.name == "pwd" && ! this.settings.rules[element.name].required ())
					{
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
 * forgotPassValidation action for forgot Password form validation through validate.js
 */
function forgotPassValidation(){
	$("form#forgotpwd")
	.validate(
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
					required : __("Please enter your Username."),
					
					email: __("Please enter valid Username.")
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
				},
				highlight : function(element,errorClass, validClass) {

					 $(element).removeClass(validClass).addClass(errorClass);
					 $('span.help-inline').removeClass('success').addClass('error');
				},
				unhighlight : function(element,	errorClass, validClass) {

					$(element).removeClass(errorClass).addClass(validClass);
					$('span.help-inline').html(validRules[element.name]).removeClass(errorClass).addClass(validClass);

				},

			});
	}
});
/*$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});*/
/**
 * checks whether username of password correct or 
 * not through validatelogin under auth controller  
 */
function validateUser()
{
	if ($("#form1").valid()) {
		$('#error-msg').html(__('Valideren...')).removeClass('error').removeClass('focus').removeClass('success').addClass('validating');
		$.ajax({
			url : HOST_PATH + "admin/auth/validatelogin",
			method : "post",
			data : {
				username:$('input#uname').val(),
				password:$('input#pwd').val(),
				},
			dataType : "json",
			type : "post",
			success : function(data) {
				if(data!=1)
					{
						$('#error-msg').html(__('<span class="help-inline focus success" for="pwd" generated="true" haserror="true">Valid password.</span>'));
						$('span.help-inline').html(data).removeClass('success').addClass('error');
						$('#error-msg').removeClass('validating');
						$('input#uname').removeClass('success');
						$('input#pwd').val('').removeClass('success');
					}
				else {
					
					window.location = HOST_PATH + "admin/user";
				}
			}
				
		});
	
	}
}
/**
 * Checks whether UserName valid or not through validateforgotpassword() action in auth controller. 
 */
function validateFogotPassword()
{
	if ($("#forgotpwd").valid()) {
		$('#error-msg').html(__('Valideren...')).removeClass('error').removeClass('focus').removeClass('success').addClass('validating');
			$.ajax({
				url : HOST_PATH + "admin/auth/validateforgotpassword",
				method : "post",
				data : {
					email:$('input#email').val()
				
					},
				dataType : "json",
				type : "post",
				success : function(data) {
					
					if(data==2)
						{
							window.location = HOST_PATH + "admin/auth/pwdresetsuccessfully";
						
						} else if(data!=1) {
							$('#error-msg').html(__('<span class="help-inline focus success" for="pwd" generated="true" haserror="true">Valid UserName. </span>'));
							$('span.help-inline').html(data).removeClass('success').addClass('error');
							$('#error-msg').removeClass('validating');
							$('input#email').removeClass('success');
						
						}
				}
			});
		}
}