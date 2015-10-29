jQuery(document).ready(function() {
    if(document.cookie.indexOf("floatingCouponClosed=1") > 0) {
        return;
    }
    jQuery.ajax({
        url : HOST_PATH_LOCALE + "offer/floating-coupon",
        method : "post",
        type : "post",
        success : function(data) {
            jQuery('#floatingCouponContainer').html(data);
            initOpenPopup();
        }
    });
});

function setFloatingCouponCookie()
{
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

function sendGTMData(offerId)
{
    var gtmData = {
        'event' : 'floatingOfferImpression',
        'variant' : 'Code',
        'clickedElement' : 'Text Link',
        'impressionElement':'floatingOffer',
        'offerId' : offerId,
        'isExpired' : 'False',
        'isFloating' : 'True'
    };
    gtmDataBuilder(gtmData);
}

function initOpenPopup() {
	var timer = 2000;
	jQuery('#floatingCouponBox').each(function() {
		var holder = jQuery(this);
		var close = holder.find('.btn-close');
        var offerId = holder.attr('offerId');
		setTimeout(function() {
			holder.removeClass('hide');
			holder.hide();
			holder.fadeIn(400);
            sendGTMData(offerId);
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