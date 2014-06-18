$(document).ready(function(){
    validateRegistration();
 });
var validator =  null;
function validateRegistration() {
    validator = $('form#contactform')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            email : {
                required: true,
                email: true,
                
            },
            name: {
                required: true
            },
            subject1: {
                required: true
            },
            message: {
                required: true
            }
        },
        messages : {
             email : {
                 required: '',
                 email: ''
              },
              name: {
                  required:''
               },
              subject1: {
                  required:''
               },
              message: {
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