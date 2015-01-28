<?php
/**
 * Functions related to Career post type display in WordPress
 *
 * This file handles everything from registering the taxonomies and post type,
 * to formatting the display in the WordPress administration pages.
 *
 * @package LivingSocial_Careers
 * 
 */

/**
 * Registers Location, Department and Status as taxonomies related to the Careers post type
 */
add_action( 'init', 'add_careers_taxonomies', 1 );
function add_careers_taxonomies( ) {
	// Add new "Location" taxonomy to Careers post type
	register_taxonomy( 'location' , 'career' , array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => true,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name'              => _x( 'Locations', 'taxonomy general name' ),
			'singular_name'     => _x( 'Location', 'taxonomy singular name' ),
			'search_items'      =>  __( 'Search Locations' ),
			'all_items'         => __( 'All Locations' ),
			'parent_item'       => __( 'Parent Location' ),
			'parent_item_colon' => __( 'Parent Location:' ),
			'edit_item'         => __( 'Edit Location' ),
			'update_item'       => __( 'Update Location' ),
			'add_new_item'      => __( 'Add New Location' ),
			'new_item_name'     => __( 'New Location Name' ),
			'menu_name'         => __( 'Locations' ),
		),
		// Control the slugs used for this taxonomy
		'rewrite' => array(
			// This controls the base slug that will display before each term
			'slug'         => 'careers/locations', 
			// Don't display the category base before "/location/"
			'with_front'   => false, 
			'hierarchical' => true 
		),
	));
	// Add new "Department" taxonomy to Careers post type
	register_taxonomy( 'department', 'career', array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => true,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name' => _x( 'Departments', 'taxonomy general name' ),
			'singular_name'     => _x( 'Department', 'taxonomy singular name' ),
			'search_items'      =>  __( 'Search Departments' ),
			'all_items'         => __( 'All Departments' ),
			'parent_item'       => __( 'Parent Department' ),
			'parent_item_colon' => __( 'Parent Department:' ),
			'edit_item'         => __( 'Edit Department' ),
			'update_item'       => __( 'Update Department' ),
			'add_new_item'      => __( 'Add New Department' ),
			'new_item_name'     => __( 'New Department Name' ),
			'menu_name'         => __( 'Departments' ),
		),
		// Control the slugs used for this taxonomy
		'rewrite' => array(
			// This controls the base slug that will display before each term
			'slug'         => 'careers/departments', 
			// Don't display the category base before "/department/"
			'with_front'   => false, 
			'hierarchical' => true 
		),
	));
	// Add new "Status" taxonomy to Career post type. UI and URLs use 'type' instead.
	register_taxonomy( 'status', 'career', array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => true,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name' => _x( 'Career Types', 'taxonomy general name' ),
			'singular_name' => _x( 'Career Type', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Career Types' ),
			'all_items' => __( 'All Career Types' ),
			'parent_item' => __( 'Parent Career Type' ),
			'parent_item_colon' => __( 'Parent Career Type:' ),
			'edit_item' => __( 'Edit Career Type' ),
			'update_item' => __( 'Update Career Type' ),
			'add_new_item' => __( 'Add New Career Type' ),
			'new_item_name' => __( 'New Career Type Name' ),
			'menu_name' => __( 'Types' ),
		),
		// Control the slugs used for this taxonomy
		'rewrite' => array(
			// This controls the base slug that will display before each term
			'slug' => 'careers/type', 
			// Don't display the category base before "/status/"
			'with_front' => false, 
			'hierarchical' => false
		),
	));
}

/**
 * Registers Careers post type
 */
add_action( 'init', 'add_careers_post_type', 2 );
function add_careers_post_type( ) {
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
		'capabilities' 		 => array(
			'create_posts' 			 => false, // Removes support for the "Add New" function
			'delete_published_posts' => false
		),
		'map_meta_cap'		 => true,
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'			 => array('title','author')
	);
	register_post_type( 'career', $args );
}

