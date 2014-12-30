$(document).ready(function(){
    validateProfile();
    $("input#resetPassword").submit(function(){
        if($("form#resetPassword").valid()){
            return true;
        } else{
            return false;
        }
    });
});

var validator =  null;
function validateProfile() {
    validator = $('form#resetPassword')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            password: {
                required: true,
                minlength : 1,
                maxlength :20,
            },
            confirmPassword: {
                required: true,
                minlength : 1,
                maxlength :20,
                equalTo : "#password"
            }
        },
        messages : {
              password: {
                  required: '',
                  minlength : '',
                  maxlength: ''
              },
              confirmPassword: {
                  required: '',
                  minlength : '',
                  maxlength: '',
                  equalTo: ''
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