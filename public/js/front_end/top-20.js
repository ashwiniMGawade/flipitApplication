// refactored code 

function showTermAndConditions(id)
{
	$('div#termAndConditions'+id).slideToggle();
	$('a#termAndConditionLink'+id).toggleClass('uparrow'); 
}

function showCodeInformation(id)
{
	$('div#offerCodeDiv'+id).show();
	$('div#websiteOfferLink'+id).show();
	$('div#offerButton'+id).hide();
	
}
// end refactored code

function doVote(uId,offer , vote)
{
	
	if(uId==0) {
		addStoreToFavorite();
			
	}else {
		
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


jQuery(document).ready(function() {


 
	

});