/**
 * Registers "Removed from Jobvite" status for Careers post type
 *
 * This post status is more descriptive than existing WordPress statuses, and also hides the post from public display.
 *
 */
add_action( 'init', 'add_careers_post_status', 2 );
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

/**
 * Creates field so Jobvite slug for taxonomies is visible from the admin side of WordPress
 */
add_action( 'department_add_form_fields', 'career_taxonomy_add_new_meta_field', 10, 2 );
add_action( 'status_add_form_fields', 'career_taxonomy_add_new_meta_field', 10, 2 );
add_action( 'location_add_form_fields', 'career_taxonomy_add_new_meta_field', 10, 2 );
function career_taxonomy_add_new_meta_field( ) {
	?>
	<div class="form-field">
		<label for="term_meta[jobvite_slug]"><?php _e( 'Jobvite Notification'); ?></label>
		<input type="text" name="term_meta[jobvite_slug]" id="term_meta[jobvite_slug]" value="" disabled>
		<p class="description" style="color: red;"><?php _e( 'Because this taxonomy is not generated by Jobvite, careers will need to be manually added to it.' ); ?></p>
	</div>
<?php
}

/**
 * Makes Jobvite slug for taxonomies visible from the edit taxonomy screen
 *
 *
 * @param object $term The taxonomy to include the edit box for.
 */
add_action( 'department_edit_form_fields', 'career_taxonomy_edit_meta_field', 10, 2 );
add_action( 'location_edit_form_fields', 'career_taxonomy_edit_meta_field', 10, 2 );
add_action( 'status_edit_form_fields', 'career_taxonomy_edit_meta_field', 10, 2 );
function career_taxonomy_edit_meta_field( $term ) {
 	$taxonomy_id = $term->term_id;
	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$taxonomy_id" ); 
	$slug = esc_attr( $term_meta['jobvite_slug'] );
	?>

	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[jobvite_slug]"><?php _e( 'Jobvite-assigned Slug' ); ?></label></th>
		<td>
			<input type="text" name="term_meta[jobvite_slug]" id="term_meta[jobvite_slug]" value="<?php echo $slug; ?>" disabled>
			<?php 
				if( '' == $slug ) {
					echo '<p class="description"  style="color: red;">Because this taxonomy was not generated by Jobvite, careers will need to be manually added to it.</p>';
				} else {
					echo '<p class="description">This slug is from Jobvite and is used to sync new careers with this taxonomy. If you want to edit the URL slug or title, please do so under "Name" or "Slug" and future Jobvite updates will sync using those labels.</p>';
				}	
				?>
		</td>
	</tr>
	
<?php
}

/**
 * Unused helper function, in case the Jobvite ID needs to be modified by the developer. Saves new Jobvite ID.
 *
 * If you need to edit the Jobvite ID, delete the 'disabled' from the function before this
 *
 * @see career_taxonomy_edit_meta_field()
 * @param integer $term_id WordPress term ID of the taxonomy being saved.
 */
add_action( 'edited_location', 'save_taxonomy_custom_meta', 10, 2 );  
add_action( 'edited_department', 'save_taxonomy_custom_meta', 10, 2 );  
add_action( 'edited_status', 'save_taxonomy_custom_meta', 10, 2 );
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

/**
 * Removes 'quick edit' link from careers list view, for clarity
 *
 * @global object $post WordPress post object.
 *
 * @param array $actions The actions made visible to the admin to interact with careers.
 * @param type $var Optional. Description.
 * @return type Description.
 */
function remove_quick_edit( $actions ) {
	global $post;
    if( $post->post_type == 'career' ) {
		unset($actions['inline hide-if-no-js']);
		if( $post->post_status == 'removed_from_jobvite' ) {
			unset($actions['view']);
		}
	}
    return $actions;
}

/**
 * Calls remove_quick_edit()
 */
add_action( 'init', 'remove_quick_edit_caller', 3 );
function remove_quick_edit_caller( ) {
	if (is_admin()) {
		add_filter('post_row_actions','remove_quick_edit',10,2);
		// Filter Yoast Meta Priority
		add_filter( 'wpseo_metabox_prio', function() { return 'low';});
	}
}

