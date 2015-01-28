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

  

	/* ========================================================================================================================
	
	Custom Post Types - include custom post types and taxonimies here e.g.

	e.g. require_once( 'custom-post-types/your-custom-post-type.php' );
	
	======================================================================================================================== */

	require_once('custom-post-types/careers.php');


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

add_filter("manage_edit_theme_columns", 'theme_columns'); 
 
function theme_columns($theme_columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'header_icon' => '',
        'description' => __('Description'),
        'slug' => __('Slug'),
        'posts' => __('Posts')
        );
    return $new_columns;
}




/* ========================================================================================================================

Department/Location List for Search

======================================================================================================================== */
function custom_taxonomy_dropdown( $taxonomy ) {
	$terms = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
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
	$terms = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
	if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
	    $count = count($terms);
	    $term_list = '<ul class="locations-list">';
	    foreach ($terms as $term) {
			if( $term->count > 0 ) {
				$term_list .= '<li><a href="' . get_term_link( $term ) . '" >' . $term->name  .' <span class="count">(' . $term->count . ' job postings)</span></a></li>';
			}
	    }
		$term_list .= '</ul>';
	    echo $term_list;
	}
}





// THIS THEME USES wp_nav_menu() IN TWO LOCATIONS FOR CUSTOM MENU.
function register_my_menu() {
  register_nav_menu('header-menu',__( 'Header Menu' ));
}
add_action( 'init', 'register_my_menu' );
