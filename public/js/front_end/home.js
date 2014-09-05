var urlsRequested = [];
$(document).ready(function(){
    $('.vouchers').hide();
    $('.vouchers:eq(0)').show();
    $('div.tab-content div:gt(0)').hide();
    removeActiveClass();
    $('#div_topOffers').addClass('active-li');
});
function showRelatedDiv(element){
    var permalink = $(element).attr('id').split('_');
    var elementDataAttribute = $(element).attr('data');
    if ($.inArray(elementDataAttribute, urlsRequested) < 0) {
        urlsRequested.push(elementDataAttribute);
        getDetails(elementDataAttribute, permalink[1]);
    }
    removeActiveClass();
    $('.vouchers').hide();
    $('div#'+$(element).attr('id')).show();
    $('a#'+$(element).attr('id')).addClass('active-li');
}
function showTab(element){
    $('#services_'+element)
    .css({"display":"block","visibility":"visible"}).siblings()
    .css({"display":"none","visibility":"hidden"});
    $('div.container ul li').removeClass('active');
    $('a#tab_'+element).parents('li').addClass('active');
}

function removeActiveClass(){
    $('.best-items .categories-block ul li a').removeClass('active-li');
}

function getDetails(divId, permalink)
{
    if(divId!=undefined && divId!='') {
        var ajaxRequestUrl = '';
        switch(divId){
            case 'newOffer':
                ajaxRequestUrl = HOST_PATH_LOCALE + "homeajax/getnewestoffers";
                getRightDiv(ajaxRequestUrl, permalink);
                break;
            case 'moneysaving':
                ajaxRequestUrl = HOST_PATH_LOCALE + "homeajax/getmoneysavingguides";
                getRightDiv(ajaxRequestUrl, permalink);
                break;
            default:
                ajaxRequestUrl = 
                    HOST_PATH_LOCALE + "homeajax/getcategoryoffers/categoryid/" + divId + "/permalink/" + permalink;
                getRightDiv(ajaxRequestUrl, permalink);
                break;
        }
    }
}

function getRightDiv(ajaxRequestUrl, divId) {
    var divHeight = $('.categories-block').height();
    $('div#ajaxContent').append("<div id='overlay' style='height:"+divHeight+"px;'><img id='img-load' src='" +  HOST_PATH  + "/public/images/back_end/ajax-loader-tran.gif'/></div>");
    $.ajax({
        type : "POST",
        url : ajaxRequestUrl,
        method : "get",
        dataType : 'json',
        success : function(rightDivWithContent) { 
            $('div#ajaxContent').append(rightDivWithContent).show();
           ___removeOverLay(divId);
        }
    });
}