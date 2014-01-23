<?php
/**
 * This file has all the main shortcode functions
 * @package Symple Shortcodes Plugin
 * @since 1.0
 * @author AJ Clarke : http://sympleplorer.com
 * @copyright Copyright (c) 2012, AJ Clarke
 * @link http://sympleplorer.com
 * @License: GNU General Public License version 3.0
 * @License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


/*
 * Clear Floats
 * @since v1.0
 */
if( !function_exists('symple_clear_floats_shortcode') ) {
	function symple_clear_floats_shortcode() {
	   return '<div class="symple-clear-floats"></div>';
	}
	add_shortcode( 'symple_clear_floats', 'symple_clear_floats_shortcode' );
}



/*
 * Spacing
 * @since v1.0
 */
if( !function_exists('symple_spacing_shortcode') ) {
	function symple_spacing_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'size' => '20px',
		  ),
		  $atts ) );
	 return '<hr class="symple-spacing" style="height: '. $size .'"></hr>';
	}
	add_shortcode( 'symple_spacing', 'symple_spacing_shortcode' );
}




/*
 * Fix Shortcodes
 * @since v1.0
 */
if( !function_exists('symple_fix_shortcodes') ) {
	function symple_fix_shortcodes($content){   
		$array = array (
			'<p>[' => '[', 
			']</p>' => ']', 
			']<br />' => ']'
		);
		$content = strtr($content, $array);
		return $content;
	}
	add_filter('the_content', 'symple_fix_shortcodes');
}


/**
* Social Icons
* @since 1.0
*/
if( !function_exists('symple_social_shortcode') ) {
	function symple_social_shortcode( $atts ){   
		extract( shortcode_atts( array(
			'icon' => 'twitter',
			'url' => 'http://www.twitter.com/sympleplorer',
			'title' => 'Follow Us',
			'target' => 'self',
			'rel' => '',
			'border_radius' => ''
		), $atts ) );
		
		return '<a href="' . $url . '" class="symple-social-icon" target="_'.$target.'" title="'. $title .'" rel="'. $rel .'"
><img src="'. plugin_dir_url( __FILE__ ) .'/images/social/'. $icon .'.png" alt="'. $icon .'" /></a>';
	}
	add_shortcode('symple_social', 'symple_social_shortcode');
}

/**
* Highlights
* @since 1.0
*/
if ( !function_exists( 'symple_highlight_shortcode' ) ) {
	function symple_highlight_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'color' => 'yellow',
		  ),
		  $atts ) );
		  return '<span class="symple-highlight symple-highlight-'. $color .'">' . do_shortcode( $content ) . '</span>';
	
	}
	add_shortcode('symple_highlight', 'symple_highlight_shortcode');
}


/*
 * Buttons
 * @since v1.0
 */
if( !function_exists('symple_button_shortcode') ) {
	function symple_button_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'color' => 'blue',
			'url' => 'http://www.sympleplorer.com',
			'title' => 'Visit Site',
			'target' => 'self',
			'rel' => '',
			'border_radius' => ''
		), $atts ) );
		
		$border_radius_style = ( $border_radius ) ? 'style="border-radius:'. $border_radius .'"' : NULL;
		
		return '<a href="' . $url . '" class="symple-button ' . $color . '" target="_'.$target.'" title="'. $title .'" '. $border_radius_style .' rel="'.$rel.'"><span class="symple-button-inner" '.$border_radius_style.'>' . $content . '</span></a>';
	}
	add_shortcode('symple_button', 'symple_button_shortcode');
}



/*
 * Boxes
 * @since v1.0
 *
 */
if( !function_exists('symple_box_shortcode') ) { 
	function symple_box_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'color' => 'gray',
			'float' => 'center',
			'text_align' => 'left',
			'width' => '100%'
		  ), $atts ) );
		  $alert_content = '';
		  $alert_content .= '<div class="symple-box ' . $color . ' '.$float.'" style="text-align:'. $text_align .'; width:'. $width .';">';
		  $alert_content .= ' '. do_shortcode($content) .'</div>';
		  return $alert_content;
	}
	add_shortcode('symple_box', 'symple_box_shortcode');
}



/*
 * Testimonial
 * @since v1.0
 *
 */
if( !function_exists('symple_testimonial_shortcode') ) { 
	function symple_testimonial_shortcode( $atts, $content = null  ) {
		extract( shortcode_atts( array(
			'by' => ''
		  ), $atts ) );
		$testimonial_content = '';
		$testimonial_content .= '<div class="symple-testimonial"><div class="symple-testimonial-content">';
		$testimonial_content .= $content;
		$testimonial_content .= '</div><div class="symple-testimonial-author">';
		$testimonial_content .= $by .'</div></div>';	
		return $testimonial_content;
	}
	add_shortcode( 'symple_testimonial', 'symple_testimonial_shortcode' );
}



