<?php
/**
 * Class to Sync Jobvite with WordPress Careers Post Type
 *
 * This class performs the following tasks:
 *
 * - Parses Jobvite Data to make it WordPress friendly.
 * - Checks for the existence of career entries and taxonomies already in WordPress.
 * - Creates new entries for career entries and/or taxonomies as needed.
 * - Hides careers in WordPress that are no longer in Jobvite from public display. Post status 'Removed from Jobvite.'
 *
 * Instructions: Create a cronjob (crontab -e, or through your webhosting panel) that runs this script every 15 minutes,
 * or as server load allows, linking to the script in /path/to/wp-content/themes/ls-careers/cron/cron-jobvite.php
 * 
 * @package LivingSocial_Careers
 */
//  Example: */15 * * * * /path/to/wp-content/themes/ls-careers/cron/cron-jobvite.php

class Jobvite_Career_Sync {
	
	/**
	 * @var string $xml_source Source for Jobvite Feed.
	 */
	protected $xml_source = 'http://hire.jobvite.com/CompanyJobs/Xml.aspx?c=qD09Vfwr';

	/**
	 * @var array $jobvite_term_meta Existing taxonomies for Jobvite careers in WordPress.
	 */	
	protected $jobvite_term_meta = array();

	/**
	 * @var array $new_taxonomies Taxonomies created during execution. Prevents duplicate entries.
	 */	
	protected $new_taxonomies = array();
	
	/**
	 * @var array $taxonomy_overrides Taxonomies that are overridden in the WP Options page for careers.
	 */	
	protected $taxonomy_overrides = array();
	
	/**
	 * Begins the collection and parsing of Jobvite data.
	 */
	function __construct ( ) {
		if( ! extension_loaded( 'simplexml' ) ) {
			exit();
		} else {
			try {
				$wp_load = '../../../../wp-load.php';
				if( ! file_exists( $wp_load ) ) {
					throw new Exception ( 'Needed WP functions failed to load.' );
				} else {
					require_once( $wp_load );
				}
			} catch (Exception $e) {
				echo $e->getMessage();
				exit();
			}
			$xml = $this->load_xml( $this->xml_source );
			$this->jobvite_term_meta = $this->fetch_terms();
			$this->taxonomy_overrides = get_option( 'jobvite_taxonomy_map' );
			if( ! array( $this->taxonomy_overrides ) ) {
				$this->taxonomy_overrides = array();
				// First time code.
				$this->taxonomy_overrides['location'] = array();
				$this->taxonomy_overrides['status'] = array();
				$this->taxonomy_overrides['department'] = array();
			}
			$this->process_jobvite( $xml );
		}
	}
	
	/**
	 * Loads XML from Jobvite, exiting if it is unavailable or fails to load.
	 *
	 * @param string $source URL for the Jobvite feed
	 * @return SimpleXMLElement PHP version of the feed.
	 */
	protected function load_xml ( $source ) {
		try {
			$xml = simplexml_load_file( $source );
		} catch (Exception $e) {
			exit();
		}
		if( ! $xml ){
			exit();
		}
		return $xml;
	}
	
	/**
	 * Collects existing taxonomy terms for location, department, and status.
	 *
	 * @return array terms associated with Career post type, by taxonomy type.
	 */
	protected static function fetch_terms ( ) {
		$wp_terms = array();
		$terms = get_terms( array( 'location', 'department', 'status' ), array( 'hide_empty' => false ) );
		// Establish arrays. Not all terms have a jobvite_slug.
		$wp_terms['location']['jobvite_slug'] = array();
		$wp_terms['department']['jobvite_slug'] = array();
		$wp_terms['status']['jobvite_slug'] = array();
		foreach ( $terms as $term ) {
			$term_custom = get_option( "taxonomy_$term->term_id" );
			if( isset( $term_custom['jobvite_slug'] ) ) {
				$wp_terms[ $term->taxonomy ]['jobvite_slug'][ $term->term_id ] = $term_custom['jobvite_slug'];
			}
			$wp_terms[ $term->taxonomy ]['wp_slug'][ $term->term_id ] = $term->slug;
		}
		return $wp_terms;
	}
	
