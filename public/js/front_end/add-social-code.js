$(document).ready(function(){
    validateAddSocialCode();
    $('input#shopId').val($('input#currentShop').val())
    $("#shareCode").click(function(){
        if ($("form#socialCodeForm").valid()) {
            saveSocialCode();
        } else {
            return false;
        }
    });
});

function saveSocialCode() {
    $.ajax({
        url : HOST_PATH_LOCALE + 'store/social-code',
        method : "post",
        data: $('form#socialCodeForm').serialize(),       
        dataType : "json",
            type : "post",
            success : function(data) {
            if (data != null) {
                $('aside#sidebar .widget').remove();
                $('aside#sidebar').append(data);
            } else {
                alert(__("Problem in your data"));
            }
        }
    });
}
var validator =  null;
function validateAddSocialCode() {
    validator = $('form#socialCodeForm')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            shops: {
                required: true
            },
            code: {
                required: true,
                regex : /^[a-zA-Z0-9]*$/
            },
            expireDate: {
                required: true,
                regex: /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(|-|)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(|-|)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(|-|)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/
            },
            offerDetails: {
                required: true,
                regex : /^(?![0-9]*$)[a-zA-Z0-9 ]+$/
            }
        },
        messages : {
            shops : {
                required: ''
            },
            code: {
                required: '',
                regex : ''
            },
            expireDate: {
                required: '',
                regex : ''
            },
            offerDetails: {
                required: '',
                regex : ''
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