// jDiv - a jQuery plugin IAS
// (c) Skyrocket Labs
// http://www.skyrocketlabs.com
// fred@skyrocketlabs.com
// Created: 10.24.2009
// Last updated: 04.09.2012

// DISPLAYS HIDDEN DIVS AS SUBMENUS ON HOVER

$(document).ready(function() {
        var hide = false;
        // Shows the DIV on hover with a fade in
		$("#menu1").hover(function(){          
		   if (hide) clearTimeout(hide);
            $("#hidden-div").fadeIn();
            // The main nav menu item is assigned the 'active' CSS class
			$(this).addClass("active");
        }, function() {
            // Fades out the DIV and removes the 'active' class from the main nav menu item
			hide = setTimeout(function() {$("#hidden-div").fadeOut("fast");});
			$("#menu1").removeClass("active");
        });
		// Ensures the DIV displays when your mouse moves away from the main nav menu item
        $("#hidden-div").hover(function(){
            if (hide) clearTimeout(hide);
            $("#menu1").addClass("active");
        }, function() {
            // If your mouse moves out of the displayed hidden DIV, the DIv fades out and removes the 'active' class
			hide = setTimeout(function() {$("#hidden-div").fadeOut("fast");});
			$("#hidden-div").stop().fadeIn();
			$("#menu1").removeClass("active");
        });
	});
	
	

$(document).ready(function() {
        var hide = false;
        // Shows the DIV on hover with a fade in
		$(".menu2").hover(function(){          
		   if (hide) clearTimeout(hide);
            $(".hidden-div2").fadeIn();
            // The main nav menu item is assigned the 'active' CSS class
			$(this).addClass("active");
        }, function() {
            // Fades out the DIV and removes the 'active' class from the main nav menu item
			hide = setTimeout(function() {$(".hidden-div2").fadeOut("fast");});
			$(".menu2").removeClass("active");
        });
		// Ensures the DIV displays when your mouse moves away from the main nav menu item
        $(".hidden-div2").hover(function(){
            if (hide) clearTimeout(hide);
            $(".menu2").addClass("active");
        }, function() {
            // If your mouse moves out of the displayed hidden DIV, the DIv fades out and removes the 'active' class
			hide = setTimeout(function() {$(".hidden-div2").fadeOut("fast");});
			$(".hidden-div2").stop().fadeIn();
			$(".menu2").removeClass("active");
        });
	});
	
	
	
$(document).ready(function() {
        var hide = false;
        // Shows the DIV on hover with a fade in
		$(".menu3").hover(function(){          
		   if (hide) clearTimeout(hide);
            $(".hidden-div3").fadeIn();
            // The main nav menu item is assigned the 'active' CSS class
			$(this).addClass("active");
        }, function() {
            // Fades out the DIV and removes the 'active' class from the main nav menu item
			hide = setTimeout(function() {$(".hidden-div3").fadeOut("fast");});
			$(".menu3").removeClass("active");
        });
		// Ensures the DIV displays when your mouse moves away from the main nav menu item
        $(".hidden-div3").hover(function(){
            if (hide) clearTimeout(hide);
            $(".menu3").addClass("active");
        }, function() {
            // If your mouse moves out of the displayed hidden DIV, the DIv fades out and removes the 'active' class
			hide = setTimeout(function() {$(".hidden-div3").fadeOut("fast");});
			$(".hidden-div3").stop().fadeIn();
			$(".menu3").removeClass("active");
        });
	});