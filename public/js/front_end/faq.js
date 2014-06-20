$(document).ready(function() {
	$('.box .heading').each(function() {
		var currentObject = $(this), 
		currentState = false, 
		answer = currentObject.next('div.slide').slideUp();
		currentObject.click(function() {				
			currentState = !currentState;
			answer.slideToggle(currentState);
			currentObject.toggleClass('active',currentState);
		});
	});
});