/**
 * Adds custom meta box to career post type edit screen, and streamlines edit screen.
 */
add_action('add_meta_boxes', 'show_career_info_box' );
function show_career_info_box( ) {
	add_meta_box(
		'career-info-box',
		__( 'Jobvite Information'),
		'career_info_box',
		'career',
		'normal',
		'high'
	);
	remove_meta_box( 'locationdiv', 'career', 'side' );
	remove_meta_box( 'departmentdiv', 'career', 'side' );
	remove_meta_box( 'statusdiv', 'career', 'side' );
	remove_meta_box( 'authordiv', 'career', 'normal' );
	remove_meta_box( 'slugdiv', 'career', 'normal' );
	remove_meta_box( 'commentsdiv', 'career', 'normal' );
	remove_meta_box( 'revisionsdiv', 'career', 'normal' );
	remove_meta_box( 'postexcerpt', 'career', 'normal' );
}

/**
 * Displays saved data from Jobvite
 */
function career_info_box( ) {
	if( is_admin( ) ) {
		wp_enqueue_script( 'jquery' );
	}
	global $post;
	// get post type by post
	$post_type = $post->post_type;

	// get career taxonomies
	$taxonomies = get_object_taxonomies( $post_type, 'objects' );
	//Collect current taxonomies (location/department/status)
	$taxonomy_data = array();
	
	foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){
		// get the terms related to post
		$terms = get_the_terms( $post->ID, $taxonomy_slug );
		$taxonomy_data[ $taxonomy_slug ] = $terms;
	}
	?>
	<script>
	jQuery(document).ready(function ($) {
		$('#title').attr('disabled','disabled');
	});
	</script>
	
	<p>The data in this career post is all provided by Jobvite. <br />
		If you wish to make updates, please do so on the Jobvite website. <br />
		Changes will sync here regularly.</p>
		
	<table class="form-table">
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Status:</label></th>
			<td><p><?php
				$c = 0;
				foreach( $taxonomy_data['status'] as $term ) {
					echo ( $c > 0 ) ? ',' : '';
					$c++;
					echo '<a href="'.get_term_link( $term->slug, 'status' ).'" target="_blank">'.$term->name.'</a>';
				}
			?></p></td>
		</tr>
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Location:</label></th>
			<td><p><?php
				$c = 0;
				foreach( $taxonomy_data['location'] as $term ) {
					echo ( $c > 0 ) ? ',' : '';
					$c++;
					echo '<a href="'.get_term_link( $term->slug, 'location' ).'" target="_blank">'.$term->name.'</a>';
				}
			?></p></td>
		</tr>
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Department:</label></th>
			<td><p><?php
				$c = 0;
				foreach( $taxonomy_data['department'] as $term ) {
					echo ( $c > 0 ) ? ',' : '';
					$c++;
					echo '<a href="'.get_term_link( $term->slug, 'department' ).'" target="_blank">'.$term->name.'</a>';
				}
			?></p></td>
		</tr>
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Description:</label></th>
			<td><?php echo $post->post_content; ?></td>
		</tr>
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Apply Now Link:</label></th>
			<td><?php $link = get_post_meta($post->ID, 'apply_now_link', true); ?>
			 <a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a>
			</td>
		</tr>
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Detail Link:</label></th>
			<td><?php $link = get_post_meta($post->ID, 'detail_link', true); ?>
			 <a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a>
		</tr>
	</table>
	<h2>Additional Jobvite Data</h2>
	<p>The following does not directly appear on the website, but is used to determine taxonomies in WordPress.</p>
	<table class="form-table">
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Requisition ID:</label></th>
			<td><?php echo get_post_meta($post->ID, 'requisition_id', true); ?></td>
		</tr>
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Jobvite ID:</label></th>
			<td><?php echo get_post_meta($post->ID, 'jobvite_id', true); ?></td>
		</tr>
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Jobvite-Assigned Status:</label></th>
			<td><?php echo get_post_meta($post->ID, 'jobvite_status', true); ?></td>
		</tr>
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Jobvite-Assigned Department:</label></th>
			<td><?php echo get_post_meta($post->ID, 'jobvite_department', true); ?></td>
		</tr>
		<tr>
			<th><label for="<?php //echo $this->prefix.'speaker_bio';?>">Jobvite-Assigned Location:</label></th>
			<td><?php echo get_post_meta($post->ID, 'jobvite_location', true); ?></td>
		</tr>
	</table>
	
	<?php
}

