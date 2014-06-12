function setHiddenFieldValue() {
    $('input#shopId').val($('input#currentShop').val());
};
function signUpNewsLetter(formName){ 
    var formName = 'form#' + formName;
    validateSignUpNewsLetter(formName);
    if($(formName).valid()){
        $(formName).submit();
    }
    return false;
}
function validateSignUpNewsLetter(formName) {
    validator  = $(formName)
    .validate({
                errorClass : 'input-error',
                validClass : 'input-success',
                rules : {
                    emailAddress : {
                        required : true,
                        email : true,
                        remote : {
                            url : HOST_PATH_LOCALE
                                 + "login/checkuser",
                            type : "post",
                            beforeSend : function(xhr) {
                            },
                            complete : function(data) {
                                if (data.responseText == 'true') {
                                    $(formName + " input#emailAddress").addClass('input-success').removeClass('input-error');
                                } else {
                                    $(formName + " input#emailAddress").addClass('input-error').removeClass('input-success');
                                }
                            }
                        }
                    }
                },
                messages : {
                  emailAddress : {
                        required : '',
                        email : '',
                        remote :''
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