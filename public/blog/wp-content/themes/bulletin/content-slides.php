<?php
/**
 * This file is used to show your homepage slides
 * @package Bulletin WordPress Theme
 * @since 1.0
 * @author WPExplorer : http://www.wpexplorer.com
 * @copyright Copyright (c) 2012, WPExplorer
 * @link http://www.wpexplorer.com
 */

if ( class_exists( 'Symple_Slides_Post_Type' ) ) {
	global $post;
	$slides = get_posts( array(
		'post_type' => 'slides',
		'numberposts' => '-1',
		'order' => 'ASC'
	));
	if($slides) { ?>
    <div id="home-slider-wrap">
        <div id="home-slider" class="flexslider">
            <ul class="slides">
                <?php foreach( $slides as $post ) :	setup_postdata($post); ?>
                <?php if( has_post_thumbnail() || get_post_meta( get_the_ID(), 'wpex_slides_video', true) ){ ?>
                    <li>
                    	<div class="slide-inner fitvids">
                        	<?php if( get_post_meta( get_the_ID(), 'wpex_slides_video', true) !== '' ) {
								echo wp_oembed_get( get_post_meta( get_the_ID(), 'wpex_slides_video', true ) );
							} else {
								if( get_post_meta( get_the_ID(), 'wpex_slides_url', true) !== '' ) { ?>
                                <a href="<?php echo get_post_meta( get_the_ID(), 'wpex_slides_url', true); ?>" title="<?php the_title(); ?>" target="_<?php echo get_post_meta( get_the_ID(), 'wpex_slides_url_target', true); ?>">
                                	<img src="<?php echo aq_resize( wp_get_attachment_url( get_post_thumbnail_id() ),  wpex_img( 'slide_width' ), wpex_img( 'slide_height' ), wpex_img( 'slide_crop' ) ); ?>" alt="<?php the_title(); ?>" />
                                </a>
								<?php } else { ?>
                            	<img src="<?php echo aq_resize( wp_get_attachment_url( get_post_thumbnail_id() ),  wpex_img( 'slide_width' ), wpex_img( 'slide_height' ), wpex_img( 'slide_crop' ) ); ?>" alt="<?php the_title(); ?>" />
							<?php }
                             }
							 if($post->post_content !=='') { ?>
                                <div class="flex-caption"><?php the_content(); ?></div>
                            <?php } ?>
                        </div><!--/ slide-inner -->
                    </li>
                <?php } ?>
                <?php endforeach; wp_reset_postdata(); ?>
            </ul><!-- /slides -->
        </div><!-- /home-slider -->
    </div><!-- /home-slider-wrap -->
	<?php } 
} ?>