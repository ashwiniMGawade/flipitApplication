jQuery(document).ready(function() {
	var floatingCoupon = initOpenPopup();
});

function setFloatingCouponCookie() {
    var now = new Date();
    var expire = new Date();
    expire.setFullYear(now.getFullYear());
    expire.setMonth(now.getMonth());
    expire.setDate(now.getDate()+1);
    expire.setHours(0);
    expire.setMinutes(0);
    expire.setSeconds(0);
    document.cookie = "floatingCouponClosed=1; expires="+expire.toString();
    return true;
}

function initOpenPopup() {
	var timer = 3000;
	jQuery('#floatingCouponBox').each(function() {
		var holder = jQuery(this);
		var close = holder.find('.btn-close');
		setTimeout(function() {
			holder.removeClass('hide');
			holder.hide();
			holder.fadeIn(400);
		},timer);

		function closeHandler(e) {
			e.preventDefault();
			holder.fadeOut(400,function() {
				holder.addClass('hide');
			});
            setFloatingCouponCookie();
		}
		close.on('click', closeHandler);
	})
}