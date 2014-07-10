$(document).ready(function(){
    $("form#emailSettings").validate({
        errorClass : 'error',
        validClass : 'success',
        errorElement : 'span',
        ignore: ":hidden",
        errorPlacement : function(error, element) {
            element.parent("div").next("div").html(error);
        },
        rules : {
            senderEmail : {
                required : true,
                email : true
            },
            senderName : {
                required : true
            },                 
        },

        onfocusin : function(element) {
            if (!$(element).parent('div').prev("div")
            .hasClass('success')) {
                var label = this.errorsFor(element);

                if( $( label ).attr('hasError')  ) {
                    if($( label ).attr('remote-validated') != "true")
                    {
                    $(element).parent('div').removeClass(
                        this.settings.errorClass)
                    .removeClass(
                        this.settings.validClass)
                    .prev("div")
                    .addClass('focus')
                    .removeClass(
                        this.settings.errorClass)
                    .removeClass(
                        this.settings.validClass);
                    }
                } else {
                    $(element).parent('div').removeClass(
                    this.settings.errorClass)
                    .removeClass(
                    this.settings.validClass)
                    .prev("div")
                    .addClass('focus')
                    .removeClass(
                    this.settings.errorClass)
                    .removeClass(
                    this.settings.validClass);
                }
            }
        },

        highlight : function(element,
        errorClass, validClass) {
            $(element).parent('div')
            .removeClass(validClass)
            .addClass(errorClass).next(
            "div").removeClass(
            validClass)
            .addClass(errorClass);
        },

        unhighlight : function(element,
        errorClass, validClass) {
            if($(element).val() != ""){
                $(element).parent('div')
                .removeClass(errorClass)
                .addClass(validClass).prev(
                "div").addClass(
                validClass)
                .removeClass(errorClass);
            }        
        },

        success: function(label , element) {
            if($(element).val() != ""){
                $(element).parent('div')
                .removeClass(this.errorClass)
                .addClass(this.validClass).prev(
                "div").addClass(
                this.validClass)
                .removeClass(this.errorClass);
                $(label).remove();
                label.addClass('valid');
            }
        }
    });
});