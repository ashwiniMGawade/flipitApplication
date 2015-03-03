$(document).ready(function(){
    $('input#shopId').val($('input#currentShop').val())
    $("#shareCode").click(function(){
        saveSocialCode();
        return false;
    });
});

function saveSocialCode() {
    $.ajax({
        url : HOST_PATH_LOCALE + 'store/socialcode',
        method : "post",
        data: $('form#socialCodeForm').serialize(),       
        dataType : "json",
            type : "post",
            success : function(data) {
            if (data != null) {
                $('aside#sidebar .widget').remove();
                $('aside#sidebar').append(data);
            } else {
                alert(__("Problem in your data"));
            }
        }
    });
}