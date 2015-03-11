function loadSocialCodeForm() {
    $.ajax({
        url : HOST_PATH_LOCALE + 'socialcode/social-code/id/' + $('input#currentShop').val(),
        type: 'get',
        dataType: 'json',
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