	/**
	 * Main function. Formats Jobvite data for WordPress entry.
	 *
	 * @param SimpleXMLElement $xml PHP version of the Jobvite feed.
	 */
	protected function process_jobvite( SimpleXMLElement $xml ) {
		/* Initialize Jobvite ID Array */
		$current_jobvite_ids = array();
		/* Initialize Formatted Career Data Array */
		$career_data = array();
		/* Loop Jobs */
		foreach($xml->job as $job){
			/* Cast ID as string */
			$id = (string) $job->id;
			/* Add ID to Jobvite ID Array */
			$current_jobvite_ids[] = $id;
			/* Add Data to Formatted Career Data Array */
			$career_data[ $id ]['id'] = $id;
			$career_data[ $id ]['date'] = strtotime($job->date);
			$career_data[ $id ]['title'] = (string) $job->title;
			$career_data[ $id ]['description'] = $this->format_description( (string) $job->description );
			$career_data[ $id ]['requisitionid'] =  (integer) $job->requisitionid;
			$career_data[ $id ]['detail-url'] = (string) $job->{ 'detail-url' };
			$career_data[ $id ]['apply-url'] = (string) $job->{ 'apply-url' };
			$career_data[ $id ]['location']['title'] = $this->format_location( $job->location, $job->region, false);
			$career_data[ $id ]['location']['slug'] = $this->format_location( $job->location, $job->region);
			$career_data[ $id ]['status']['title'] = (string) $job->jobtype;
			$career_data[ $id ]['status']['slug'] = $this->format_taxonomy_slug( $job->jobtype );
			$career_data[ $id ]['department']['title'] = (string) $job->category;
			$career_data[ $id ]['department']['slug'] = $this->format_taxonomy_slug( $job->category );
		}
		$published_wp_careers = $this->career_post_statuses( $current_jobvite_ids );
		foreach( $career_data as $jobvite_id => $data ) {
			$career_data[ $jobvite_id ]['job_status'] = 'new';
			// Is this new or existing in WordPress
			if( array_key_exists( $jobvite_id, $published_wp_careers ) ) {
				$career_data[ $jobvite_id ]['job_status'] = $published_wp_careers[ $jobvite_id ];
			}
			// Add/Update Career in WordPress
			$this->process_career_entry( $career_data[ $jobvite_id ] );
		}
		// Archive careers no longer provided by Jobvite (assumed not currently available)
		if( ! empty( $current_jobvite_ids ) ) {
			$this->archive_inactive_careers( $current_jobvite_ids );
		}
	}
	
	/**
	 * Changes career posts in WordPress that are no longer listed on Jobvite to 'Removed from Jobvite' status,
	 * and hides them from frontend display.
	 *
	 * @param array $current_jobvite_ids Jobvite IDs received in the current XML feed.
	 */
	protected function archive_inactive_careers ( $current_jobvite_ids = array() ) {
		$args = array (
		  'meta_key'       => 'jobvite_id',
		  'meta_query'     =>  array (
			  'key'     => 'jobvite_id',
			  'value'   => $current_jobvite_ids,
			  'compare' => 'NOT IN'
		  ),
		  'post_type'      => 'career',
		  'posts_per_page' => -1
		);
		$query = new WP_Query( $args );
		if( $query->have_posts() ) {
			while( $query->have_posts() ) {
				$query->the_post();
				// Create post object
				$wp_id = get_the_ID();
				$archived_career = array(
				  'ID'			  => $wp_id,
				  'post_status'	  => 'removed_from_jobvite'
				);
				// Insert the post into the database
				wp_update_post( $archived_career );
			}
		}	
	}
	
