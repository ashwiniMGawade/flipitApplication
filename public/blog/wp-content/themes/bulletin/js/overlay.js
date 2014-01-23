jQuery(function($){
	$(window).load(function() {
		$('.post-entry-thumbnail a').hover(function(e) {
			$(this).children('.overlay').fadeIn('fast');
		}, function(){
			$(this).children('.overlay').fadeOut('fast');
		});
	});
});