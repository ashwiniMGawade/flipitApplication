function showTermAndConditions(id)
{
    $('div#termAndConditions'+id).slideToggle();
    $('a#termAndConditionLink'+id).toggleClass('uparrow'); 
}
function showPopupTermAndConditions(id)
{
    $('div#termAndConditionsPopup'+id).slideToggle();
    $('a#termAndConditionLinkPopup'+id).toggleClass('uparrow'); 
}
function showCodeInformation(id)
{
    $('div#offerCodeDiv'+id).show();
    $('div#websiteOfferLink'+id).show();
    $('div#offerButton'+id).hide();
}
function printIt(urlToShow) 
{
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
$(function () {
	$('#popup-wrapper').modalPopLite({ openButton: '#clicker', closeButton: '#close-btn' });
});
function doVote(uId,offer , vote) {
	if(uId==0) {
		addStoreToFavorite();
	} else {
		$.ajax({
			url:HOST_PATH_LOCALE + "store/add-vote/offer/"+ offer  + "/vote/"  + vote,
			method:"post",
			dataType:"json",
			type:"POST",
			success:function(json){
				if(json.flag){
					flashMessage(__('Your vote is succesfully registered'));
				}else {
					flashMessage(  __('You have already voted it'));
				}
				$("span.rate" , ".success-rate").html(json.succes);
			}
		});
	
	}
}
