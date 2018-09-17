<?php
/*
Plugin Name: Sao Custom Image
Description: The custom image plugin.
Author: Saoshyant
*/ 
/*********************************************************************************************
Registers Custom Imager Post Type
*********************************************************************************************///
function sao_image_post_type() {
	$labels = array(
		'name' 					=> __('Image','sao'),
		'singular_name'			=> __('Image','sao'),
		'add_new'				=> __('Add New','sao'),
		'add_new_item'			=>__('Add New Image','sao'),
		'edit_item'				=> __('Edit Image','sao'),
		'new_item'				=> __('New Image','sao'),
		'view_item'				=> __('View Image','sao'),
 		'all_items'				=>__('All Image','sao'),
 		'search_items'			=> __('Search Image','sao'),
		'not_found'				=>  __('No Image found','sao'),
		'not_found_in_trash'	=>__('No Image found in trash','sao'),
		'parent_item_colon'		=> '',
		'menu_name'				=> __('Sao Image','sao')
	);
	
	$args = array(
		'labels'				=> $labels,
		'public'				=> true,
		'publicly_queryable'	=> true,
		'show_ui'				=> true, 
		'show_in_menu'			=> true, 
		'query_var'				=> true,
		'rewrite'				=> true,
		'capability_type'		=> 'post',
		'has_archive'			=> false, 
		'hierarchical'			=> false,
		'menu_position'			=> null,
		'supports' => array( 'title', 'thumbnail' )
	); 

	register_post_type( 'sao_image', $args );
}
add_action( 'init', 'sao_image_post_type' );
 
 
add_action( 'init', 'sao_images_taxonomy', 0 );
function sao_images_taxonomy() {
 
   $labels = array(
    'name'							=> __( 'Category Image','sao' ),
    'singular_name'					=> __( 'Category Image','sao'  ),
    'search_items'					=> __( 'Search Images' ,'sao' ),
    'popular_items'					=> __( 'Popular Images','sao'  ),
    'all_items' 					=> __( 'All Images' ,'sao' ),
    'parent_item'					=> __( 'Parent Image' ,'sao' ),
    'edit_item'						=> __( 'Edit Topic','sao' ), 
    'update_item' 					=> __( 'Update Image','sao'  ),
    'add_new_item'					=> __( 'Add New Image','sao'  ),
    'new_item_name'			 		=> __( 'New Topic Name' ,'sao' ),
    'separate_items_with_commas'	=> __( 'Separate Imagers with commas' ,'sao' ),
    'add_or_remove_items'			=> __( 'Add or remove Imagers','sao'  ),
    'choose_from_most_used' 		=> __( 'Choose from the most used Imagers','sao'  ),
    'menu_name' 					=> __( 'Imagers' ,'sao' ),
  ); 


// Now register the taxonomy

  register_taxonomy('sao_images','sao_image', array(
    'hierarchical' 					=> true,
    'labels' 						=> $labels,
    'show_ui' 						=> true,
    'show_admin_column'				=> true,
    'query_var'						=> true,
    'rewrite' 						=> array( 'slug' => 'sao_images' ),
  ));

}
 

add_action('manage_sao_image_posts_custom_column', 'sao_display_thumbnails_column', 5, 2);

function sao_display_thumbnails_column($column_name, $post_id){
  switch($column_name){
    case 'new_post_thumb':
      $post_thumbnail_id = get_post_thumbnail_id($post_id);
      if (!empty($post_thumbnail_id)) {
        $post_thumbnail_img = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail' );
        echo '<img width="100" src="' . esc_url($post_thumbnail_img[0]) . '" />';
      }
      break;
    case 'new_post_excerpt':
	$the_excerpt = strip_tags(get_the_excerpt());
  	if ( strlen($the_excerpt) > 200 && 200){
 		 $content= mb_substr($the_excerpt, 0,200); $dots='...';
		 
	}else{
		$content= @$the_excerpt;
		$dots='';
	}
	  echo esc_html($content),esc_html($dots);	
      break;
	  
  }
}

 


add_action( 'add_meta_boxes', 'sao_image_link' );
function sao_image_link()
{
    add_meta_box( 'link-meta-box-id', 'Link', 'sao_image_callback', 'sao_image', 'normal', 'high' );
}

function sao_image_callback( $post )
{
    $values = get_post_custom( $post->ID );
    $link = isset( $values['sao_image_link'] ) ? $values['sao_image_link'][0] : '';

    wp_nonce_field( 'my_sao_image_nonce', 'sao_image_nonce' );
    ?>
    <p>
 		<input type="text" name="sao_image_link" id="sao_image_link"  value="<?php echo esc_url($link); ?>" style="width:100%;" />

    </p>
    <p><?php echo esc_html__('Add the link of the Image','sao');?></p>
    <?php   
}

add_action( 'save_post', 'sao_image_link_save' );
function sao_image_link_save( $post_id )
{
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['sao_image_nonce'] ) || !wp_verify_nonce( $_POST['sao_image_nonce'], 'my_sao_image_nonce' ) ) return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;

 
    // Probably a good idea to make sure your data is set

    if( isset( $_POST['sao_image_link'] ) )
        update_post_meta( $post_id, 'sao_image_link', $_POST['sao_image_link'] );

}
?>