/**
 * Formats columns in admin career list to be more relevant.
 *
 * @param array $columns Default columns for WordPress displays.
 * @return array Updated columns.
 */
add_filter( 'manage_edit-career_columns', 'edit_career_columns' ) ;
function edit_career_columns( $columns ) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => 'Career',
		'date' => __( 'Date' ),
		'location' => __( 'Location' ),
		'department' => __( 'Department' ),
		'status' => __( 'Status' ),
		'livingsocial_url' => __( 'LivingSocial Career Page' ),
		'apply_url' => __( 'Jobvite Apply URL' )
	);

	return $columns;
}

/**
 * Formats output for custom columns in the career post type admin list.
 *
 * @see edit_career_columns()
 *
 * @param string $column Name of the column.
 * @param integer $post_id The Career post ID in WordPress.
 */
add_action( 'manage_career_posts_custom_column', 'manage_career_columns', 10, 2 );
function manage_career_columns( $column, $post_id ) {
	$post_type = get_post_type( $post_id );
	// get career taxonomies
	$taxonomies = get_object_taxonomies( $post_type, 'objects' );
	//Collect current taxonomies (location/department/status)
	$taxonomy_data = array();
	foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){
		// get the terms related to career post
		$terms = get_the_terms( $post_id, $taxonomy_slug );
		$taxonomy_data[ $taxonomy_slug ] = $terms;
	}
	switch( $column ) {
		case 'location' :
		foreach( $taxonomy_data['location'] as $term ) {
			echo ( $c > 0 ) ? ',' : '';
			$c++;
			echo '<a href="'.get_term_link( $term->slug, 'location' ).'" target="_blank">'.$term->name.'</a>';
		}
		break;
		case 'department' :
		foreach( $taxonomy_data['department'] as $term ) {
			echo ( $c > 0 ) ? ',' : '';
			$c++;
			echo '<a href="'.get_term_link( $term->slug, 'department' ).'" target="_blank">'.$term->name.'</a>';
		}
		break;
		case 'status' :
		foreach( $taxonomy_data['status'] as $term ) {
			echo ( $c > 0 ) ? ',' : '';
			$c++;
			echo '<a href="'.get_term_link( $term->slug, 'status' ).'" target="_blank">'.$term->name.'</a>';
		}
		break;
		case 'apply_url' :
		if( 'removed_from_jobvite' == get_post_status( $post_id ) ) {
			echo 'Hidden from public. No longer on Jobvite.';
		} else {
			$url = get_post_meta( $post_id, 'apply_now_link', true );
			echo '<a href="'.$url.'" target="_blank">View on Jobvite</a>';			
		}
		break;
		case 'livingsocial_url' :
		if( 'removed_from_jobvite' == get_post_status( $post_id ) ) {
			echo 'Hidden from public. No longer on Jobvite.';
		} else {
			$url = get_permalink( $post_id );
			echo '<a href="'.$url.'" target="_blank">View on LivingSocial Careers</a>';			
		}
		break;
		/* Just break out of the switch statement for everything else. */
		default :
		break;
	}
}

/**
 * Adds documentation page under Careers link in the WordPress admin navigation
 * Also adds options page, for overriding Jobvite behavior
 */
