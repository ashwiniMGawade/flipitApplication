$(document).ready(function() {
	$("img.lazy").show().lazyload();
    $('.switcher li:eq(0) a').addClass('active');
    $('.switcher li a').click(addActiveClass);
});

function addActiveClass() {
    $('.switcher li a').removeClass('active');
    $(this).addClass('active');
}
