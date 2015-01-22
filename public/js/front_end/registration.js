var date = new Date();
var fullYear = date.getFullYear();
$(document).ready(function(){
    validateRegistration();
    $("input#register").submit(function(){
        if($("form#registerForm").valid()){
          return true;
        } else{
          return false;
        }
    });
});

var validator =  null;
function validateRegistration() {
    validator = $('form#registerForm')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            emailAddress : {
                required: true,
                email: true,
                remote: {
                    url : HOST_PATH_LOCALE
                    + "signup/checkuser",
                    type : "post",
                    beforeSend : function(xhr) {},
                complete : function(data) {
                    if (data.responseText == 'true') {
                        $("form#registerForm input#emailAddress").addClass('input-success').removeClass('input-error');
                    } else {
                        $("form#registerForm input#emailAddress").addClass('input-error').removeClass('input-success');
                    }
                }}
            },
            firstName: {
                required: true
            },
            lastName: {
                required: true
            },
            gender: {
                selectcheck: true
            },
            password: {
                required: true,
                minlength : 1,
                maxlength :20
            }
        },
        messages : {
             emailAddress : {
                 required: '',
                 email: '',
                 remote:''
              },
              firstName: {
                  required:''
               },
              lastName: {
                  required:''
               },
              gender: {
                  selectcheck: ''
              },
              password: {
                  required: '',
                  minlength : '',
                  maxlength: ''
              }
        },
        onfocusin : function(element) {
            if($(element).valid() == 0){
                $(element).removeClass('input-error').removeClass('input-success');
                $(element).next('label').hide();
            } else {
                $(element).removeClass('input-error').addClass('input-success');
                $(element).next('label').hide();
            }
        },
        onfocusout :function(element) {
            if($(element).valid() == 0){
                $(element).removeClass('input-success').addClass('input-error');
                $(element).next('label').hide();
            } else {
                $(element).removeClass('input-error').addClass('input-success');
                $(element).next('label').hide();
            }
         },
        highlight : function(element, errorClass, validClass) {
            $(element).addClass(errorClass).removeClass(validClass);
            $(element).next('label').hide();
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass(errorClass);
            $(element).next('label').hide();
        },
        success: function(element, errorClass, validClass) {
            $(element).removeClass(errorClass).addClass(validClass);
            $(element).next('label').hide();
        }
    });
}