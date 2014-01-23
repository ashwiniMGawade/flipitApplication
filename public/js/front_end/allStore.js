$(document).ready(function(){
	$().UItoTop({ easingType: 'easeOutQuart' });
	
	//call a functionon change of the All store drop down
	//$('select#selectAllStore').change(redirectToStorePage);
	$('select#selectAllStore').val($('input#storeSelection').val());
	//call a functionon change of the All Category drop down
	//$('select#selectAllCategory').change(redirectToCategoryPage);
	$('select#selectAllCategory').val($('input#catSelection').val());
	$(window).scroll(setSearchDiv);
	//apply selected class on search panle like A-Z AND 0-9
	var ur = window.location.href;
	var cr = ur.split('#');
	if(cr[1]!=undefined) {
		scrollbyCategory(cr[1]);
		}
	
	
});

function setSearchDiv(){
	  var y = $(window).scrollTop();
	  if(parseInt(y) > 300)
		{
			$('div#fixed').addClass('fixedPosition');
			
		}else {
			$('div#fixed').removeClass('fixedPosition');
		}
}

// scroll function added by sunny patial...
var _flag = 1;
function scrollbyCategory(aid){
	var pexc = '';
	if($('div#fixed').hasClass('fixedPosition')){
		
			pexc = 55;
		} 
		else {
			pexc = 100;
		}
	$('div#fixed ul li a').removeClass('alphaSelection');
	$('div#fixed ul li a#'+aid).addClass('alphaSelection');
	
    var aTag = $("div[name='"+ aid +"']");
    if($("div#" + aid).length == 0) {
    	var aTag = $("div[name='showerror']");
    	$('div#showerror').html(__("Sorry guys, no record found!"));
    	$('html,body').animate({scrollTop: aTag.offset().top},'slow');
    	_flag  = 1;
    	}
    else{
    	$('div#showerror').html("");
    	
    	$('html,body').animate({scrollTop: aTag.offset().top-pexc},'slow');
    	_flag  = 2;
    	
    }
    
}

// end scroll function added by sunny patial....