	/**
	 * Removes 'United States' from location for US-based career offers. 
	 * Returns either text without 'United States' or a URL slug for WordPress entry.
	 * 
	 * @param string $location Location to be converted.
	 * @param string $region Region for the job.
	 * @param boolean $return_slug Whether to return just text without 'United States' or a URL slug.
	 * @return string Either text without 'United States' or a URL slug (e.g. boulder-co).
	 */	
	protected function format_location ( $location = '', $region = '', $return_slug = true ) {
		$slug = $location;
		/* Strip Country from City Title, but only if US-based */
		if( 'USA' == $region ) {
			$slug = str_ireplace( 'USA', '', $slug );
			$slug = str_ireplace( 'United States', '', $slug );
		}
		$slug = rtrim( $slug, ', ' );
		$slug = str_replace( ' ,', ', ', $slug );
		if( $return_slug ) {
			return $this->format_taxonomy_slug( $slug );
		}
		return $slug;
	}
	
	/**
	 * Filter for description from Jobvite. Sometimes DIV tags are present instead of paragraphs.
	 * 
	 * @param string $content Content to be formatted.
	 * @return string Formatted content.
	 */	
	protected function format_description( $content = '' ) {
		$originals = array(
			'<div>',
			'</div>'
		);
		$replacements = array(
			'<p>',
			'</p>'
		);
		$content = str_replace( $originals, $replacements, $content );
		return $content;
	}
	
		
	/**
	 * Converts string to WordPress-friendly URL slug.
	 *
	 * @param string $jobvite_taxonomy String to be converted.
	 * @return string URL slug.
	 */
	protected function format_taxonomy_slug ( $jobvite_taxonomy = '' ) {
		$slug = strtolower( str_replace ( array( ',', ' ' ), '-', $jobvite_taxonomy ) );
		$slug = preg_replace( '/-{2,}/', '-', $slug );
		/* Remove Trailing Dash if Present */
		return rtrim( $slug, "-" );
	}
	
	/**
	 * Finds all current Jobvite listings that are already WordPress entries.
	 *
	 * @param array $jobvite_job_ids A collection of existing Jobvite IDs from the XML feed.
	 * @return array Collection of jobvite listing IDs with existing entries in WordPress. Returns WordPress ID as key.
	 */
	protected function career_post_statuses ( $jobvite_job_ids = array() ) {
		$careers = array();
		$args = array (
		  'meta_key'       => 'jobvite_id',
		  'meta_query'     =>  array (
			  'key'      => 'jobvite_id',
			  'value'    => $jobvite_job_ids,
			  'compare'  => 'IN'
		  ),
		  'post_type'      => 'career',
		  'posts_per_page' => -1,
		  'post_status'    => 'any'
		);
		$query = new WP_Query( $args );
		if( $query->have_posts() ) {
			while( $query->have_posts() ) {
				$query->the_post();
				$wp_id = get_the_ID();
				$jobvite_id = get_post_meta( $wp_id, 'jobvite_id', true );
				$careers[ $jobvite_id ] = $wp_id;
			}
		}
		return $careers;
	}
	
