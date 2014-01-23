<?php
/***
* Special Thanks To Devin Price
* This file is a modified of the original plugin found
* @https://github.com/devinsays/slides-post-type - Special Thanks!
***/

if ( ! class_exists( 'Symple_Slides_Post_Type' ) ) :
class Symple_Slides_Post_Type {

	// Current plugin version
	var $version = 1;

	function __construct() {

		// Runs when the plugin is activated
		register_activation_hook( __FILE__, array( &$this, 'plugin_activation' ) );

		// Add support for translations
		load_plugin_textdomain( 'symple', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		// Adds the slides post type and taxonomies
		add_action( 'init', array( &$this, 'slides_init' ) );

		// Thumbnail support for slides posts
		add_theme_support( 'post-thumbnails', array( 'slides' ) );

		// Adds columns in the admin view for thumbnail and taxonomies
		add_filter( 'manage_edit-slides_columns', array( &$this, 'slides_edit_columns' ) );
		add_action( 'manage_posts_custom_column', array( &$this, 'slides_column_display' ), 10, 2 );

		// Give the slides menu item a unique icon
		add_action( 'admin_head', array( &$this, 'slides_icon' ) );
	}

	/**
	 * Flushes rewrite rules on plugin activation to ensure slides posts don't 404
	 * http://codex.wordpress.org/Function_Reference/flush_rewrite_rules
	 */

	function plugin_activation() {
		$this->slides_init();
		flush_rewrite_rules();
	}

	function slides_init() {

		/**
		 * Enable the Slides custom post type
		 * http://codex.wordpress.org/Function_Reference/register_post_type
		 */

		$labels = array(
			'name' => __( 'Slides', 'symple' ),
			'singular_name' => __( 'Slides Item', 'symple' ),
			'add_new' => __( 'Add New Item', 'symple' ),
			'add_new_item' => __( 'Add New Slides Item', 'symple' ),
			'edit_item' => __( 'Edit Slides Item', 'symple' ),
			'new_item' => __( 'Add New Slides Item', 'symple' ),
			'view_item' => __( 'View Item', 'symple' ),
			'search_items' => __( 'Search Slides', 'symple' ),
			'not_found' => __( 'No slides items found', 'symple' ),
			'not_found_in_trash' => __( 'No slides items found in trash', 'symple' )
		);
		
		$args = array(
	    	'labels' => $labels,
	    	'public' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'capability_type' => 'post',
			'rewrite' => array("slug" => "slides"), // Permalinks format
			'menu_position' => 5,
			'has_archive' => true
		); 
		
		$args = apply_filters('symple_slides_args', $args);
		
		register_post_type( 'slides', $args );

	}

	/**
	 * Add Columns to Slides Edit Screen
	 * http://wptheming.com/2010/07/column-edit-pages/
	 */

	function slides_edit_columns( $slides_columns ) {
		$slides_columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => _x('Title', 'column name'),
			"slides_thumbnail" => __('Thumbnail', 'symple'),
			"author" => __('Author', 'symple'),
			"comments" => __('Comments', 'symple'),
			"date" => __('Date', 'symple'),
		);
		$slides_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
		return $slides_columns;
	}

	function slides_column_display( $slides_columns, $post_id ) {

		// Code from: http://wpengineer.com/display-post-thumbnail-post-page-overview

		switch ( $slides_columns ) {

			// Display the thumbnail in the column view
			case "slides_thumbnail":
				$width = (int) 80;
				$height = (int) 80;
				$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );

				// Display the featured image in the column view if possible
				if ( $thumbnail_id ) {
					$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
				}
				if ( isset( $thumb ) ) {
					echo $thumb;
				} else {
					echo __('None', 'symple');
				}
				break;	

			// Display the slides tags in the column view
			case "slides_category":

			if ( $category_list = get_the_term_list( $post_id, 'slides_category', '', ', ', '' ) ) {
				echo $category_list;
			} else {
				echo __('None', 'symple');
			}
			break;	

			// Display the slides tags in the column view
			case "slides_tag":

			if ( $tag_list = get_the_term_list( $post_id, 'slides_tag', '', ', ', '' ) ) {
				echo $tag_list;
			} else {
				echo __('None', 'symple');
			}
			break;			
		}
	}
	
	/**
	 * Displays the custom post type icon in the dashboard
	 */

	function slides_icon() { ?>
	    <style type="text/css" media="screen">
	        #menu-posts-slides .wp-menu-image {
	            background: url(<?php echo plugin_dir_url( __FILE__ ); ?>images/slides-icon.png) no-repeat 6px -17px !important;
	        }
			#menu-posts-slides:hover .wp-menu-image, #menu-posts-slides.wp-has-current-submenu .wp-menu-image {
	            background-position:6px 7px !important;
	        }
	    </style>
	<?php }

}

new Symple_Slides_Post_Type;

endif;