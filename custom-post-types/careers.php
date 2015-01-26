<?php
/* ========================================================================================================================

Careers Post Type & Attach Locations/Departments Taxonomies

======================================================================================================================== */
function add_careers_post_type() {
	$labels = array(
			'name'               => _x( 'Careers', 'post type general name', 'your-plugin-textdomain' ),
			'singular_name'      => _x( 'Career', 'post type singular name', 'your-plugin-textdomain' ),
			'menu_name'          => _x( 'Careers', 'admin menu', 'your-plugin-textdomain' ),
			'name_admin_bar'     => _x( 'Career', 'add new on admin bar', 'your-plugin-textdomain' ),
			'add_new'            => _x( 'Add New', 'book', 'your-plugin-textdomain' ),
			'add_new_item'       => __( 'Add New Career', 'your-plugin-textdomain' ),
			'new_item'           => __( 'New Career', 'your-plugin-textdomain' ),
			'edit_item'          => __( 'Edit Career', 'your-plugin-textdomain' ),
			'view_item'          => __( 'View Career', 'your-plugin-textdomain' ),
			'all_items'          => __( 'All Careers', 'your-plugin-textdomain' ),
			'search_items'       => __( 'Search Careers', 'your-plugin-textdomain' ),
			'parent_item_colon'  => __( 'Parent Careers:', 'your-plugin-textdomain' ),
			'not_found'          => __( 'No careers found.', 'your-plugin-textdomain' ),
			'not_found_in_trash' => __( 'No careers found in Trash.', 'your-plugin-textdomain' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'careers' ),
			'capability_type'    => 'post',
			'capabilities' => array(
			    'create_posts' => false, // Removes support for the "Add New" function
				'delete_published_posts' => false
			 ),
			'map_meta_cap'		 => true,
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array(  )
		);

		register_post_type( 'career', $args );
		
		
}

add_action( 'init', 'add_careers_post_type', 1 );

function add_careers_post_status() {
	
	register_post_status( 'removed_from_jobvite', array(
			'label'                     => _x( 'Removed from Jobvite', 'career' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Removed from Jobvite <span class="count">(%s)</span>', 'Removed from Jobvite <span class="count">(%s)</span>' ),
		) );
		
}

add_action( 'init', 'add_careers_post_status', 2 );

function add_careers_taxonomies() {
	
    // Add new "Locations" taxonomy to Careers Post Type
    register_taxonomy( 'location' , 'career' , array(
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
        'slug' => 'location', // This controls the base slug that will display before each term
        'with_front' => false, // Don't display the category base before "/locations/"
        'hierarchical' => true 
      ),
    ));

  // Add new "Locations" taxonomy to Posts
    register_taxonomy('department', 'career', array(
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
        'menu_name' => __( 'Departments' ),
      ),
      // Control the slugs used for this taxonomy
      'rewrite' => array(
        'slug' => 'department', // This controls the base slug that will display before each term
        'with_front' => false, // Don't display the category base before "/locations/"
        'hierarchical' => true 
      ),
    ));
	
    register_taxonomy('status', 'career', array(
      // Hierarchical taxonomy (like categories)
      'hierarchical' => true,
      // This array of options controls the labels displayed in the WordPress Admin UI
      'labels' => array(
        'name' => _x( 'Statuses', 'taxonomy general name' ),
        'singular_name' => _x( 'Status', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Status' ),
        'all_items' => __( 'All Status' ),
        'parent_item' => __( 'Parent Status' ),
        'parent_item_colon' => __( 'Parent Status:' ),
        'edit_item' => __( 'Edit Status' ),
        'update_item' => __( 'Update Status' ),
        'add_new_item' => __( 'Add New Status' ),
        'new_item_name' => __( 'New Status Name' ),
        'menu_name' => __( 'Statuses' ),
      ),
      // Control the slugs used for this taxonomy
      'rewrite' => array(
        'slug' => 'status', // This controls the base slug that will display before each term
        'with_front' => false, // Don't display the category base before "/locations/"
        'hierarchical' => false
      ),
    ));
	
}

add_action( 'init', 'add_careers_taxonomies', 2 );

function get_terms_filter( $terms, $taxonomies, $args )
{
	global $wpdb;
	if( ! is_admin( ) && ! is_category( ) && is_archive( ) && is_search( ) ) {
		$taxonomy = $taxonomies[0];
		if ( ! is_array($terms) && count($terms) < 1 )
			return $terms;
		$filtered_terms = array();
		foreach ( $terms as $term )
		{
			$result = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts p JOIN $wpdb->term_relationships rl ON p.ID = rl.object_id WHERE rl.term_taxonomy_id = $term->term_id AND p.post_status = 'publish' LIMIT 1");
			if ( intval($result) > 0 )
				$filtered_terms[] = $term;
		}
		return $filtered_terms;
	}
	return $terms;
	
}
//add_filter('get_terms', 'get_terms_filter', 10, 3);

// Add term page
function pippin_taxonomy_add_new_meta_field() {
	// this will add the custom meta field to the add new term page
	?>
	<div class="form-field">
		<label for="term_meta[jobvite_slug]"><?php _e( 'Jobvite-assigned Slug'); ?></label>
		<input type="text" name="term_meta[jobvite_slug]" id="term_meta[jobvite_slug]" value="">
		<p class="description"><?php _e( 'This field is what was pulled from Jobvite and is used to sync categories even if the Pretty URL slug is changed.' ); ?></p>
	</div>
<?php
}
add_action( 'department_add_form_fields', 'pippin_taxonomy_add_new_meta_field', 10, 2 );
add_action( 'location_add_form_fields', 'pippin_taxonomy_add_new_meta_field', 10, 2 );


// Edit term page
function pippin_taxonomy_edit_meta_field($term) {
 
	// put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" ); ?>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[jobvite_slug]"><?php _e( 'Jobvite-assigned Slug'); ?></label></th>
		<td>
			<input type="text" name="term_meta[jobvite_slug]" id="term_meta[jobvite_slug]" value="<?php echo esc_attr( $term_meta['jobvite_slug'] ) ? esc_attr( $term_meta['jobvite_slug'] ) : ''; ?>">
			<p class="description"><?php _e( 'This field is what was pulled from Jobvite and is used to sync categories even if the Pretty URL slug is changed.'); ?></p>
		</td>
	</tr>
<?php
}
add_action( 'department_edit_form_fields', 'pippin_taxonomy_edit_meta_field', 10, 2 );
add_action( 'location_edit_form_fields', 'pippin_taxonomy_edit_meta_field', 10, 2 );

// Save extra taxonomy fields callback function.
function save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}  
add_action( 'edited_location', 'save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_location', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'edited_department', 'save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_department', 'save_taxonomy_custom_meta', 10, 2 );

//removes quick edit from custom post type list
function remove_quick_edit( $actions ) {
	global $post;
    if( $post->post_type == 'career' ) {
		unset($actions['inline hide-if-no-js']);
	}
    return $actions;
}

if (is_admin()) {
	add_filter('post_row_actions','remove_quick_edit',10,2);
}
