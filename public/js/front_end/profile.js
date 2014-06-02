$(document).ready(function(){
    validateProfile();
    $("input#profile").submit(function(){
        if($("form#profile").valid()){
          return true;
        } else{
          return false;
        }
    });
});
var validator =  null;
function validateProfile() {
    validator = $('form#profile')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            emailAddress : {
                required: true,
                email: true
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
                minlength : 1,
                maxlength :20
            },
            confirmPassword: {
                minlength : 1,
                maxlength :20,
                equalTo : "#password"
            },
            dateOfBirthDay: {
                required: true,
                digits : true
            },
            dateOfBirthMonth: {
                required: true,
                digits : true
            },
            dateOfBirthYear: {
                required: true,
                digits : true
            },
            postCode : {
                required: true
            }
        },
        messages : {
             emailAddress : {
                 required: '',
                 email: ''
              },
              firstName: {
                  required: ''
               },
              lastName: {
                  required: ''
               },
              gender: {
                  selectcheck: ''
              },
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
              },
              dateOfBirthDay: {
                  required: '',
                  digits :''
              },
              dateOfBirthMonth: {
                  required: '',
                  digits :''
              },
              dateOfBirthYear: {
                  required: '',
                  digits :''
              },
              postCode : {
                  required: ''
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