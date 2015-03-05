$(document).ready(function(){
    loadSocialCodeForm();
});

function loadSocialCodeForm() {
    $.ajax({
        url : HOST_PATH_LOCALE + 'socialcode/social-code',
        type: 'get',
        dataType: 'json',
        data:{id: $('input#currentShop').val()},
        success: function(data) {
            if (data != null) {
                appendSocialCodeForm(data);
            }
        }
    });
}

function appendSocialCodeForm(data) {
    $('aside#sidebar .widget').remove();
    $('aside#sidebar').append(data);
}

$(document).ajaxStop(function() {
    validateAddSocialCode();
    $("#shareCode").click(function() {
        if ($("form#socialCodeForm").valid()) {
            saveSocialCode();
            return false;
        } else {
            return false;
        }
    });
    $('body').click(function(event) {
        var clickedId = event.target.id;
        if (clickedId == "searchShops") {
            return false;
        }
    });
    if ($("input#searchShops").length) {
        $("input#searchShops").autocomplete({
            minLength : 1,
            search: function(event, ui) {
                $('.ajax-autocomplete ul').empty();
            },
            select: function(event, ui) {
                $('input#shopPermalink').val(ui.item.permalink);
            },
            source :  function(request, response) {
                var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex(request.term), "i");
                response($.grep(shopsJSON, function(item) {
                    return matcher.test(item.label);
                }));
            },
            focus: function(event, ui) {
                $('li.wLi2').removeClass('select');
                $('a#ui-active-menuitem').parents('li').addClass('select');
            },
        }).data("autocomplete")._renderItem = function(ul, item, url) {
            url = item.permalink;
            return $("<li class='wLi2'></li>").data("item.autocomplete", item).append(
                $('<a href="javascript:void(0);" onClick="setInHidden(\'' + url + '\')"></a>').html((__highlight(
                    item.label,
                    $("input#searchShops").val()
                ))))
            .appendTo(ul);
        };  
        $("input#searchShops").keypress(function(event) {
            $('ul.ui-autocomplete').addClass('wd1');
        });
    }
});

function __highlight(s, t) {
    var matcher = new RegExp("(" + $.ui.autocomplete.escapeRegex(t) + ")", "ig");
    return s.replace(matcher, '<span>$1</span>');
}

function setInHidden(url) {
    $('input#shopPermalink').val(url);
}

function saveSocialCode() {
    $.ajax({
        url : HOST_PATH_LOCALE + 'socialcode/social-code',
        method : "post",
        data: $('form#socialCodeForm').serialize(),       
        dataType : "json",
            type : "post",
            success : function(data) {
            if (data != null) {
                appendSocialCodeForm(data);
            }
        }
    });
}
var validator =  null;
function validateAddSocialCode() {
    validator = $('form#socialCodeForm')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            shops: {
                required: true,
                remote: {
                    url : HOST_PATH_LOCALE
                    + "socialcode/check-store",
                    type : "post",
                    beforeSend : function(xhr) {},
                    complete : function(data) {
                    if (data.responseText == 'false') {
                        $("input#searchShops").addClass('input-error').removeClass('input-success');
                    } else {
                        $("input#searchShops").addClass('input-success').removeClass('input-error');
                        $("input#shopPermalink").val(data.responseText);
                    }
                }}
            },
            code: {
                required: true,
                regex : /^[a-zA-Z0-9]*$/
            },
            expireDate: {
                required: true,
                checkTodayDate: true,
                regex: /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(|-|)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(|-|)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(|-|)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/
            },
            offerDetails: {
                required: true,
                regex : /^(?![0-9]*$)[a-zA-Z0-9 ]+$/
            }
        },
        messages : {
            shops : {
                required: '',
                remote:''
            },
            code: {
                required: '',
                regex : ''
            },
            expireDate: {
                required: '',
                checkTodayDate: '',
                regex : ''
            },
            offerDetails: {
                required: '',
                regex : ''
            }
        },
        onfocusin : function(element) {
            if($(element).valid() == 0) {
                $(element).removeClass('input-error').removeClass('input-success');
                $(element).next('label').hide();
            } else {
                $(element).removeClass('input-error').addClass('input-success');
                $(element).next('label').hide();
            }
        },
        onfocusout :function(element) {
            if($(element).valid() == 0) {
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