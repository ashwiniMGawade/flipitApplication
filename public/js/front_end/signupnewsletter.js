// refactored code 
function setHiddenFieldValue(){
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
                                    $(formName + " input#emailAddress").removeClass('input-error')
                                      .addClass('input-success');
                                } else {
                                    $(formName + " input#emailAddress").removeClass('input-success')
                                        .addClass('input-error');
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
                        if($(element).attr('type') == 'text') {
                            var label = this.errorsFor(element);
                             if( $( label).attr('hasError')) {
                                 if($( label).attr('remote-validated') != "true") {
                                        $(element).removeClass('input-error input-success input-error input-success');
                                     }
                             } else {
                                $(element).removeClass('input-error input-success input-error input-success');
                             }
                        }
                        else{
                            $(element).removeClass('input-error input-success input-error input-success');
                            
                        }
                },
                highlight : function(element,
                        errorClass, validClass) {
                        $(element).removeClass('input-success')
                        .addClass('input-error input-error');
                },
                success: function(label , element) {
                        $(element).removeClass('input-error')
                          .addClass('input-success input-success');
                        $(label).removeClass('input-error help-inline')
                        .html('').addClass('input-success');
                }
            });
}