jQuery(document).ready(function() {
	initOpenPopup();
});

function initOpenPopup() {
	var timer = 3000;
	jQuery('.popup-box').each(function(){
		var holder = jQuery(this);
		var close = holder.find('.btn-close');
		setTimeout(function(){
			holder.removeClass('hide');
			holder.hide();
			holder.fadeIn(400);
		},timer);

		function closeHandler(e) {
			e.preventDefault();
			holder.fadeOut(400,function(){
				holder.addClass('hide');
			});
		}

		close.on('click',closeHandler)
	})
}