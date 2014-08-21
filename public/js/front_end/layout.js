
$(document).ready(function(){
    $(document).click('click', function(e) {
        if(!$(e.target).is('.dropdown-toggle')) {
            $('a.dropdown-toggle b').removeClass('arrow-menu-down').addClass('arrow-menu-up');
        }
    });
});
function showArticleOnClick(id) {
    if(id == 0) {
        $("#first").addClass("active");
        $("#second").removeClass("active");
        $("#third").removeClass("active");

    } else if(id == 1) {
        $("#second").addClass("active");
        $("#first").removeClass("active");
        $("#third").removeClass("active");

    } else {
        $("#third").addClass("active");
        $("#first").removeClass("active");
        $("#second").removeClass("active");
    }

}
function showErrow(){
    if($('ul li.dropdown').hasClass('open')==false) {
        $('a.dropdown-toggle b').removeClass('arrow-menu-up').addClass('arrow-menu-down');
    } else{
        $('a.dropdown-toggle b').removeClass('arrow-menu-down').addClass('arrow-menu-up');
    }
}
function viewCounter(eventType, type, id) {
    $.ajax({
        type : "POST",
        url : HOST_PATH_LOCALE + "viewcount/storecount/event/" + eventType + "/type/"
            + type + "/id/" + id,
        success : function() {
        }
    });
}
function ___addOverLay() {
    if( jQuery("div#overlay").length == 0) {
        var overlay = jQuery("<div id='overlay'><img id='img-load' src='" +  HOST_PATH  + "/public/images/back_end/ajax-loader.gif'/></div>");
        overlay.appendTo(document.body);
        return true;
    }
}

function ___removeOverLay() {
    jQuery('div#overlay').remove();
    return true ;
}