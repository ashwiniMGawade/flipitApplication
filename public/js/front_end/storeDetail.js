// refactored code 
function setHiddenFieldValue(){
    $('input#shopId').val($('input#currentShop').val());
};
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
// end refactored code

function checkFavorite(uId,id){
	alert(uId+id);
}

function storeAddToFeborite(uId,id) {
	if(uId==0) {
			addStoreToFavorite();
	}else {
	 		$.ajax({
			url:HOST_PATH_LOCALE+"store/addshopinfevorite/shopid/"+ id  + "/uId/"  + uId+'/rnd/'+Math.random()*99999,
			method:"post",
			dataType:"json",
			type:"POST",
			success:function(json){
				console.log(json);
				if(parseInt(json.flag)==0){
					
					$("div.fav-heart-icon").addClass('new-fav-red')
					 						.removeClass('new-fav-black new-fav-blue new-fav-main')
					 						.find('span.fav-text-change').html( json.shop + ' ' + __('is toegevoegd aan favorieten'));
					
					flashMessage( json.shop + " "+ __('is toegevoegd aan uw favorieten'));
						
				}else {
						  
					$("div.fav-heart-icon").addClass('new-fav-main')
												.removeClass('new-fav-black new-fav-blue new-fav-red')
												.find('span.fav-text-change').html( json.shop+ ' ' + __('toevoegen aan favorieten'));
					 
					flashMessage( json.shop + " " + __('is verwijderd uit uw favorieten'));
						
				}
				
				$(".fav-btn span").toggleClass("grey-heart red-heart");
			},
		    
		});	
	}
}

function flashMessage(msg)
{
	$("div#messageDiv").fadeIn()
					   .find('strong')
					   .html(msg);
}

$(document).ready(function(){

	$("div.fav-heart-icon a").mouseover(function(){
							  $(this).parent().addClass('new-fav-black');
						 }).mouseout(function(){
							  $(this).parent().removeClass('new-fav-black');
						 }).click(function(){
							 $(this).parent().addClass('new-fav-blue')
							 				 .removeClass('new-fav-black new-fav-red new-fav-main');
						 });
	$('div.tearmAnCondition').slideToggle();
	$('div.tearmAnCondition').hide();
	$('div#readMore').hide();
	$('div.close_area').slideToggle();
	$('div.noteExpander').expander({
	    slicePoint:       600,  // default is 100
	    expandPrefix:     '...', // default is '... '
	    expandText:       __('Read More'), // default is 'read more'
	    userCollapseText: __('Less More')  // default is 'read less'
	});
	$('div.noteExpander1').expander({
	    slicePoint:       150,  // default is 100
	    expandPrefix:     '...', // default is '... '
	    expandText:       __('Read More'), // default is 'read more'
	    userCollapseText: __('Less More')  // default is 'read less'
	});
});
function showTermAndCondition(id)
{
	$('div#close_area'+ id).show();
	$('div#termAndCondition'+id).slideToggle();
}
function showReadMore(id)
{
	$('div#close_area'+ id).show();
	$('div#teamandCondition'+id).slideToggle();
	
}
function hideReadMoreAndTeamCondition(id)
{
	$('div#close_area'+ id).hide();
	$('div#teamandCondition'+id).slideToggle();;
	
}
$(function () {
	$('#popup-wrapper').modalPopLite({ openButton: '#clicker', closeButton: '#close-btn' });
});

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
