<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */

function optionsframework_option_name() {
    $optionsframework_settings = get_option('optionsframework');
    $optionsframework_settings['id'] = 'options_wpex_themes';
    update_option('optionsframework', $optionsframework_settings);
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'wpex'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {

	$options = array();
	
	//GENERAL	
	$options[] = array(
		'name' => __('General', 'wpex'),
		'type' => 'heading');
		
	$options['custom_logo'] = array(
		'name' => __('Custom Logo', 'wpex'),
		'desc' => __('Upload your custom logo.', 'wpex'),
		'std' => '',
		'id' => 'custom_logo',
		'type' => 'upload');
	
	$options['notification'] = array(
		'name' => __('Notification Bar', 'att'),
		'desc' => __('Enter your text for the notification bar.', 'att'),
		'std' => 'Thank you for purchasing this theme by <a href="http://themeforest.net/user/WPExplorer">WPExplorer &rarr;</a>',
		'id' => 'notification',
		'type' => 'textarea');
		
	$options['ajax_loading'] = array(
		'name' => __('AJAX Loading Instead of Pagination?', 'wpex'),
		'desc' => __('Check box to enable the load more ', 'wpex'),
		'id' => 'ajax_loading',
		'std' => '1',
		'type' => 'checkbox');
			
		
	//Slider
	$options[] = array(
				'name' => __('Slides', 'att'),
				'type' => 'heading');
			
		if ( class_exists( 'Symple_Slides_Post_Type' ) ) {
				
			$options['slides_slideshow'] = array(
				"name" => __('Toggle: Slideshow', 'att'),
				"desc" => __('Check this box to enable automatic slideshow for your slides.', 'att'),
				"id" => "slides_slideshow",
				"std" => "true",
				"type" => "select",
				"options" => array(
					'true' => 'true',
					'false' => 'false'
				) );
				
			$options['slides_randomize'] = array(
				"name" => __('Toggle: Randomize', 'att'),
				"desc" => __('Check this box to enable the randomize feature for your slides.', 'att'),
				"id" => "slides_randomize",
				"std" => "false",
				"type" => "select",
				"options" => array(
					'true' => 'true',
					'false' => 'false'
				) );
				
			$options['slides_animation'] = array(
				"name" => __('Animation', 'att'),
				"desc" => __('Select your animation of choice.', 'att'),
				"id" => "slides_animation",
				"std" => "slide",
				"type" => "select",
				"options" => array(
					'fade' => 'fade',
					'slide' => 'slide'
				) );
				
			$options['slides_direction'] = array(
				"name" => __('Direction', 'att'),
				"desc" => __('Select the direction for your slides. Slide animation only & if using the <strong>vertical direction</strong> all slides must have the same height.', 'att'),
				"id" => "slides_direction",
				"std" => "horizontal",
				"type" => "select",
				"options" => array(
					'horizontal' => 'horizontal',
					'vertical' => 'vertical'
				));
				
			$options['slideshow_speed'] = array(
				"name" => __('SlideShow Speed', 'att'),
				"desc" => __('Enter your preferred slideshow speed in milliseconds.', 'att'),
				"id" => "slideshow_speed",
				"std" => "7000",
				"type" => "text" );
				
			$options['animation_speed'] = array(
				"name" => __('Animation Speed', 'att'),
				"desc" => __('Enter your preferred animation speed in milliseconds.', 'att'),
				"id" => "animation_speed",
				"std" => "600",
				"type" => "text" );
		}
			
		$options['slides_alt'] = array(
				"name" => __('Slider Alternative', 'att'),
				"desc" => __('If you prefer to use another slider you can enter the <strong>shortcode</strong> here.', 'att'),
				"id" => "slides_alt",
				"std" => "",
				"type" => "textarea" );

	return $options;
}


/*
 * This is an example of how to add custom scripts to the options panel.
 * This example shows/hides an option when a checkbox is clicked.
 */

add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');

function optionsframework_custom_scripts() { ?>

<script type="text/javascript">
jQuery(document).ready(function($) {

	$('#example_showhidden').click(function() {
  		$('#section-example_text_hidden').fadeToggle(400);
	});

	if ($('#example_showhidden:checked').val() !== undefined) {
		$('#section-example_text_hidden').show();
	}

});
</script>

<?php } ?>