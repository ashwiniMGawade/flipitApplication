$(document).ready(function() {
    if (getQueryStringParams("popup")) {
        showCodePopUp('popupOnLoad');
        if (getQueryStringParams("type") == 'code') {
        showCodeInformation(getQueryStringParams("popup"));
       }
    }
    if ($('#userGenerated').val() == 1) {
       showTermAndConditions($('#userGenerated').attr('alt'));
    }
    $('a.btn-top').hide();
    var offersSectionArticlesCount = $('#content section.section:eq(0) article').length;
    offersSectionArticlesCount = offersSectionArticlesCount - 1;
    $(window).bind('scroll', function() {
        var offersSection = $('#content section.section:eq(0) article:eq('+ offersSectionArticlesCount +')').offset();
        if (offersSection.top <= $(window).scrollTop() ) {
            $("a.btn-top").fadeIn("slow");
        } else {
            $('a.btn-top').fadeOut("slow");
        }
    });
});

function scrollToOffer(){
    var offersSection = $('#content section.section article:eq(0)');
    $('html,body').animate({scrollTop: offersSection.offset().top},'slow');
    $('a.btn-top').fadeIn("slow");
}

function scrollToDisqus(){
    var disqusDivId = $('#disqus_thread');
    $('html,body').animate({scrollTop: disqusDivId.offset().top},'slow');
}

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
    if (LOCALE == 'be' || LOCALE == 'es' || LOCALE == 'br' || LOCALE == 'ch'|| LOCALE == 'sg'|| LOCALE == 'au') {
        var locale = LOCALE;
    } else if (LOCALE == '') {
        locale = 'nl';
    } else {
        locale = '';
    }
    if (locale != '') {
        try {
            __adroll.record_user({"adroll_segments": locale+"_clickout"});
        } catch(err){}
    }

    var codeType = getQueryStringParams("codetype");
    if (codeType == 'un') {
        $.ajax({
            url : HOST_PATH_LOCALE + "offer/offer-code",
            method : "post",
            data : {
                'id' : id
            },
            type : "post",
            success : function(data) {
                $('div#offerCodeDiv'+id+' .code-value').text(data);

                $.ajax({
                    url : HOST_PATH_LOCALE + "offer/offer-unique-code-update",
                    method : "post",
                    data : {
                        'id' : id
                    },
                    type : "post",
                    success : function(data) {
                        
                    }
                });
            }
        });
    }
    $('div#offerCodeDiv'+id).show();
    $('div#websiteOfferLink'+id).show();
    $('div#offerButton'+id).hide();
}

function printIt(urlToShow) {
    var flirNameWithExtension = urlToShow.split(".");
    var htmlTag = '';
    if (flirNameWithExtension[3] == 'pdf') {
        htmlTag = 'iframe';
    } else {
        htmlTag = 'img';
    }
    var windowUrl = 'about:blank';
    var uniqueName = new Date();
    var windowName = 'Print' + uniqueName.getTime();
    var printWindow = window.open(windowUrl, windowName);
    printWindow.document.write('<html>\n'); 
    printWindow.document.write('<head>\n');
    printWindow.document.write('<script>\n');
    printWindow.document.write('function winPrint()\n');
    printWindow.document.write('{\n');
    printWindow.document.write('window.focus();\n');  

    if (navigator.userAgent.toLowerCase().indexOf("chrome") > -1)
    {
        printWindow.document.write('printChrome();\n');
    }
    else
    {       
        printWindow.document.write('window.print();\n');
    }

    if(navigator.userAgent.toLowerCase().indexOf("firefox") > -1)
    {
        printWindow.document.write('window.close();\n');
    }
    else
    {
        printWindow.document.write('chkstate();\n');
    }   

    printWindow.document.write('}\n');
    printWindow.document.write('function chkstate()\n');
    printWindow.document.write('{\n');
    printWindow.document.write('if(document.readyState=="complete")');
    printWindow.document.write('{\n');
    printWindow.document.write('window.close();\n');
    printWindow.document.write('}\n');
    printWindow.document.write('else{\n');
    printWindow.document.write('setTimeout("chkstate();",3000);\n');
    printWindow.document.write('}\n');
    printWindow.document.write('}\n');
    printWindow.document.write('function printChrome()\n');
    printWindow.document.write('{\n');
    printWindow.document.write('if(document.readyState=="complete")');
    printWindow.document.write('{\n');
    printWindow.document.write('window.print();\n');
    printWindow.document.write('}\n');
    printWindow.document.write('else{\n');
    printWindow.document.write('setTimeout("printChrome();",3000);\n');
    printWindow.document.write('}\n');
    printWindow.document.write('}\n');
    printWindow.document.write('</scr');
    printWindow.document.write('ipt>');
    printWindow.document.write('</head>');
    printWindow.document.write('<body onload="winPrint()" >');
    printWindow.document.write('<'+htmlTag+' src="'+urlToShow+'" width = "100%" height = "100%"/>');
    printWindow.document.write('</body>'); 
    printWindow.document.write('</html>'); 
    printWindow.document.close();
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
    if(! ( /(Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini)/i.test(navigator.userAgent) )) {
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
    if(popupId != 'how_to_lightbox_pop_up') {
        fadeCustomPopup();
    }
    return false;
}

function fadeCustomPopup()
{
    $('#fade').css({
        'filter' : 'alpha(opacity=80)'
    }).fadeIn();
}

function customPopUpClose() {
    $('#fade , .popup_block, .popup_block_signup').fadeOut('9000', function() {
        $('#fade').remove(); 
    });
    return false;
}

