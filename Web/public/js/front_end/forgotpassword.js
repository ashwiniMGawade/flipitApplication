$(document).ready(function() {
    validateForgotPassword();
    $("input#forgotPassword").click(function(event) {
        if($("#forgotPassword").valid()==false){
            return false;
        } else {
            return true;
        }
    });
    $("input").keypress(function(event) {
        if (event.which == 13) {
            if($("#forgotPassword").valid()==true){
                return true;
            }else{
                return false;
            }
        }
    });
});

var validator = null; 
function validateForgotPassword()
{
    validator = $('form#forgotPassword')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            emailAddress : {
                required: true,
                email: true
            }
        },
        messages : {
             emailAddress : {
                required: '',
                email: ''
              }
        },
        onfocusin : function(element) {
            if($(element).valid() == 0) {
                $(element).removeClass('input-error').removeClass('input-success');
            } else {
                $(element).removeClass('input-error').addClass('input-success');
            }
        },
        onfocusout :function(element) {
            if($(element).valid() == 0) {
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
