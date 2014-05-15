$(document).ready(function(){
    $('.vouchers').hide();
    $('.vouchers:eq(0)').show();
    $('div.tab-content div:gt(0)').hide();
});
function showRelatedDiv(obj){
    $('.vouchers').hide();
    $('div#'+$(obj).attr('id')).show();
    $('a#'+$(obj).attr('id')).addClass('active-li');

}
function nexttab(obj){
    $('#services_'+obj)
    .css({"display":"block","visibility":"visible"}).siblings()
    .css({"display":"none","visibility":"hidden"});
    $('div.container ul li').removeClass('active');
    $('a#tab_'+obj).parents('li').addClass('active');
}
