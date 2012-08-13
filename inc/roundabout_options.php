<?php
/* CURRENT OPTIONS:

wprabt_post_category - select post category from current category dropdown
wprabt_post_type - select post type from post type dropdown
wprabt_show_posts - input integer for number of posts to show
wprabt_date_format - select from date formats dropdown
wprabt_text_length - input integer for wprabt text length
wprabt_include_images - select 'yes' or 'no' from dropdown

*/

//returns assoc array of all categories
function wprabt_get_categories(){

	$cats = get_categories();
	$categories = array();
	
	$i = 0;
	
	foreach($cats as $cat) {
  		$categories[$cat->cat_name] = $cat->cat_ID;
	}	
	
    return $categories;
    
}

//returns assoc array of output date and corresponding PHP the_date() format

//returns all registered post types
function wprabt_get_post_types(){
	$post_types = get_post_types('','names'); 
	
	return $post_types;
}

/*set options

each option needs a name, default value, description, and input_type (dropdown or text)
dropdown options need a data field that takes a single dimensional associative array as its value

*/
function set_options(){
	
	$cat_data = wprabt_get_categories();
	$post_type_data = wprabt_get_post_types();
	
	$options = array(
		'slider_height' => array ( 
			'name' => 'wprabt_img_height' , 
			'default' => '300', 
			'desc' => 'Set image height', 
			'input_type' => 'text'
			),
		'slider_width' => array ( 
			'name' => 'wprabt_img_width' , 
			'default' => '750', 
			'desc' => 'Set image width', 
			'input_type' => 'text'
			),
		'slider_mode' => array ( 
			'name' => 'wprabt_slider_mode' , 
			'default' => 'featured-image', 
			'desc' => 'Select slider mode (post attachments or featured images)', 
			'input_type' => 'dropdown', 
			'data' => array(
				'Post Attachments' => 'attachment', 
				'Post "Featured Image"' => 'featured-image')
			),
		'post_category' => array ( //option 'slug'
			'name' => 'wprabt_post_category', 
			'default' => '0', 
			'desc' => 'Select a post category for your slider', 
			'input_type' => 'dropdown', 
			'data' => $cat_data //data should be single dimensional assoc array
			),
		'show_posts' => array ( 
			'name' => 'wprabt_show_posts', 
			'default' => '4', 
			'desc' => 'How many slides (posts) do you want to show?', 
			'input_type' => 'text'
			),
		'post_order' => array ( 
			'name' => 'wprabt_order_posts' , 
			'default' => 'DESC', 
			'desc' => 'How do you want to order your posts?', 
			'input_type' => 'dropdown', 
			'data' => array(
				'Ascending' => 'ASC', 
				'Descending' => 'DESC')
			),
		'post_link' => array ( 
			'name' => 'wprabt_posts_link' , 
			'default' => 'yes', 
			'desc' => 'Do you want to link to the post?', 
			'input_type' => 'dropdown', 
			'data' => array(
				'yes' => 'yes', 
				'no' => 'no') 
			)
		
	);

	return $options;
	
}

//create settings page
function wprabt_settings() {
	?>
		<div class="wrap">	
			<h2><?php _e('jQuery Roundabout Slider for Posts Settings', WPRABT_UNIQUE); ?></h2>
			<div id="timeline_quick_links">
				<?php /*include('inc/timeline_links.php'); */?>
			</div>
		<?php
		if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
			?>
			<div id="message" class="updated fade"><p><strong><?php _e('Settings Updated', WPRABT_UNIQUE); ?></strong></p></div>
			<?php
		}
		?>
			<form method="post" action="<?php echo esc_url('options.php');?>">
				<div>
					<?php settings_fields('wprabt-settings'); ?>
				</div>
				
				<?php
					$options = set_options();
					
					?>
				<table class="form-table">
				<?php foreach($options as $option){ ?>
					<?php 
						//if option type is a dropdown, do this
						if ( $option['input_type'] == 'dropdown'){ ?>
							<tr valign="top">
				        		<th scope="row"><?php _e($option['desc'], WPRABT_UNIQUE); ?></th>
				        			<td><select id="<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>">
				        					<?php foreach($option['data'] as $opt => $value){ ?>
												<option <?php if(get_option($option['name']) == $value){ echo 'selected="selected"';}?> name="<?php echo $option['name']; ?>" value="<?php echo $value; ?>"><?php echo $opt ; ?></option>
												<? } //endforeach ?>
										</select>
									</td>
					        </tr>
				    <?php 
				    	//if option type is text, do this
				    	}elseif ( $option['input_type'] == 'text'){ ?>
				    		<tr valign="top">
				        		<th scope="row"><?php _e($option['desc'], WPRABT_UNIQUE); ?></th>
				        			<td><input id="<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>" value="<?php echo get_option($option['name']); ?>" />
									</td>
					        </tr>
			     <?php 
			     		
			     		}else{} //endif
			     		
			     	} //endforeach ?>
			        
			    </table>
			    <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Update', WPRABT_UNIQUE); ?>" /></p>
			</form>
		</div>
	<?php
}

//register settings loops through options
function wprabt_register_settings()
{
	$options = set_options(); //get options array
	
	foreach($options as $option){
		register_setting('wprabt-settings', $option['name']); //register each setting with option's 'name'
		
		if (get_option($option['name']) === false) {
			add_option($option['name'], $option['default'], '', 'yes'); //set option defaults
		}
	}

	if (get_option('wprabt_promote_plugin') === false) {
		add_option('wprabt_promote_plugin', '0', '', 'yes');
	}

}
add_action( 'admin_init', 'wprabt_register_settings' );


//add settings page
function wprabt_settings_page() {	
	add_options_page('Slider Settings', 'Slider Settings', 'manage_options', WPRABT_UNIQUE, 'wprabt_settings');
}
add_action("admin_menu", 'wprabt_settings_page');

?>