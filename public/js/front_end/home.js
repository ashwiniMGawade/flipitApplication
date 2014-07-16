$(document).ready(function(){

    $('.vouchers').hide();
    $('.vouchers:eq(0)').show();
    $('div.tab-content div:gt(0)').hide();
    removeActiveClass();
    $('#div_topOffers').addClass('active-li');
});
function showRelatedDiv(element){
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