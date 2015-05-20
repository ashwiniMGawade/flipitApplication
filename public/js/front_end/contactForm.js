$(document).ready(function(){
    validateRegistration();
 });
function ltrim(src){
    if (src.indexOf('/') === 0){
        src = src.substring(1);
      }
    return src;
}
$('#submit').click( function(e) {
    $.ajax({
        url : HOST_PATH_LOCALE + 'contact/getcontactformdetails',
        type: 'post',
        dataType: 'json',
        data: $('form#contactform').serialize(),
        success: function(data) {
          window.location.href = data.urlLocation;
        }
    });
    return false;
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
            subject: {
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
              subject: {
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