	/**
	 * Either creates a new entry for a Jobvite listing, or updates an existing entry's data.
	 *
	 * @param array $career_data Formatted jobvite data ready for WordPress entry.
	 */
	protected function process_career_entry ( $career_data = array() ) {
		$taxonomy_ids = $this->process_career_taxonomies( $career_data );
		$publish_date = date( 'Y-m-d H:i:s', $career_data['date'] );
		if( 'new' == $career_data['job_status'] ){	
			// Career is new
			$new_career = array(
			  'post_title'    => $career_data['title'],
			  'post_type'	  => 'career',
			  'post_content'  => $career_data['description'],
			  'post_status'   => 'publish',
			  'post_author'   => 1,
			  'post_date'	  => $publish_date
			);
			// Add Career to WordPress
			$wp_id = wp_insert_post( $new_career, true );
			//Check for overrides
			
			foreach($taxonomy_ids as $taxonomy => $taxonomy_id){
				$jobvite_taxonomy_slug = '';
				if( ! empty( $this->taxonomy_overrides[ $taxonomy ] ) && array_key_exists( $career_data[ $taxonomy ]['slug'], $this->taxonomy_overrides[ $taxonomy ] ) ) {
					$jobvite_taxonomy_slug = $career_data[ $taxonomy ]['slug'];
					if( $this->taxonomy_overrides[ $taxonomy ][ $jobvite_taxonomy_slug ] > 0) {
						//There is an override in place
						$taxonomy_id = array( $this->taxonomy_overrides[ $taxonomy ][ $jobvite_taxonomy_slug ] );
					}
				}	
				$taxonomy_id = array_map( 'intval', $taxonomy_id );
				$taxonomy_id = array_unique( $taxonomy_id );
				wp_set_object_terms( $wp_id, $taxonomy_id, $taxonomy );
			}
			add_post_meta( $wp_id, 'jobvite_id', $career_data['id'] );
			add_post_meta( $wp_id, 'apply_now_link', $career_data['apply-url'] );
			add_post_meta( $wp_id, 'detail_link', $career_data['detail-url'] );
			add_post_meta( $wp_id, 'requisition_id', $career_data['requisitionid'] );
			add_post_meta( $wp_id, 'jobvite_status', $career_data['status']['title'] );
			add_post_meta( $wp_id, 'jobvite_status_slug', $career_data['status']['slug'] );
			add_post_meta( $wp_id, 'jobvite_department', $career_data['department']['title'] );
			add_post_meta( $wp_id, 'jobvite_department_slug', $career_data['department']['slug'] );
			add_post_meta( $wp_id, 'jobvite_location', $career_data['location']['title'] );
			add_post_meta( $wp_id, 'jobvite_location_slug', $career_data['location']['slug'] );
			add_post_meta( $wp_id, 'jobvite_taxonomies', $taxonomy_ids );
		} else {
			// Career is an update
			$wp_id = $career_data['job_status'];
			$updated_career = array(
			  'ID'			  => $wp_id,
			  'post_title'    => $career_data['title'],
			  'post_content'  => $career_data['description'],
			  'post_status'	  => 'publish',
			  'post_date'	  => $publish_date
			);
			// Update Career
			wp_update_post( $updated_career );
			foreach( $taxonomy_ids as $taxonomy => $taxonomy_id ) {
				$jobvite_taxonomy_slug = '';
				if( ! empty( $this->taxonomy_overrides[ $taxonomy ] ) && array_key_exists( $career_data[ $taxonomy ]['slug'], $this->taxonomy_overrides[ $taxonomy ] ) ) {
					$jobvite_taxonomy_slug = $career_data[ $taxonomy ]['slug'];
					if( $this->taxonomy_overrides[ $taxonomy ][ $jobvite_taxonomy_slug ] > 0) {
						//There is an override in place
						$taxonomy_id = array( $this->taxonomy_overrides[ $taxonomy ][ $jobvite_taxonomy_slug ] );
					}
				}	
				$taxonomy_id = array_map( 'intval', $taxonomy_id );
				$taxonomy_id = array_unique( $taxonomy_id );
				//true here means categories will be appended, not replaced.
				wp_set_object_terms( $wp_id, $taxonomy_id, $taxonomy, true );
			}
			// Jobvite ID is constant so is not updated
			update_post_meta( $wp_id, 'apply_now_link', $career_data['apply-url'] );
			update_post_meta( $wp_id, 'detail_link', $career_data['detail-url'] );
			update_post_meta( $wp_id, 'requisition_id', $career_data['requisitionid'] );
			update_post_meta( $wp_id, 'jobvite_status', $career_data['status']['title'] );
			update_post_meta( $wp_id, 'jobvite_status_slug', $career_data['status']['slug'] );
			update_post_meta( $wp_id, 'jobvite_department', $career_data['department']['title'] );
			update_post_meta( $wp_id, 'jobvite_department_slug', $career_data['department']['slug'] );
			update_post_meta( $wp_id, 'jobvite_location', $career_data['location']['title'] );
			update_post_meta( $wp_id, 'jobvite_location_slug', $career_data['location']['slug'] );
			update_post_meta( $wp_id, 'jobvite_taxonomies', $taxonomy_ids );
		}
	}
	
