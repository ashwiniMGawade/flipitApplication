$(document).ready(init);
function init() {
    $('#floatDiv').stickyfloat( {duration: 0} );
}

function scrollByChapter(element) {
    var anchorTag = $("h2#"+ $(element).attr('rel'));
    $('html,body').animate({scrollTop: anchorTag.offset().top},'slow');  
}