add_action( 'admin_menu', 'register_career_custom_submenu_page' );
function register_career_custom_submenu_page( ) {
	add_submenu_page( 'edit.php?post_type=career', 'Jobvite Career Documentation', 'Documentation', 'publish_pages', 'career-documentation', 'career_custom_submenu_page_callback' );
	add_submenu_page( 'edit.php?post_type=career', 'Jobvite Career Options Page', 'Options', 'publish_pages', 'career-options-page', 'career_options_page_callback' );
}

/**
 * Content for the documentation page.
 *
 * @see register_career_custom_submenu_page()
 *
 */
function career_custom_submenu_page_callback() {
	?>
	<style>
	.wrap{
		max-width: 75%;
	}
	</style>
	<div class="wrap">
		<h2>Jobvite Sync Documentation</h2>
		<p>This guide outlines how Jobvite syncs to WordPress and what to expect when viewing content. The important concept to remember is that, while WordPress stores information from Jobvite, all edits must still be made there.</p>
		<h3>Process</h3>
		<p>During synchronization, the following occurs:</p>
		<ul style="list-style-type: square; padding-left: 20px;">
			<li>Jobvite XML feed is pulled from  <a href="http://hire.jobvite.com/CompanyJobs/Xml.aspx?c=qD09Vfwr" target="_blank">this</a> source, which can be edited by the developer in 'classes/class-jobvite.php'</li>
			<li>Careers are tested to see if they exist in WordPress yet or not.</li>
			<li>Taxonomies (e.g. departments, locations) are tested to see if they exist in WordPress yet or not.</li>
			<li>If a career does not yet exist in WordPress, it is created. If it does exist, it is updated, in case the description on Jobvite is modified.</li>
			<li>If a taxonomy does not yet exist, it is created. Either way, it is attached automatically to the career. This allows for Pretty URL's like /location/atlanta-ga/</li>
			<li>Careers no longer showing up in Jobvite are hidden from public view, as it is assumed they are no longer available. You can still view them from the list, under "Removed from Jobvite".</li>
			<li>If a hidden career later appears in Jobvite again (same ID), it will republish itself and update content.</li>
		</ul>
		
		<h3>Changing Department/Status/Location Titles &amp; URL slugs</h3>
		<p>This system automatically creates department/status/location taxonomies based on information pulled from Jobvite. However, these may not always be ideal. You can edit both the title and URL slug for any category created by Jobvite, and it will use that information instead in WordPress for existing and future careers.</p>
		<p>To edit the title or URL slug for a taxonomy, look under Careers in the left navigation column for the taxonomy type you want to edit, find the taxonomy (e.g. Dallas) and modify the top two fields (Name/Slug).</p>
		
		<h3>Changing Inidividual Career URL slugs</h3>
		<p>WordPress by default creates URL slugs based on the title of a post; in this case, the career being offered. Since multiple careers may have the same title, this means some entries may have '-' followed by a number appended to the end of the URL. This is by design to avoid duplicates and confusing links.</p>
		<p>To edit the slug for an individual career, navigate to the edit screen for it and look under the title. There is a button here that will let you change the URL. Note that it still will not allow duplicate entries, so if it finds another career with the same URL, it will again add a hyphen with a number.</p>
		
		
		<h3>Redirecting Jobvite Departments/Statuses/Locations</h3>
		<p>Automatic taxonomies (e.g. departments/statuses/locations) are generated from the category/jobtype/location fields from Jobvite.</p>
		<p>At times, you may want several taxonomies from Jobvite to display in just one taxonomy on the Careers site. For example, "Sales" and "National Sales" may be better suited to group under "Sales &amp; Business Development."</p>
		<p>To achieve this, go to Careers->Options in the left navigation, and choose existing Jobvite taxonomies to redirect. It will affect all current and future careers pulled from Jobvite with that matching taxonomy.</p>
		<p>Note that you cannot delete any Jobvite categories that are being redirected, should you choose to use this feature.</p>
		
		<h3>Developer Notes</h3>
		<p>Files that power this system are 'custom-post-types/careers.php' and 'classes/class-jobvite.php'.</p>
		<p>The cron script can be found at '/cron/cron-jobvite.php'.</p>
		<p>To set up the automation of pulling data from Jobvite, you will need to create a cron job that runs <strong>/path/to</strong>/wp-content/themes/ls-careers/cron/jobvite.php at intervals you decide, no more frequently than 5 minutes. As several requests are made to the WordPress database during syncing, more frequency will impact server load.</p><p>If you need to manually sync from Jobvite, you can click <a href="<?php echo get_theme_root_uri(); ?>/ls-careers/cron/cron-jobvite.php" target="_blank">here</a>. Let the page finish loading; no text will be displayed.</p>
		
		<h3>Installation</h3>
		<ol>
			<li>If the ls-careers theme is active, first go to Settings->Permalinks to refresh new rules for the new post type/taxonomy structure.</li>
			<li>Click <a href="<?php echo get_theme_root_uri(); ?>/ls-careers/cron/cron-jobvite.php" target="_blank">here</a> and let the page finish loading. It will not display text, but it will manually trigger the first Jobvite sync, to populate WordPress with data.</li>
			<li>After the first sync, "Options" will be functional in the left nav.</li>
			<li>Configure the cron job to run at set intervals.</li>
			<li>Some taxonomies may need grouped or redirected. Go <a href="edit.php?post_type=career&amp;page=career-options-page">here</a> to manage redirection.</li>
		</ol>
		
		
	</div>
	<?php
}

