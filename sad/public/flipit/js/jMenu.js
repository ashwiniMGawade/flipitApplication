$(function() {
	// Hide all the content except the first
	$('.accordian li:odd').hide();
	
	// Add a padding to the first link
	$('.accordian li:first').animate( {
		paddingLeft:"0px"
	} );
	
	// Add the dimension class to all the content
	$('.accordian li:odd').addClass('dimension');
	
	// Set the even links with an 'even' class
	$('.accordian li:even:even').addClass('even');
	
	// Set the odd links with a 'odd' class
	$('.accordian li:even:odd').addClass('odd');
	
	// Show the correct cursor for the links
	$('.accordian li:even').css('cursor', 'pointer');
	
	// Handle the click event
	$('.accordian li:even').click( function() {
		// Get the content that needs to be shown
		var cur = $(this).next();
		
		// Get the content that needs to be hidden
		var old = $('.accordian li:odd:visible');
		
		// Make sure the content that needs to be shown 
		// isn't already visible
		if ( cur.is(':visible') ){ cur.slideToggle(); $('.accordian > ul > li.dropdown > div.arrow-down').removeClass();
			$('.accordian > ul > li.dropdown > div').addClass('arrow-right');
			$('.accordian > ul > li.dropdown > h2.blue').removeClass();return false;}

			$('.accordian > ul > li.dropdown > div.arrow-down').removeClass();
			$('.accordian > ul > li.dropdown > div').addClass('arrow-right');
			$('.accordian > ul > li.dropdown > h2.blue').removeClass();
								
			var html = $(this).html();
			html = html.replace('h2','h2 class="blue"');
			html = html.replace('H2','H2 class="blue"');
			html = html.replace('arrow-right','arrow-down');
			$(this).html(html);

		// Hide the old content
		old.slideToggle(500);
		
		// Show the new content
		cur.stop().slideToggle(500);
		
		// Animate (add) the padding in the new link
		$(this).stop().animate( {
			paddingLeft:"0px"
		} );
		
		// Animate (remove) the padding in the old link
		old.prev().stop().animate( {
			paddingLeft:"0px"
		} );
	} );
});