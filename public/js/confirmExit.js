(function ($) {
	$.fn.confirmExit = function (message) {
		var confirmExit = false;
		$('input, textarea, select', this).on('change keyup', function () {
		if (!confirmExit) {
			confirmExit = true;
			window.onbeforeunload = function (event) {
			var e = event || window.event;
			if (e) {
				e.returnValue = message;
			}
			return message;
		}
	}
	});
	this.submit(function () {
		window.onbeforeunload = null;
		confirmExit = false;
	});
	return this;
}
})(jQuery);