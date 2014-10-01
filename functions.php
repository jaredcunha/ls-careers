<?php
	/**
	 * Starkers functions and definitions
	 *
	 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
	 *
 	 * @package 	WordPress
 	 * @subpackage 	Starkers
 	 * @since 		Starkers 4.0
	 */

	/* ========================================================================================================================
	
	Required external files
	
	======================================================================================================================== */

	require_once( 'external/starkers-utilities.php' );

	/* ========================================================================================================================
	
	Theme specific settings

	Uncomment register_nav_menus to enable a single menu with the title of "Primary Navigation" in your theme
	
	======================================================================================================================== */

	add_theme_support('post-thumbnails');
	
	// register_nav_menus(array('primary' => 'Primary Navigation'));

	/* ========================================================================================================================
	
	Actions and Filters
	
	======================================================================================================================== */

	add_action( 'wp_enqueue_scripts', 'starkers_script_enqueuer' );

	add_filter( 'body_class', array( 'Starkers_Utilities', 'add_slug_to_body_class' ) );

  //remove_filter( 'the_content', 'wpautop' );

  remove_filter( 'the_excerpt', 'wpautop' );

	/* ========================================================================================================================
	
	Custom Post Types - include custom post types and taxonimies here e.g.

	e.g. require_once( 'custom-post-types/your-custom-post-type.php' );
	
	======================================================================================================================== */



	/* ========================================================================================================================
	
	Scripts
	
	======================================================================================================================== */

	/**
	 * Add scripts via wp_head()
	 *
	 * @return void
	 * @author Keir Whitaker
	 */	

	/* ========================================================================================================================
	
	Scripts
	
	======================================================================================================================== */
function add_custom_taxonomies() {
  // Add new "Locations" taxonomy to Posts
  register_taxonomy('location', 'post', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => true,
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x( 'Locations', 'taxonomy general name' ),
      'singular_name' => _x( 'Location', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Locations' ),
      'all_items' => __( 'All Locations' ),
      'parent_item' => __( 'Parent Location' ),
      'parent_item_colon' => __( 'Parent Location:' ),
      'edit_item' => __( 'Edit Location' ),
      'update_item' => __( 'Update Location' ),
      'add_new_item' => __( 'Add New Location' ),
      'new_item_name' => __( 'New Location Name' ),
      'menu_name' => __( 'Locations' ),
    ),
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'locations', // This controls the base slug that will display before each term
      'with_front' => false, // Don't display the category base before "/locations/"
      'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
    ),
  ));

// Add new "Locations" taxonomy to Posts
  register_taxonomy('department', 'post', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => true,
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x( 'Departments', 'taxonomy general name' ),
      'singular_name' => _x( 'Department', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Departments' ),
      'all_items' => __( 'All Departments' ),
      'parent_item' => __( 'Parent Department' ),
      'parent_item_colon' => __( 'Parent Department:' ),
      'edit_item' => __( 'Edit Department' ),
      'update_item' => __( 'Update Department' ),
      'add_new_item' => __( 'Add New Department' ),
      'new_item_name' => __( 'New Department Name' ),
      'menu_name' => __( 'Department' ),
    ),
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'departments', // This controls the base slug that will display before each term
      'with_front' => false, // Don't display the category base before "/locations/"
      'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
    ),
  ));
// Add new "Locations" taxonomy to Posts
  register_taxonomy('status', 'post', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => true,
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x( 'Status', 'taxonomy general name' ),
      'singular_name' => _x( 'Status', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Status' ),
      'all_items' => __( 'All Status' ),
      'parent_item' => __( 'Parent Status' ),
      'parent_item_colon' => __( 'Parent Status:' ),
      'edit_item' => __( 'Edit Status' ),
      'update_item' => __( 'Update Status' ),
      'add_new_item' => __( 'Add New Status' ),
      'new_item_name' => __( 'New Status Name' ),
      'menu_name' => __( 'Status' ),
    ),
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'departments', // This controls the base slug that will display before each term
      'with_front' => false, // Don't display the category base before "/locations/"
      'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
    ),
  ));
}

add_filter("manage_edit_theme_columns", 'theme_columns'); 
 
function theme_columns($theme_columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'header_icon' => '',
//      'description' => __('Description'),
        'slug' => __('Slug'),
        'posts' => __('Posts')
        );
    return $new_columns;
}

add_action( 'init', 'add_custom_taxonomies', 0 );






/* ========================================================================================================================

Department/Location List for Search

======================================================================================================================== */
function custom_taxonomy_dropdown( $taxonomy ) {
	$terms = get_terms( $taxonomy );
	if ( $terms ) {
		printf( '<select name="%s" class="postform">', esc_attr( $taxonomy ) );
		printf( '<option value="">all '.$taxonomy.'s </option>');
		foreach ( $terms as $term ) {
			printf( '<option value="%s">%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
		}
		print( '</select>' );
	}
}

/* ========================================================================================================================

Location List HP

======================================================================================================================== */
function location_list( $taxonomy ) {
	$terms = get_terms( $taxonomy );
	if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
	    $count = count($terms);
	    $i=0;
	    $term_list = '<ul class="locations-list">';
	    foreach ($terms as $term) {
	        $i++;
	    	$term_list .= '<li><a href="' . get_term_link( $term ) . '" >' . $term->name  .' <span class="count">(' . $term->count . ' job postings)</span></a></li>';
	    	if ($count != $i) {
	            
	        }
	        else {
	            $term_list .= '</ul>';
	        }
	    }
	    echo $term_list;
	}
}





// THIS THEME USES wp_nav_menu() IN TWO LOCATIONS FOR CUSTOM MENU.
function register_my_menu() {
  register_nav_menu('header-menu',__( 'Header Menu' ));
}
add_action( 'init', 'register_my_menu' );
