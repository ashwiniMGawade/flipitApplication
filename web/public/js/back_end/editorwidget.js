$(document).ready(function() {
    validateWidgetData();
    CKEDITOR.replace(
        'description',
        {
            customConfig : 'config.js',  
            toolbar :  'BasicToolbar',
            height : "300"
        }
    );
});

function changeAction(e, type)
{
    var id = $(e).attr("id");
    if (id=='btnNo') {
        $('#btnNo').addClass('btn-primary');
        $('#btnYes').removeClass('btn-primary');
    } else {
        $('#btnYes').addClass('btn-primary');
        $('#btnNo').removeClass('btn-primary');
        $('#redirectTo').parent('div').removeClass('error')
            .removeClass('success')
            .prev("div")
            .addClass('focus')
            .removeClass('error')
            .removeClass('error');
        $('span[for=redirectTo]').remove();
    }
    $("input#actionType").val(type);
}

function getPageTypeWidgetData() {
    var pageType = $("#pageType").val();
    $.ajax({
        url: HOST_PATH + "admin/popularcode/page-type-detail",
        dataType: 'json',
        data: {'pageType' : pageType},
        success: function(dataSet) {
            if (dataSet != '') {
                CKEDITOR.instances['description'].setData(dataSet[0].description);
                $("#subtitle").val(dataSet[0].subtitle);
                $("#selecteditors").val(dataSet[0].editorId);
                if (dataSet[0].status) {
                    $('#btnYes').addClass('btn-primary');
                    $('#btnNo').removeClass('btn-primary');
                    $("#actionType").val(1);
                } else {
                    $('#btnNo').addClass('btn-primary');
                    $('#btnYes').removeClass('btn-primary');
                    $("#actionType").val(0);
                }
                
            } else {
                CKEDITOR.instances['description'].setData('');
                $("#description").val('');
                $("#subtitle").val('');
                $("#selecteditors").val('');
                $("#btnYes").val(1);
            }
        } 
    }); 
}

function validateWidgetData() {
    validator = $('form#addEditorWidgetForm')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            type: {
                required: true
            },
            selecteditors: {
                required: true
            },
            subtitle: {
                required: true
            },
            description: {
                required: true
            }
        },
        messages : {
            type: {
                required:''
            },
            selecteditors: {
                required: ''
            },
            subtitle: {
                required: ''
            },
            description: {
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