/**
 * Content for the options page. Allows Jobvite taxonomies to be overridden.
 *
 * @see register_career_custom_submenu_page()
 *
 */
function career_options_page_callback() {
    // Must check that the user has the required capability 
     if (!current_user_can('manage_options'))
     {
       wp_die( __('You do not have sufficient permissions to access this page.') );
     }
     // Read in existing option value from database
     $taxonomy_map = get_option( 'jobvite_taxonomy_map' );
	 if( ! is_array($taxonomy_map) ) {
		 // First time use.
		 $taxonomy_map = array();
	 }
     // See if the user is saving changes
     // If they did, this hidden field will be set to 'Y'
     if( isset($_POST[ 'career_submit_hidden' ]) && 'Y' == $_POST[ 'career_submit_hidden' ] ) {
		 // Check for the taxonomy data. If it's there, save.
		 if( isset( $_POST['taxonomy_map'] ) && is_array( $_POST['taxonomy_map'] ) ) {
			 // Add new values but keep the old ones.
			 $taxonomy_map = array_replace( $taxonomy_map, $_POST['taxonomy_map'] );
			 update_option( 'jobvite_taxonomy_map', $taxonomy_map );
			 // Update all existing careers
			 update_career_taxonomies( $taxonomy_map );
		}
         // Put a settings updated message on the screen
 ?>
 <div class="updated"><p><strong><?php _e('Your settings have been saved.', 'menu-career' ); ?></strong></p></div>
 <?php
     }
	 // Fetch existing jobvite taxonomy slugs with active posts
	 $taxonomies = array(
		 'location',
		 'department',
		 'status'
	 );
	 $args = array(
		 'hide_empty' => false
	 );
	 $terms = get_terms( $taxonomies, $args );
	 //Option HTML data for the fields.
	 $sorted_taxonomies = array();
	 // Sort into different taxonomies for display.
	 $options = array();
	 $counter = 0;
	 foreach( $terms as $term ){
		 // Fetch Jobvite slugs for each
		 $term_meta = get_option( "taxonomy_$term->term_id" ); 
		 $jobvite_slug = '';
		 $jobvite_title = '';
		 if( isset( $term_meta['jobvite_slug'] ) ) {
			 $jobvite_slug = esc_attr( $term_meta['jobvite_slug'] );
			 $jobvite_title = esc_attr( $term_meta['jobvite_title'] );
		 }
		 // It is a Jobvite-generated taxonomy
		 $sorted_taxonomies[ $term->taxonomy ][ $counter ]['data'] = $term;
		 // Add Jobvite Data to array
		 $sorted_taxonomies[ $term->taxonomy ][ $counter ]['jobvite_slug'] = $jobvite_slug;
		 $sorted_taxonomies[ $term->taxonomy ][ $counter ]['jobvite_title'] = $jobvite_title;	
		 $counter++;
	 }
     // settings form
     ?>
 <style>
 .wrap{
	 max-width: 85%;
 }
 </style>
 <div class="wrap">
	 <h2>Jobvite Career Sync Settings</h2>
	 <form name="form1" method="post" action="edit.php?post_type=career&amp;page=career-options-page">
		 <input type="hidden" name="career_submit_hidden" value="Y">
		 <h3>Override Department Sorting</h3>
		 <p>If you want Jobvite-generated departments to go to ones you have created, choose where each should be sorted instead.<br />
			 This will change all existing and future career posts in WordPress with that Jobvite department.</p>
			 <table class="form-table">
				 <?php
				 foreach( $sorted_taxonomies['department'] as $term ){
					 // Build options dropdown
					 $output = '';
					 $jobvite_slug = $term['jobvite_slug'];
					 // Loop through each option to test if it is a match for this setting
					 foreach( $sorted_taxonomies['department'] as $option ){
						 $output .= '<option value="'.$option['data']->term_id.'"';
						 // Select what is currently set, if anything.
						 if( '' != $jobvite_slug && isset( $taxonomy_map['department'][ $jobvite_slug ] ) && $option['data']->term_id == $taxonomy_map['department'][ $jobvite_slug ] ) {
							 $output .= ' selected';
						 }
						 $output .= '>'.$option['data']->name.'</option>';
					 }
					 // Create the option if it is a currently used Jobvite taxonomy.
					 if( '' != $jobvite_slug ) {
						 //Jobvite created, not user created.
						 ?>
						 <tr>
							 <th><label for="taxonomy_map[department][<?php echo $jobvite_slug; ?>]"><?php echo $term['jobvite_title']; ?></label></th>
							 <td width="20">=></td>
							 <td>
								 <select name="taxonomy_map[department][<?php echo $jobvite_slug; ?>]" id="taxonomy_map[department][<?php echo $jobvite_slug; ?>]">
									 <option value="0">No Override</option>
									 <?php echo $output; ?>
								 </select>
							 </td>
						 </tr>
						 <?php
					 }
				 }
				 ?>
			 </table>
			 <p class="submit">
				 <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			 </p>	
			 <h3>Override Status Sorting</h3>
			 <p>If you want different Jobvite-generated statuses to go to ones you have created, choose where each should be sorted instead.<br />
				 This will change all existing and future career posts in WordPress with that Jobvite status.</p>
			 <table class="form-table">
				 <?php
				 foreach( $sorted_taxonomies['status'] as $term ){
					 // Build options dropdown
					 $output = '';
					 $jobvite_slug = $term['jobvite_slug'];
					 // Loop through each option to test if it is a match for this setting
					 foreach( $sorted_taxonomies['status'] as $option ){
						 $output .= '<option value="'.$option['data']->term_id.'"';
						 // Select what is currently set, if anything.
						 if( '' != $jobvite_slug && isset( $taxonomy_map['status'][ $jobvite_slug ] ) && $option['data']->term_id == $taxonomy_map['status'][ $jobvite_slug ] ) {
							 $output .= ' selected';
						 }
						 $output .= '>'.$option['data']->name.'</option>';
					 }
					 // Create the option if it is a currently used Jobvite taxonomy.
					 if( '' != $jobvite_slug ) {
						 //Jobvite created, not user created.
						 ?>
						 <tr>
							 <th><label for="taxonomy_map[status][<?php echo $jobvite_slug; ?>]"><?php echo $term['jobvite_title']; ?></label></th>
							 <td width="20">=></td>
							 <td>
								 <select name="taxonomy_map[status][<?php echo $jobvite_slug; ?>]" id="taxonomy_map[status][<?php echo $jobvite_slug; ?>]">
									 <option value="0">No Override</option>
									 <?php echo $output; ?>
								 </select>
							 </td>
						 </tr>
						 <?php
					 }
				 }
				 ?>
			 </table>
				 <p class="submit">
					 <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
				 </p>
				 <h3>Override Location Sorting</h3>
				 <p>If you want different Jobvite-generated locations to go to ones you have created, choose where each should be sorted instead.<br />
					 This will change all existing and future career posts in WordPress with that Jobvite location.</p>
			 <table class="form-table">
				 <?php
				 foreach( $sorted_taxonomies['location'] as $term ){
					 // Build options dropdown
					 $output = '';
					 $jobvite_slug = $term['jobvite_slug'];
					 // Loop through each option to test if it is a match for this setting
					 foreach( $sorted_taxonomies['location'] as $option ){
						 $output .= '<option value="'.$option['data']->term_id.'"';
						 // Select what is currently set, if anything.
						 if( '' != $jobvite_slug && isset( $taxonomy_map['location'][ $jobvite_slug ] ) && $option['data']->term_id == $taxonomy_map['location'][ $jobvite_slug ] ) {
							 $output .= ' selected';
						 }
						 $output .= '>'.$option['data']->name.'</option>';
					 }
					 // Create the option if it is a currently used Jobvite taxonomy.
					 if( '' != $jobvite_slug ) {
						 //Jobvite created, not user created.
						 ?>
						 <tr>
							 <th><label for="taxonomy_map[location][<?php echo $jobvite_slug; ?>]"><?php echo $term['jobvite_title']; ?></label></th>
							 <td width="20">=></td>
							 <td>
								 <select name="taxonomy_map[location][<?php echo $jobvite_slug; ?>]" id="taxonomy_map[location][<?php echo $jobvite_slug; ?>]">
									 <option value="0">No Override</option>
									 <?php echo $output; ?>
								 </select>
							 </td>
						 </tr>
						 <?php
					 }
				 }
				 ?>
			 </table>
					 <p class="submit">
						 <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
					 </p>
				 </form>
			 </div>

 <?php
}
 
 /**
  * Updates taxonomies from the Options page following submission.
  *
  * @param array $taxonomy_map Jobvite slugs and the terms they are to be mapped to.
  */
