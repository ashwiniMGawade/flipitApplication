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
                                 + "signup/checkuser",
                            type : "post",
                            beforeSend : function(xhr) {
                            },
                            complete : function(data) {
                                if (data.responseText == 'true') {
                                    $(formName + " input#emailAddress").addClass('input-success').removeClass('input-error');
                                    $(formName + " input#emailAddress").next('label').remove();
                                } else {
                                    $(formName + " input#emailAddress").addClass('input-error').removeClass('input-success');
                                    $(formName + " input#emailAddress").next('label').remove();
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
                        $(element).next('label').remove();
                    } else {
                        $(element).removeClass('input-error').addClass('input-success');
                        $(element).next('label').remove();
                    }
                },
                onfocusout :function(element) {
                    if($(element).valid() == 0){
                        $(element).removeClass('input-success').addClass('input-error');
                        $(element).next('label').remove();
                    } else {
                        $(element).removeClass('input-error').addClass('input-success');
                        $(element).next('label').remove();
                    }
                 },
                highlight : function(element, errorClass, validClass) {
                    $(element).addClass(errorClass).removeClass(validClass);
                    $(element).next('label').remove();
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass(errorClass);
                    $(element).next('label').remove();
                },
                success: function(element, errorClass, validClass) {
                    $(element).removeClass(errorClass).addClass(validClass);
                    $(element).next('label').remove();
                }
            });
}