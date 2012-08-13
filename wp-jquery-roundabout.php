<?php
/*
	Plugin Name: jQuery Roundabout for Posts
	Plugin URI: http://wordpress.org/plugins/
	Description: jQuery Roundabout Carousel/Slider for WordPress Posts
	Author: Wylie Hobbs
	Version: 1.0
	Author URI: http://wyliehobbs.com
	Text Domain: wp-jquery-roundabout
	
	jquery.roundabout.js by Fred LeBlanc
	
 */

//define global (mainly for use in options)
define('WPRABT_UNIQUE', 'wprabt');


require_once('lib/functions.php');

function wprabt_shortcode($atts){
	$args = shortcode_atts( array(
      'cat' => '',
      'type' => 'post',
      'show' => 3,
      'order' => 'DESC',
      'mode' => 'post',
      'textlength' => 200
     	), $atts	
     );

	return display_carousel($args);
}
add_shortcode('wprabt-slider', 'wprabt_shortcode');

function display_carousel(){
	$post_args = array(
			'post_type' => 'post',
			'numberposts' => get_option('wprabt_show_posts'),
			'category' => get_option('wprabt_post_category'),
			'order' => get_option('wprabt_order_posts'),
			'post_status' => 'publish'
		);
		
	//get a few options
	$mode = get_option('wprabt_slider_mode');
	$class = wprabt_get_slider_mode($mode);
	$link_to_post = get_option('wprabt_post_link');
	
	echo '<div id="roundabout-container">';
		echo '<ul id="wp-roundabout" class="' . $class . '">';
		
		
		if($mode == 'attachment'){
		
			foreach ($posts as $post){
				
				//arguments for attachments query
				$att_args = array(
				 	'post_type' => 'attachment',
				 	'numberposts' => $post_args['numberposts'],
				 	'order' => $post_args['order'],
				 	'post_status' => null,
				 	'post_parent' => $post->ID
				);
	
				 $attachments = get_posts( $att_args );
				 $count = count($attachments);
				 $image = '';
				 $i = 0;
			 
				if ( $attachments && $count > 0 ) {
					  foreach ( $attachments as $attachment ) {
					  		echo '<li>';
						  		if($link_to_post == 'yes'){
						  			echo '<a href="'.get_permalink($post->ID).'">';
						  			echo wp_get_attachment_image( $attachment->ID, 'wprabt-slider-image' );
						  			echo '</a>';
						  		}else{
						  			echo wp_get_attachment_image( $attachment->ID, 'wprabt-slider-image' );
						  		}
					  		echo '</li>';
						}
					}
				
				//implement choice of attachment to use
				
				/*else {
					  foreach ( $attachments as $attachment ) {
					   		$image[$i] =  wp_get_attachment_image( $attachment->ID, 'wprabt-slider-image' );
					   		$i++;
						}
						if($post->ID == 13){
							echo '<li><a href="'.get_permalink($post->ID).'">'.$image[1].'</a></li>';
						}else{
							echo '<li><a href="'.get_permalink($post->ID).'">'.$image[0].'</a></li>';
						}
					}
				}*/
				
			}//endforeach
			
		}elseif($mode == 'featured-image'){
			
			$posts = get_posts( $post_args );

			foreach($posts as $post){
				echo '<li>';
				if($link_to_post == 'yes'){
					echo '<a href="'.get_permalink($post->ID).'">';
						echo get_the_post_thumbnail( $post->ID, 'wprabt-slider-image' );
					echo '</a>';
				}else{
					echo get_the_post_thumbnail( $post->ID, 'wprabt-slider-image' );
				}
				echo '</li>';
			}
		
		
		}elseif($mode == 'hybrid'){
			echo '<li><a href="' . get_permalink($post->ID) . '">';
				echo '<div class="wp-rabt-image">' . get_the_post_thumbnail($post->ID, 'wprabt-slider-image') . '</div>';
				echo '<div class="wp-rabt-content">';
					echo '<h5>' . $post->post_title . '</h5>';
					echo custom_text_length($args['textlength'], 'read more', 'content');
				echo '</div>';
			echo '</a></li>';
		
		
		}else{
			echo 'Something went wrong…';
			
		}//endif $mode
	
		
		echo '</ul>';
	echo '</div>';
	wp_reset_postdata();
	
}//end display()

function wprabt_get_slider_mode($mode){

	$class = '';
	
	if($mode == 'attachment' || $mode == 'featured-image'){
		$class='attachment-mode';
	}
	else{
		$class='default-mode';
	}
	
	return $class;

}

add_theme_support('post-thumbnails');
add_filter('get_the_content', 'do_shortcode');
add_filter('get_the_excerpt', 'do_shortcode');


function wprabt_image_size(){
	$width = get_option('wprabt_img_width');
	$height = get_option('wprabt_img_height');

	add_image_size( 'wprabt-slider-image', $width, $height ); //80 pixels wide (and unlimited height)
}

add_action('init', 'wprabt_image_size');

function roundabout_scripts() 
{
	wp_enqueue_script('jquery-roundabout', plugins_url('/js/jquery.roundabout.min.js', __FILE__),
		array('jquery')
	);
	wp_enqueue_script(
		'roundabout-init',
		plugins_url('/js/roundabout.js', __FILE__),
		array('jquery-roundabout')
	);
	wp_register_style($handle = 'rabt-default-css', $src = plugins_url('skins/default.css', __FILE__), $deps = array(), $ver = '1.0.0', $media = 'all');
	wp_enqueue_style('rabt-default-css');
}

add_action ('wp_enqueue_scripts', 'roundabout_scripts');


require_once('inc/roundabout_options.php');
