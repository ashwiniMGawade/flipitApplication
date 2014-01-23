jQuery(function($){
	$(window).load(function() {
		
		/* ajax scroll */
		var ajaxurl = wpexvars.ajaxurl;
		
		$('div#load-more').click(function() {
			
			$(this).children('a').addClass('loading').html('');
			
			var $this = $(this),
				anchor = $this.children('a'),
				nonce = anchor.val(),
				pagenum = anchor.data('pagenum'),
				maxpage = anchor.data('maxpage'),
				data = {
					action: 'aq_ajax_scroll',
					pagenum: pagenum,
					archive_type: anchor.data('archive_type'),
					archive_id: anchor.data('archive_id'),
					post_format: anchor.data('post_format'),
					author: anchor.data('author'),
					s: anchor.data('s'),
					security: nonce
				};
				
			$.post(ajaxurl, data, function(response) {
				content = $(response);
					
					$('div#load-more a').removeClass('loading');
					
					$('#post-entries').append(content);
					
					$('.post-entry-thumbnail a').hover(function(){
						$(this).children('.overlay').fadeIn('fast');
					}, function(){
						$(this).children('.overlay').fadeOut('fast');
					});

				
				anchor.data('pagenum', pagenum + 1);
				
				if(pagenum >= maxpage) {
					$this.fadeOut();
				}
				
			});
			
			return false;
			
		});
		
		/*hover text*/
		if ($(window).width() > 767) {
			$('#load-more a').hover(function(){
					$('#load-more-txt').stop(true,true).fadeIn();
				}, function(){
					$('#load-more-txt').stop(true,true).fadeOut();
			});
		}
		
	}); /* END window ready */
}); /* END function */