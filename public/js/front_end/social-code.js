$(document).ready(function(){
    loadSocialCodeForm();
});

function loadSocialCodeForm() {
    $.ajax({
        url : HOST_PATH_LOCALE + 'store/social-code',
        type: 'get',
        dataType: 'json',
        success: function(data) {
        	$('aside#sidebar .widget').remove();
            $('aside#sidebar').append(data);
        }
    });
}