/*
 * Columns
 * @since v1.0
 *
 */
if( !function_exists('symple_column_shortcode') ) {
	function symple_column_shortcode( $atts, $content = null ){
		extract( shortcode_atts( array(
			'size' => 'one-third',
			'position' =>'first'
		  ), $atts ) );
		  return '<div class="symple-' . $size . ' symple-column-'.$position.'">' . do_shortcode($content) . '</div>';
	}
	add_shortcode('symple_column', 'symple_column_shortcode');
}



/*
 * Toggle
 * @since v1.0
 */
if( !function_exists('symple_toggle_shortcode') ) {
	function symple_toggle_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array( 'title' => 'Toggle Title' ), $atts ) );
		 
		// Enque scripts
		wp_enqueue_script('symple_toggle');
		
		// Display the Toggle
		return '<div class="symple-toggle"><h3 class="symple-toggle-trigger">'. $title .'</h3><div class="symple-toggle-container">' . do_shortcode($content) . '</div></div>';
	}
	add_shortcode('symple_toggle', 'symple_toggle_shortcode');
}


/*
 * Accordion
 * @since v1.0
 *
 */

// Main
if( !function_exists('symple_accordion_main_shortcode') ) {
	function symple_accordion_main_shortcode( $atts, $content = null  ) {
		
		// Enque scripts
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('symple_accordion');
		
		// Display the accordion	
		return '<div class="symple-accordion">' . do_shortcode($content) . '</div>';
	}
	add_shortcode( 'symple_accordion', 'symple_accordion_main_shortcode' );
}


// Section
if( !function_exists('symple_accordion_section_shortcode') ) {
	function symple_accordion_section_shortcode( $atts, $content = null  ) {
		extract( shortcode_atts( array(
		  'title' => 'Title',
		), $atts ) );
		  
	   return '<h3 class="symple-accordion-trigger"><a href="#">'. $title .'</a></h3><div>' . do_shortcode($content) . '</div>';
	}
	
	add_shortcode( 'symple_accordion_section', 'symple_accordion_section_shortcode' );
}


/*
 * Tabs
 * @since v1.0
 *
 */
