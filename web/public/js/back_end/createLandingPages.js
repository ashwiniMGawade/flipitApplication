var validRules = {
    shopName : __("Shop name looks great"),
    permalink : __("Permalink looks great"),
    title : __("Landing Page Title looks great"),
    subTitle : __("Landing Page Subtitle looks great"),
    overwriteTitle : __("Overwrite Title looks great"),
    metaDescription : __("Meta Description looks great"),
    pageContent : __("Landing Page Content looks great")
};

var focusRules = {
    shopName : __("Select shop name"),
    permalink : __("Enter Permalink"),
    title : __("Enter Landing Page Title"),
    subTitle : __("Enter Landing Page Subtitle"),
    overwriteTitle : __("Enter Overwrite Title"),
    metaDescription : __("Enter Meta Description"),
    pageContent : __("Enter Landing Page Content")
};

jQuery.noConflict();
jQuery(document).ready(init);

function init() {
    jQuery("#shopName").select2({placeholder: __("Select a Shop")});
    jQuery("#shopName").change(function(){
        jQuery("#selectedShop").val(jQuery(this).val());
    });

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
}
