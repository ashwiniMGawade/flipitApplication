$(document).ready(function() {
    if (getQueryStringParams("popup")) {
        showCodePopUp('popupOnLoad');
        if (getQueryStringParams("type") == 'code') {
        showCodeInformation(getQueryStringParams("popup"));
       }
    }
});
function OpenInNewTab(url) {
    var windowObject=window.open(url, '_blank');
    windowObject.focus();
}
function showTermAndConditions(id) {
    $('#termAndConditions'+id).toggle();
    $('a#termAndConditionLink'+id).toggleClass('uparrow'); 
}
function showPopupTermAndConditions(id) {
    $('div#termAndConditionsPopup'+id).slideToggle();
    $('a#termAndConditionLinkPopup'+id).toggleClass('uparrow'); 
}
function showCodeInformation(id) {
    $('div#offerCodeDiv'+id).show();
    $('div#websiteOfferLink'+id).show();
    $('div#offerButton'+id).hide();
}
function printIt(urlToShow) {
    var windowObject = window.open();
    self.focus();
    windowObject.document.open();
    windowObject.document.write('<'+'html'+'><'+'body'+'>');
    windowObject.document.write('<img src ='+urlToShow+'>');
    windowObject.document.write('<'+'/body'+'><'+'/html'+'>');
    windowObject.document.close();
    windowObject.print();
    windowObject.close();
}

function getQueryStringParams(popupParameter) {
    var popupPageUrl = window.location.search.substring(1);
    var popupUrlVariables = popupPageUrl.split('&');
    for (var i = 0; i < popupUrlVariables.length; i++)
    {
        var popupParameterName = popupUrlVariables[i].split('=');
        if (popupParameterName[0] == popupParameter) 
        {
            return popupParameterName[1];
        }
    }
}

function showCodePopUp(event) {
    if(event == 'popupOnLoad') {
        var offerId = getQueryStringParams("popup");
        var offerVote = 0;
        var offerUrl = getQueryStringParams("printable");
    } else {
        var offerId = $(event).attr('id');
        var offerVote = $(event).attr('vote');
        var offerUrl = $(event).attr('alt');
    }
    
    $('#element_to_pop_up').html('');
    if(! ( /(iPod|iPhone|iPad)/i.test(navigator.userAgent) )) {
    customPopUp('element_to_pop_up');
            $.ajax({
                url : HOST_PATH_LOCALE + "offer/offer-detail",
                method : "post",
                data : {
                    'id' : offerId,
                    'vote' : offerVote,
                    'imagePath': offerUrl
                },
                type : "post",
                success : function(data) {
                    $('#element_to_pop_up').html(data);
                    $('#code-lightbox').show();
                    $("#bigtextPopupShopLink").bigtext();
                    $('#couponCode').click(function() {
                        $(this).focus().select();
                    });
                }
            });
    }
}

function customPopUp(id) {
    var popupId = id; 
    $('#' + popupId).css({
        "z-index" : 999999
    }).fadeIn();
    $('body').append('<div onClick="customPopUpClose();" id="fade"></div>');
    $('#fade').css({
        'filter' : 'alpha(opacity=80)'
    }).fadeIn();
    return false;
}

function customPopUpClose() {
    $('#fade , .popup_block, .popup_block_signup').fadeOut('9000', function() {
        $('#fade').remove(); 
    });
    return false;
}