function update_career_taxonomies( $taxonomy_map = array() ) {
	$taxonomy_types = array(
		'department',
		'location',
		'status'
	);
	$existing_taxonomy_slug = array();	 
	$args = array (
	  'post_type'      => 'career',
	  'posts_per_page' => -1,
	  'post_status'    => 'any'
	);
	$query = new WP_Query( $args );
	if( $query->have_posts() ) {
		while( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
			$existing_taxonomy_slug['status'] = get_post_meta( $id, 'jobvite_status_slug', true);
			$existing_taxonomy_slug['location'] = get_post_meta( $id, 'jobvite_location_slug', true);
			$existing_taxonomy_slug['department'] = get_post_meta( $id, 'jobvite_department_slug', true);
			$original_taxonomies = get_post_meta( $id, 'jobvite_taxonomies', true);
			//check each taxonomy and update if needed.
			foreach( $taxonomy_types as $taxonomy ) {
				$jobvite_slug = $existing_taxonomy_slug[ $taxonomy ];
				if( array_key_exists( $jobvite_slug, $taxonomy_map[ $taxonomy ] ) ) {
					//Change was submitted. Override or return to default?
					if( (integer) $taxonomy_map[ $taxonomy ][ $jobvite_slug ] > 0 ) {
						// This taxonomy is now being rerouted. First it must be cast as an integer.
						$new_taxonomy_term_id = (integer) $taxonomy_map[ $taxonomy ][ $jobvite_slug ];
						wp_set_object_terms( $id, $new_taxonomy_term_id, $taxonomy );	
					} else {
						// No longer overriding. Return to Jobvite defaults.
						wp_set_object_terms( $id, (integer) $original_taxonomies[ $taxonomy ][0], $taxonomy );
					}
				}		
			}
		}
	}	 	 
}
 
