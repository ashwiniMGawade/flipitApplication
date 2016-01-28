var validRules = {
    url : __("URL looks great")
};

var focusRules = {
    url : __("Enter URL")
};

jQuery.noConflict();
jQuery(document).ready(init);

jQuery.validator.addMethod("permalinkRegex", function(value, element) {
    return this.optional(element) || /^[a-z0-9\-_//]+$/i.test(value);
}, "Field must contain only letters, numbers, underscores or dashes.");

function init() {
    if(jQuery("#status").val() == 1) {
        jQuery('#statusActive').addClass('btn-primary');
        jQuery('#statusInactive').removeClass('btn-primary');
    } else {
        jQuery('#statusInactive').addClass('btn-primary');
        jQuery('#statusActive').removeClass('btn-primary');
    }

    if(jQuery("#hotjarStatus").val() == 1) {
        jQuery('#hotjarActive').addClass('btn-primary');
        jQuery('#hotjarInActive').removeClass('btn-primary');
    } else {
        jQuery('#hotjarInActive').addClass('btn-primary');
        jQuery('#hotjarActive').removeClass('btn-primary');
    }

    validateForm();
}

function setStatus(status) {
    jQuery('#' + status).addClass("btn-primary").siblings().removeClass("btn-primary");
    if(status == 'statusActive') {
        jQuery('#status').val(1);
    } else {
        jQuery('#status').val(0);
    }
}

function setHotjarStatus(status) {
    jQuery('#' + status).addClass("btn-primary").siblings().removeClass("btn-primary");
    if(status == 'hotjarActive') {
        jQuery('#hotjarStatus').val(1);
    } else {
        jQuery('#hotjarStatus').val(0);
    }
}

function validateForm() {
    jQuery('#editURLSetting').validate({
        errorClass : 'error',
        validClass : 'success',
        errorElement : 'span',
        ignore: ".ignore",
        errorPlacement : function(error, element) {
            element.parent("div").prev("div").html(error);
        },
        rules: {
            status : "required",
            url : {
                required : true,
                permalinkRegex: true,
                remote : {
                    url: HOST_PATH + "admin/urlsettings/validateurl",
                    type: "post",
                    data: {
                        url : function() {
                            return jQuery("#url").val();
                        },
                        editId : function() {
                            return jQuery( "#editId" ).val();
                        }
                    }
                }
            }
        },
        messages : {
            status : {
                required : __("Please select a status")
            },
            url : {
                required : __("Please enter a URL"),
                permalinkRegex : __("Invalid characters in URL"),
                remote: __("VWO Tag already added for this URL")
            }
        },
        onfocusin : function (element) {
            this.showLabel(element, focusRules[element.name]);
            jQuery(element).parent('div')
                .removeClass(this.settings.errorClass)
                .removeClass(this.settings.validClass)
                .prev("div")
                .addClass('focus')
                .removeClass(this.settings.errorClass)
                .removeClass(this.settings.validClass);
        },
        highlight : function (element, errorClass, validClass) {
            this.showLabel(element, focusRules[element.name]);
            jQuery(element).parent('div')
                .removeClass(validClass)
                .addClass(errorClass)
                .prev("div")
                .removeClass(validClass)
                .addClass(errorClass);
        },
        success: function (label, element) {
            jQuery(element).parent('div')
                .removeClass(this.errorClass)
                .addClass(this.validClass)
                .prev("div")
                .removeClass(this.errorClass)
                .addClass(this.validClass)
                .removeClass("focus");
            jQuery(label).append(validRules[element.name]);
            label.addClass('valid');
        }
    });
}