	/**
	 * Creates new taxonomy terms (e.g. boulder-co) for careers if location/department/status does not yet exist in WordPress.
	 * Returns the WordPress IDs for these taxonomies so they are used to connect the appropriate career post.
	 * 
	 * @param array $career_data Formatted Jobvite data ready for WordPress entry.
	 * @return array WordPress IDs for taxonomies related to the current career listing.
	 */
	protected function process_career_taxonomies ( $career_data = array() ) {
		$taxonomy_ids = array();
		$terms['department'] = $this->taxonomy_status( $career_data['department'], 'department' );
		$terms['location'] = $this->taxonomy_status( $career_data['location'], 'location' );
		$terms['status'] = $this->taxonomy_status( $career_data['status'], 'status' );
		foreach( $terms as $taxonomy => $term_status ) {
			if ( 'new' == $term_status && ! in_array( $career_data[ $taxonomy ]['slug'], $this->new_taxonomies ) ) {
				/* New Taxonomy */
				$insert = wp_insert_term(
				  $career_data[ $taxonomy ]['title'],
				  $taxonomy,
				  array(
				    'slug' => $career_data[ $taxonomy ]['slug']
				  )
				);
				$new_id = $insert['term_id'];
				// Adds custom fields to taxonomies.
				$term_meta = array();
				$term_meta['jobvite_slug'] = $career_data[ $taxonomy ]['slug'];
				$term_meta['jobvite_title'] = $career_data[ $taxonomy ]['title'];
				update_option( "taxonomy_".$new_id, $term_meta );
				// Adds to taxonomy class property to prevent duplicates
				$this->new_taxonomies[] = $career_data[ $taxonomy ]['slug'];
				$this->jobvite_term_meta[ $taxonomy ]['jobvite_slug'][ $new_id ] = $career_data[ $taxonomy ]['slug'];
				$this->jobvite_term_meta[ $taxonomy ]['wp_slug'][ $new_id ] = $career_data[ $taxonomy ]['slug'];
				$taxonomy_ids[ $taxonomy ][] = $new_id;
			} else {
				// Existing Taxonomy
				$taxonomy_ids[ $taxonomy ][] = $term_status;
			}
		}
		return $taxonomy_ids;
	}
	
	/**
	 * Determines if taxonomy exists in WordPress. 
	 * If it was created but not by Jobvite, and the slugs match, it will sync.
	 *
	 * @param array $jobvite_taxonomy Title and slug for the taxonomy to be checked.
	 * @param string $wp_taxonomy Which taxonomy (location/department/status) to check.
	 * @return string Either 'new' for a new entry, or the WordPress taxonomy ID for an existing entry.
	 */
	protected function taxonomy_status ( $jobvite_taxonomy = array(), $wp_taxonomy = '' ) {
		$term_check = array_search( $jobvite_taxonomy['slug'], $this->jobvite_term_meta[ $wp_taxonomy ]['jobvite_slug'] );
		if( false === $term_check ) {
			//Not Created Yet by Jobvite
			$term_check = array_search( $jobvite_taxonomy['slug'], $this->jobvite_term_meta[ $wp_taxonomy ]['wp_slug'] );
			if( false === $term_check ) {
				return 'new';
			} else {
				//Existed in WP before added by Jobvite. Update Jobvite meta in WP to match.
				$term_meta = get_option( "taxonomy_$term_check" );
				$term_meta['jobvite_slug'] = $jobvite_taxonomy['slug'];
				$term_meta['jobvite_title'] = $jobvite_taxonomy['title'];
				update_option( "taxonomy_$term_check", $term_meta );
			}
		}
		return $term_check;
	}	
}
