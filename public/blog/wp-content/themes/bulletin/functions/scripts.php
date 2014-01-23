<?php
/**
 * This file loads the CSS and Javascript used for the theme.
 * @package Bulletin WordPress Theme
 * @since 1.0
 * @author WPExplorer : http://www.wpexplorer.com
 * @copyright Copyright (c) 2012, WPExplorer
 * @link http://www.wpexplorer.com
 */
 
 
add_action('wp_enqueue_scripts','wpex_load_scripts');
function wpex_load_scripts() {

	/**
	* CSS
	* @since 1.0
	*/
	
		// Main CSS
		wp_enqueue_style('style', get_stylesheet_uri() );
		
		// Shortcodes
		wp_dequeue_style('symple_shortcode_styles');
		
		// Fancybox
		wp_enqueue_style('fancybox', WPEX_CSS_DIR . '/fancybox.css');
		
		// Source Sans Pro - Google Font
		wp_enqueue_style('source-sans-pro', 'http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900,200italic,300italic,400italic,600italic,700italic,900italic&subset=latin,latin-ext');
		
		// Remove default contact 7 styling
		if( function_exists('wpcf7_enqueue_styles') ) {
			wp_dequeue_style('contact-form-7');
		}


	/**
	* jQuery
	* @since 1.0
	*/
	
		// Main Scripts
		wp_enqueue_script('easing', WPEX_JS_DIR .'/easing.js', array('jquery'), '1.3', true);
		wp_enqueue_script('flexslider', WPEX_JS_DIR .'/flexslider.js', array('jquery'), '2', true);
		wp_enqueue_script('fancybox', WPEX_JS_DIR .'/fancybox.js', array('jquery'), '2.1.3', true);
		wp_enqueue_script('cookie', WPEX_JS_DIR .'/cookie.js', array('jquery'), '1.0', true);
		wp_enqueue_script('wpex-callout', WPEX_JS_DIR .'/callout.js', array('jquery'), '1.0', true);
			
		// Homepage script
		if( is_front_page() ) {
				
			wp_enqueue_script('wpex-slider-home', WPEX_JS_DIR .'/slider-home.js', false, '1.0', true);
			
			
			//localize homepage slider
			$flex_params = array(
				'slideshow' => of_get_option('slides_slideshow', '0'),
				'randomize' => of_get_option('slides_randomize', '0'),			
				'animation' => of_get_option('slides_animation', 'slide'),
				'direction' => of_get_option('slides_direction', 'horizontal'),
				'slideshowSpeed' => of_get_option('slideshow_speed', '7000'),
				'animationSpeed' => of_get_option('animation_speed', '600')
			);
			wp_localize_script( 'wpex-slider-home', 'flexLocalize', $flex_params );
				
		}
		
		// Responsive
		wp_enqueue_script('fitvids', WPEX_JS_DIR .'/fitvids.js', array('jquery'), 1.0, true);
		wp_enqueue_script('uniform', WPEX_JS_DIR .'/uniform.js', array('jquery'), '1.7.5', true);
		wp_enqueue_script('wpex-responsive', WPEX_JS_DIR .'/responsive.js', array('jquery'), '', true);
		//wp_enqueue_script('foresight', WPEX_JS_DIR .'/foresight.js', array('jquery'), 1.0, true)
		
		// IE only
		global $is_IE;
		if ( $is_IE ) wp_enqueue_script('bulletin-ie', WPEX_JS_DIR .'/ie.js', array('jquery'), 1.0, true);
	
		// Comment replies
		if(is_single() || is_page()) {
			wp_enqueue_script('comment-reply');
		}
		
		// Register
		wp_register_script('wpex-overlay', WPEX_JS_DIR .'/overlay.js', array('jquery'), 1.0, true);
		wp_register_script('wpex-ajax-load', WPEX_JS_DIR .'/ajax-load.js', array('jquery'), 1.0, true);
		
		//localize ajax
		$ajax_params = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( 'wpex-ajax-load', 'wpexvars', $ajax_params );
	
} //end wpex_load_scripts()