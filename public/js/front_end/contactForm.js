$(document).ready(function(){
    validateRegistration();
    var number1 = Math.floor((Math.random() * 10) + 1); 
    var number2 = Math.floor((Math.random() * 10) + 1);
    $("#number1").val(number1);
    $("#number2").val(number2);

 });
function ltrim(src){
    if (src.indexOf('/') === 0){
        src = src.substring(1);
      }
    return src;
}
$('#submit').click( function() {
    $('#contactform').attr('action', '');
    $.ajax({
        url : HOST_PATH_LOCALE + 'contact/getcontactformdetails',
        type: 'post',
        dataType: 'json',
        data: $('form#contactform').serialize(),
        success: function(data) {
        }
    });
});
$.validator.addMethod('captcha',
  function(value) {
    $result = ( parseInt($('#number1').val()) + parseInt($('#number2').val()) == parseInt($('#captcha').val()) ) ;
    $('#spambot').fadeOut('fast');
        return $result;
    },
        'Incorrect value, please try again.'
);

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
            },
            captcha: {
              required: true,
              captcha: true
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
              },
              captcha: {
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