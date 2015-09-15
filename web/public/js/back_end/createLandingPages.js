var validRules = {
    permalink : __("Permalink looks great"),
    title : __("Title looks great")
};

var focusRules = {
    permalink : __("Enter Permalink"),
    title : __("Enter Title")
};

jQuery.noConflict();
jQuery(document).ready(init);

function init() {
    jQuery("#shopName").select2({placeholder: __("Select a Shop")});
    jQuery("#shopName").change(function(){
        jQuery("#selectedShop").val(jQuery(this).val());
    });
    if(jQuery("#selectedShop").val()) {
        jQuery("#shopName").select2("val", jQuery("#selectedShop").val());
    }
    if(jQuery("#status").val() == 1) {
        jQuery('#statusActive').addClass('btn-primary');
        jQuery('#statusInactive').removeClass('btn-primary');
    } else {
        jQuery('#statusInactive').addClass('btn-primary');
        jQuery('#statusActive').removeClass('btn-primary');
    }

    jQuery('#permalink').NobleCount('#permalinkLeft',{
        max_chars: 68,
        prefixString : __("Shop overwrite title length ")
    });
    jQuery('#title').NobleCount('#titleLeft',{
        max_chars: 150,
        prefixString : __("Shop meta description length ")
    });
    jQuery('#subTitle').NobleCount('#subTitleLeft',{
        max_chars: 512,
        prefixString : __("Shop reason sub title length ")
    });
    jQuery('#overwriteTitle').NobleCount('#overwriteTitleLeft',{
        max_chars: 512,
        prefixString : __("Shop reason sub title length ")
    });

    var options = {
        'maxCharacterSize': '',
        'displayFormat' : ''
    };

    jQuery('#metaDescription').textareaCount(options, function(data){
        jQuery('#metaDescriptionLeft').val(__("Shop meta description length ") + (data.input) + __(" characters"));
    });
    jQuery('#pageContent').textareaCount(options, function(data){
        jQuery('#pageContentLeft').val(__("Shop meta description length ") + (data.input) + __(" characters"));
    });

    CKEDITOR.replace('pageContent',
        {
            customConfig : 'config.js',
            toolbar :  'BasicToolbar',
            height : "300"
        }
    );

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

function validateForm() {
    jQuery('#createLandingPage').validate({
        errorClass : 'error',
        validClass : 'success',
        errorElement : 'span',
        ignore: ".ignore",
        errorPlacement : function(error, element) {
            element.parent("div").prev("div").html(error);
        },
        rules: {
            status : "required",
            selectedShop : "required",
            permalink : "required",
            title : {
                "required" : true
            }
        },
        messages : {
            status : {
                required : __("Please select a status")
            },
            selectedShop : {
                required : __("Please select a shop")
            },
            permalink : {
                required : __("Please enter a permalink")
            },
            title : {
                required : __("Please enter a title")
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