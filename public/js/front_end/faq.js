$(document).ready(function() {
	$('.box .heading').each(function() {
		var tis = $(this), 
		state = false, 
		answer = tis.next('div.slide').slideUp();
		tis.click(function() {				
			state = !state;
			answer.slideToggle(state);
			tis.toggleClass('active',state);
		});
	});
});