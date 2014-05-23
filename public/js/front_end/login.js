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
/*var validRules = {
		
		uname : __("E-mail ziet er geweldig uit"),
		pwd : __("ok! goed wachtwoord")
};*/
/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {
	
		uname : " ",
		pwd   : " "
};
$(document).ready(function() {
	validatelogin();
	$("input#login").click(function(event) {
		if($("#login").valid()==false){
			 return false;
		} else {
			return true;
		}
	});
	$('input#emailAddress').bind('keypress', function(event) {
		  if(event.keyCode === 9) {
            $('#password').focus();
            return false;
		  }
		});
	$("input").keypress(function(event) {
	    if (event.which == 13) {
	    	if($("#login").valid()==true){
	    	return true;
	    	}else{
	    		return false;
	    	}
	    }
	});
});

var validator = null; 
function validatelogin()
{
	validator = $('form#login')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            emailAddress : {
                required: true,
                email: true,
            },
            password: {
                required: true,
            }
        },
        messages : {
             emailAddress : {
                 required: '',
                 email: ''
              },
              password: {
                  required: '',
              }
        },
        onfocusin : function(element) {
            if($(element).valid() == 0){
                $(element).removeClass('input-error').removeClass('input-success');
            } else {
                $(element).removeClass('input-error').addClass('input-success');
            }
        },
        onfocusout :function(element) {
            if($(element).valid() == 0){
                $(element).removeClass('input-success').addClass('input-error');
            } else {
                $(element).removeClass('input-error').addClass('input-success');
            }
         },
        highlight : function(element, errorClass, validClass) {
            $(element).addClass(errorClass).removeClass(validClass);
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass(errorClass);
        },
        success: function(element, errorClass, validClass) {
            $(element).removeClass(errorClass).addClass(validClass);
        }
    });
}


