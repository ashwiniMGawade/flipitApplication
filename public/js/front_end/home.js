$(document).ready(function(){
    $('.vouchers').hide();
    $('.vouchers:eq(0)').show();
    $('div.tab-content div:gt(0)').hide();
});
function showRelatedDiv(element){
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
