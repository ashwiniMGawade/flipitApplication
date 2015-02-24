$(document).ready(function(){
    loadSocialCodeForm();
});
function loadSocialCodeForm() {
    $.ajax({
        url : HOST_PATH_LOCALE + 'store/socialcode',
        type: 'post',
        dataType: 'json',
        success: function(data) {
            $('aside#sidebar').append(data);
        }
    });
}
