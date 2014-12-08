
$(document).ready(function(){
	$('input#shopId').val($('input#currentShop').val())
	$('#expireDate').datepicker({
		dateFormat: "dd-mm-yy"
	});
    validateAddSocialCode();
    $("#sharecode").submit(function(){
        if($("form#socialcodeForm").valid()){
          return true;
        } else{
          return false;
        }
    });
});
var validator =  null;
function validateAddSocialCode() {
    validator = $('form#socialcodeForm')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            nickname: {
                required: true
            },
            title: {
                required: true
            },
            offerUrl: {
                required: true,
                regex  :/((http|https):\/\/)([_a-z\d\-]+(\.[_a-z\d\-]+)+)(([_a-z\d\-\\\.\/]+[_a-z\d\-\\\/])+)*/

            },
            code: {
                required: true
            },
            expireDate: {
                required: true
            },
            offerDetails: {
                required: true
            }
        },
        messages : {
            nickname : {
                required: ''
              },
            title: {
                required:''
            },
            offerUrl: {
                required:'',
                regex : ''
            },
            code: {
                  required: ''
              },
            expireDate: {
                required: '',
                date : ''
            },
            offerDetails: {
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