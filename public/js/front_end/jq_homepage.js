// page init
jQuery(function(){
	initCursorPointerClass();
});

function initCursorPointerClass(){
	jQuery('body').addClass('click');
	jQuery('.categories-block li, .vouchers li').each(function(){
		var li = jQuery(this);
		li.css({cursor: 'pointer'});
		li.on('click', function() {
			jQuery(this).find('a').trigger('click');
		});
		li.find('a').on('click', function(e) {
			e.stopPropagation();
			window.location.href = jQuery(this).attr('href');
		});
	});
}