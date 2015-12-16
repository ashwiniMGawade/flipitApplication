/**
 * validRules object contain all the messages that are visible when an elment
 * value is valid
 *
 * structure to define a message for element: key is to be element name and Value is
 * message
 */
var validRules = {
    "campaign-header" : __("Campaign header looks great"),
    "campaign-footer" : __("Campaign foorer looks great")
};

/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 *
 * structure to define a message for element : key is to be element name and Value is
 * message
 */
var focusRules = {
    "campaign-header" : __("Please enter campaign header"),
    "campaign-footer" : __("Please enter campaign footer")
};

var invalidForm = {} ;
var errorBy = "" ;

$.validator.setDefaults({
    onkeyup : false,
    onfocusout : function(element) {
        $(element).valid();
    }

});

function resetBorders(el)
{
    $(el).each(function(i,o){
        $(o).parent('div')
            .removeClass("error success")
            .prev("div").removeClass('focus error success') ;

    });
}

var init = function ()
{
    $("form").submit(function(e){
       // e.preventDefault();
        if (! jQuery.isEmptyObject(invalidForm) ) {
            for (var i in invalidForm) {
                if (invalidForm[i]) {
                    $('#saveSettings').removeAttr('disabled');
                    return false;
                }
            }
        }
    });

    //function call to validate new category
    validateSettingsForm();
}

/**
 * form validation during add category
 * @author blal
 */
function validateSettingsForm(){
    validatorForSettings = $("form#campaignsettings").validate(
        {
            errorClass : 'error',
            validClass : 'success',
            errorElement : 'span',
            ignore: ".ignore, :hidden",
            afterReset  : resetBorders,
            errorPlacement : function(error, element) {
                element.parent("div").prev("div")
                    .html(error);
            },
            rules : {
                "campaign-header": {
                    required : true,
                    minlength : 10,
                },
                "campaign-footer": {
                    required : true,
                    minlength : 10,
                }
            },
            messages : {
                "campaign-header": {
                    required : __("Please enter campaign header"),
                    minlength : __("Please enter atleast 10 characters")
                },
                "campaign-footer"  : {
                    required : __("Please enter campaign footer"),
                    minlength : __("Please enter atleast 10 characters")
                }
            },
            onfocusin : function(element) {
                // display hint messages when an element got focus
                if (!$(element).parent('div').prev("div")
                        .hasClass('success')) {

                    var label = this.errorsFor(element);

                    if( $( label ).attr('hasError')  ) {
                        if($( label ).attr('remote-validated') != "true") {
                            this.showLabel(element, focusRules[element.name]);

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
                        this.showLabel(element, focusRules[element.name]);
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

            highlight : function(element, errorClass, validClass) {
                // highlight borders in case of error
                $(element).parent('div')
                    .removeClass(validClass)
                    .addClass(errorClass).prev(
                    "div").removeClass(
                    validClass)
                    .addClass(errorClass);

                $('span.help-inline', $(element).parent('div')
                    .prev('div')).removeClass(validClass) ;



            },
            unhighlight : function(element, errorClass, validClass) {
                // check for ignored elemnets and highlight borders when succeed
                if(! $(element).hasClass("ignore")) {
                    $(element).parent('div')
                        .removeClass(errorClass)
                        .addClass(validClass).prev(
                        "div").addClass(
                        validClass)
                        .removeClass(errorClass);
                    $(
                        'span.help-inline',
                        $(element).parent('div')
                            .prev('div')).text(
                        validRules[element.name]) ;
                } else {
                    // check to display errors for ignored elements or not
                    var showError = false ;
                    //
                    switch( element.nodeName.toLowerCase() ) {
                        case 'input':
                            if ( this.checkable(element) ) {

                                showError = this.getLength(element.value, element) > 0;

                            } else if($.trim(element.value).length > 0) {

                                showError =  true ;

                            } else {

                                showError = false ;
                            }

                            break ;
                        default:
                            var val = $(element).val();
                            showError =  $.trim(val).length > 0;
                    }
                    if(! showError ) {
                        // hide errors message and remove highlighted borders
                        $(
                            'span.help-inline',
                            $(element).parent('div')
                                .prev('div')).hide();

                        $(element).parent('div')
                            .removeClass(errorClass)
                            .removeClass(validClass)
                            .prev("div")
                            .removeClass(errorClass)
                            .removeClass(validClass) ;
                    } else {
                        // show errors message and  highlight borders

                        // display green border and message
                        $(element).parent('div')
                            .removeClass(errorClass)
                            .removeClass(validClass)
                            .removeClass("focus")
                            .prev("div")
                            .removeClass("focus")
                            .removeClass(errorClass)
                            .removeClass(validClass) ;
                    }
                }

            },
            success: function(label , element) {
                $(element).parent('div')
                    .removeClass(this.errorClass)
                    .addClass(this.validClass).prev(
                    "div").addClass(
                    this.validClass)
                    .removeClass(this.errorClass)
                    .removeClass("focus");
                $(label).append( validRules[element.name] ) ;
                label.addClass('valid') ;
            }
        });
}

$(document).ready(function() {
    init();
    $('form#campaignsettings').submit(function () {
        if ($("form#campaignsettings").valid()) {
            $('#saveSettings').attr('disabled', "disabled");
            return true;
        } else {
            return false;
        }
    });
});


