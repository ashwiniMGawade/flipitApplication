jQuery(function($){
	$(document).ready(function(){
		$("#callout-exit").click(function() {
			$("#callout-open").show();
			$("#callout").slideUp(200, 'swing');
			$.cookie('notificationCookie', 'hidden', {
					expires: 1,
					path: '/'
				});
			return false;
		});
		$("#callout-open").click(function() {
			$("#callout-open").hide();
			$("#callout").slideDown(200, 'swing');
			$.cookie('notificationCookie', 'visible', {
					expires: 1,
					path: '/'
				});
			return false;
		});
		var notificationVisibility = $.cookie('notificationCookie');
		if(notificationVisibility == 'hidden') {
			$("#callout").hide();
			$("#callout-open").show();
		};
		if(notificationVisibility == 'visible' || notificationVisibility == null) {
			$("#callout-open").hide();
			$("#callout").slideDown(200, 'swing');
		};
	});
});