if (!function_exists('symple_tabgroup_shortcode')) {
	function symple_tabgroup_shortcode( $atts, $content = null ) {
		
		//Enque scripts
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('symple_tabs');
		
		// Display Tabs
		$defaults = array();
		extract( shortcode_atts( $defaults, $atts ) );
		preg_match_all( '/tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );
		$tab_titles = array();
		if( isset($matches[1]) ){ $tab_titles = $matches[1]; }
		$output = '';
		if( count($tab_titles) ){
		    $output .= '<div id="symple-tab-'. rand(1, 100) .'" class="symple-tabs">';
			$output .= '<ul class="ui-tabs-nav symple-clearfix">';
			foreach( $tab_titles as $tab ){
				$output .= '<li><a href="#symple-tab-'. sanitize_title( $tab[0] ) .'">' . $tab[0] . '</a></li>';
			}
		    $output .= '</ul>';
		    $output .= do_shortcode( $content );
		    $output .= '</div>';
		} else {
			$output .= do_shortcode( $content );
		}
		return $output;
	}
	add_shortcode( 'symple_tabgroup', 'symple_tabgroup_shortcode' );
}
if (!function_exists('symple_tab_shortcode')) {
	function symple_tab_shortcode( $atts, $content = null ) {
		$defaults = array( 'title' => 'Tab' );
		extract( shortcode_atts( $defaults, $atts ) );
		return '<div id="symple-tab-'. sanitize_title( $title ) .'" class="tab-content">'. do_shortcode( $content ) .'</div>';
	}
	add_shortcode( 'symple_tab', 'symple_tab_shortcode' );
}




/*
 * Pricing Table
 * @since v1.0
 *
 */
 
/*main*/
if( !function_exists('symple_pricing_table_shortcode') ) {
	function symple_pricing_table_shortcode( $atts, $content = null  ) {
	   return '<div class="symple-pricing-table">' . do_shortcode($content) . '</div><div class="symple-clear-floats"></div>';
	}
	add_shortcode( 'symple_pricing_table', 'symple_pricing_table_shortcode' );
}

/*section*/
if( !function_exists('symple_pricing_shortcode') ) {
	function symple_pricing_shortcode( $atts, $content = null  ) {
		
		extract( shortcode_atts( array(
			'size' => 'one-half',
			'position' => '',
			'featured' => 'no',
			'plan' => 'Basic',
			'cost' => '$20',
			'per' => 'month',
			'button_url' => 'http://www.sympleplorer.com',
			'button_text' => 'Purchase',
			'button_color' => 'blue',
			'button_target' => 'self',
			'button_rel' => 'nofollow',
			'button_border_radius' => ''
		), $atts ) );
		
		//set variables
		$featured_pricing = ( $featured == 'yes' ) ? 'featured' : NULL;
		$border_radius_style = ( $button_border_radius ) ? 'style="border-radius:'. $button_border_radius .'"' : NULL;
		
		//start content  
		$pricing_content ='';
		$pricing_content .= '<div class="symple-pricing symple-'. $size .' '. $featured_pricing .' symple-column-'. $position. '">';
			$pricing_content .= '<div class="symple-pricing-header">';
				$pricing_content .= '<h5>'. $plan. '</h5>';
				$pricing_content .= '<div class="symple-pricing-cost">'. $cost .'</div><div class="symple-pricing-per">'. $per .'</div>';
			$pricing_content .= '</div>';
			$pricing_content .= '<div class="symple-pricing-content">';
				$pricing_content .= ''. $content. '';
			$pricing_content .= '</div>';
			if( $button_text ) {
				$pricing_content .= '<div class="symple-pricing-button"><a href="'. $button_url .'" class="symple-button '. $button_color .'" target="_'. $button_target .'" rel="'. $button_rel .'" '. $border_radius_style .'><span class="symple-button-inner" '. $border_radius_style .'>'. $button_text .'</span></a></div>';
			}
		$pricing_content .= '</div>';  
		return $pricing_content;
	}
	
	add_shortcode( 'symple_pricing', 'symple_pricing_shortcode' );
}




/************************
 *
 * Version 1.1 Additions
 *
*************************/



/*
 * Heading
 * @since v1.1
 */
if( !function_exists('symple_heading_shortcode') ) {
	function symple_heading_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'title' => 'Sample Heading',
			'type' => 'h2',
			'margin_top' => '',
			'margin_bottom' => '',
			'text_align' => 'center'
		  ),
		  $atts ) );
		  
		$style_attr = '';
		if ( $margin_top && $margin_bottom ) {  
			$style_attr = 'style="margin-top: '. $margin_top .';margin-bottom: '. $margin_bottom .';"';
		} elseif( $margin_bottom ) {
			$style_attr = 'style="margin-bottom: '. $margin_bottom .';"';
		} elseif ( $margin_top ) {
			$style_attr = 'style="margin-top: '. $margin_top .';"';
		} else {
			$style_attr = NULL;
		}
	 	return '<'.$type.' class="symple-heading text-align-'. $text_align .'" '.$style_attr.'><span>'. $title .'</span></'.$type.'>';
	}
	add_shortcode( 'symple_heading', 'symple_heading_shortcode' );
}


/*
 * Google Maps
 * @since v1.1
 */
if (! function_exists( 'symple_shortcode_googlemaps' ) ) :
	function symple_shortcode_googlemaps($atts, $content = null) {
		
		extract(shortcode_atts(array(
				"title" => '',
				"location" => '',
				"width" => '', //leave width blank for responsive designs
				"height" => '300',
				"zoom" => 8,
				"align" => '',
		), $atts));
		
		// load scripts
		wp_enqueue_script('symple_googlemap');
		wp_enqueue_script('symple_googlemap_api');
		
		
		$output = '<div id="map_canvas_'.rand(1, 100).'" class="googlemap" style="height:'.$height.'px;width:100%">';
			$output .= (!empty($title)) ? '<input class="title" type="hidden" value="'.$title.'" />' : '';
			$output .= '<input class="location" type="hidden" value="'.$location.'" />';
			$output .= '<input class="zoom" type="hidden" value="'.$zoom.'" />';
			$output .= '<div class="map_canvas"></div>';
		$output .= '</div>';
		
		return $output;
	   
	}
	add_shortcode("symple_googlemap", "symple_shortcode_googlemaps");
endif;


/*
 * Divider
 * @since v1.1
 */
if( !function_exists('symple_divider_shortcode') ) {
	function symple_divider_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'style' => 'solid',
			'margin_top' => '20px',
			'margin_bottom' => '20px',
		  ),
		  $atts ) );
		$style_attr = '';
		if ( $margin_top && $margin_bottom ) {  
			$style_attr = 'style="margin-top: '. $margin_top .';margin-bottom: '. $margin_bottom .';"';
		} elseif( $margin_bottom ) {
			$style_attr = 'style="margin-bottom: '. $margin_bottom .';"';
		} elseif ( $margin_top ) {
			$style_attr = 'style="margin-top: '. $margin_top .';"';
		} else {
			$style_attr = NULL;
		}
	 return '<hr class="symple-divider '. $style .'" '.$style_attr.' />';
	}
	add_shortcode( 'symple_divider', 'symple_divider_shortcode' );
}
