$(document).ready(init);

function init()
{
	$('#floatDiv').stickyfloat( {duration: 0} );
}

function scrollByChapter(el){
	var anchorTag = $("h2#"+ $(el).attr('rel'));
    $('html,body').animate({scrollTop: anchorTag.offset().top},'slow');  
}