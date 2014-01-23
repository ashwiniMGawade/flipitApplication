function showTeamAndCondition(id)
{
	$('div#close_area'+ id).show();
	$('div#teamandCondition'+id).slideToggle();
}
function hideReadMoreAndTeamCondition(id)
{
	$('div#close_area'+ id).hide();
	$('div#teamandCondition'+id).slideToggle();;
	
}
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