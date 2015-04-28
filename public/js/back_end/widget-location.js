var validRules = {
    widgetPostion : ""
};

var focusRules = {
    widgetPostion : ""
};

$(document).ready(init);

function init() {
    validateWidgetlocationForm();
}

function showLightBoxForWidgetLocation() {
    showModel1("", "", "add");
}

function showModel1(id,rootid,type) {
    removeBorders();
    $('#myModal1').modal('show');
}

function removeBorders() {
    $("div.mainpage-content-right").removeClass("error").removeClass('success')
    .prev("div").removeClass('focus').removeClass('error').removeClass('success') ;
}

function hideModel() {
    $('#myModal1').modal('hide');
    window.location.reload();
    return false;
}

function showWidgetPosition() {
    $.ajax({
        url : HOST_PATH + "admin/widgetlocation/get-widget-position",
        type : "post",
        data : {
            'pageType' : $('#pageType').val(),
            'widgetLocation' : $('#widgetlocation').val()
        },
        dataType : "json",
        success : function(data) {
            if (data!='') {
                $('#widgetPostion').val(data[0].position);
            }
        }
    });
}

function validateWidgetlocationForm() {
    var validateNewMenu = $("form#widgetlocationForm").validate({ 
        errorClass : 'error',
        validClass : 'success',
        errorElement : 'span',
        ignore: ".ignore, :hidden",
        errorPlacement : function(error, element) {
            element.parent("div").prev("div").html(error);
        },
        rules : {
            widgetPostion : {
                required : true,
                number : true
            }
        },
        messages : {
            widgetPostion : {
                required : "",
                number : ""
            }
        },
        onfocusin : function(element) {
            if (!$(element).parent('div').prev("div").hasClass('success')) {
                var label = this.errorsFor(element);
                if ($(label).attr('hasError')) {
                    if ($(label).attr('remote-validated') != "true") {
                        this.showLabel(element, focusRules[element.name]);
                        $(element).parent('div').removeClass(this.settings.errorClass)
                            .removeClass(this.settings.validClass).prev("div")
                            .addClass('focus').removeClass(this.settings.errorClass)
                            .removeClass(this.settings.validClass);
                    }
                } else {
                    this.showLabel(element, focusRules[element.name]);
                    $(element).parent('div').removeClass(this.settings.errorClass).removeClass(this.settings.validClass)
                        .prev("div").addClass('focus').removeClass(this.settings.errorClass).removeClass(
                        this.settings.validClass);
                }
            }
        },
        highlight : function(element,errorClass, validClass) {
            $(element).parent('div').removeClass(validClass).addClass(errorClass).prev("div").removeClass(validClass)
                .addClass(errorClass);
            $('span.help-inline', $(element).parent('div').prev('div')).removeClass(validClass) ;
        },
        unhighlight : function(element,
        errorClass, validClass) {
            var showError = false ;
            switch(element.nodeName.toLowerCase()) {
                case 'select' :
                    var val = $(element).val();
                    if ($($(element).children(':selected')).attr('default') == undefined) {
                        showError = true ;
                    } else {
                        showError  = false;
                    }
                    break ; 
                case 'input':
                    if (this.checkable(element)) {
                        showError = this.getLength(element.value, element) > 0;
                    } else if($.trim(element.value).length > 0) {
                        showError =  true ;
                    } else {
                        showError = false ;
                    }
                    break; 
                default:
                    var val = $(element).val();
                    showError =  $.trim(val).length > 0;
            }
            if (! showError){
                $('span.help-inline', $(element).parent('div').prev('div')).hide();
                $(element).parent('div').removeClass(errorClass).removeClass(validClass).prev("div")
                    .removeClass(errorClass).removeClass(validClass);
            } else {
                if (element.type !== "file") {
                    $(element).parent('div').removeClass(errorClass).addClass(validClass).prev("div")
                        .addClass(validClass).removeClass(errorClass);
                    $('span.help-inline', $(element).parent('div').prev('div')).text(validRules[element.name]).show();
                } else {
                    $(element).parent('div').removeClass(errorClass).removeClass(validClass).prev("div")
                        .removeClass(errorClass).removeClass(validClass);
                }
            }
        },
        submitHandler: function(form) {
            $.ajax({
                url : HOST_PATH + "admin/widgetlocation/save-or-update-widget-location",
                type : "post",
                data : $("#widgetlocationForm").serialize(),
                dataType : "json",
                success : function(data) {
                    if (data == true) {
                        $('div#message-display').html(__('Record has been saved successfully'))
                            .addClass('widget-location-success-message');
                        window.location.reload();
                    } else {
                        $('div#message-display').html(__('You have entered wrong data'))
                            .addClass('widget-location-error-message');
                    }
                }
            });
        }
    });
}