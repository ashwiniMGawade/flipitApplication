$(document).ready(function(){
    loadSocialCodeForm();
});

function loadSocialCodeForm() {
    $.ajax({
        url : HOST_PATH_LOCALE + 'store/socialcode',
        type: 'get',
        dataType: 'json',
        success: function(data) {
        	$('aside#sidebar .widget').remove();
            $('aside#sidebar').append(data);
        }
    });
}
