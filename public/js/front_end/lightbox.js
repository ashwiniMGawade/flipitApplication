function showLightboxPopUp(id) {
    $('#element_to_pop_up').html('');
    if (! ( /(iPod|iPhone|iPad)/i.test(navigator.userAgent) )) {
        customPopUp('element_to_pop_up');
        $.ajax({
            url : HOST_PATH_LOCALE + "store/how-to-use-guide-lightbox",
            method : "post",
            data : {
                'id' : id
            },
            type : "post",
            success : function(data) {
                $('#element_to_pop_up').html(data);
                $('#code-lightbox').show();
            }
        });
    }
}