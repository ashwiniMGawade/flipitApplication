function showLightboxPopUp(id) {
    $('#how_to_lightbox_pop_up').html('');
    if (! ( /(iPod|iPhone|iPad)/i.test(navigator.userAgent) )) {
        customPopUp('how_to_lightbox_pop_up');
        $.ajax({
            url : HOST_PATH_LOCALE + "store/how-to-use-guide-lightbox",
            method : "post",
            data : {
                'id' : id
            },
            type : "post",
            success : function(data) {
                $('#how_to_lightbox_pop_up').html(data);
            }
        });
    }
}

function showHowToLightboxPopup()
{
    customPopUp('how_to_lightbox_pop_up');
    fadeCustomPopup();
    $('#how_to_lightbox_pop_up, #how_to_lightbox_pop_up #code-lightbox').show();
}

showLightboxPopUp(relatedShopId);