<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
/**
 * Cron Script to Sync Jobvite with WordPress Careers Post Type
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * Instructions: Create a cronjob (crontab -e, or through your webhosting panel) that runs this script every 15 minutes.
 */
//  Example: */15 * * * * /path/to/wp-content/themes/ls-careers/cron/jobvite.php

class Jobvite_Career_Sync {
	
	protected $xml_source = 'http://hire.jobvite.com/CompanyJobs/Xml.aspx?c=qD09Vfwr';
	protected $jobvite_term_meta = array();
	protected $new_taxonomies = array();
	
	function __construct ( ) {
		if( ! extension_loaded( 'simplexml' ) ) {
			exit();
		} else {
			try {
				$wp_load = '../../../../wp-load.php';
				if( ! file_exists( $wp_load ) ) {
					throw new Exception ('Needed WP functions failed to load.');
				} else {
					require_once( $wp_load );
				}
			} catch (Exception $e) {
				echo $e->getMessage();
				exit();
			}
			$xml = $this->load_xml( $this->xml_source );
			$this->jobvite_term_meta = $this->fetch_terms();
			$this->process_jobvite( $xml );
		}
	}
	
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
	
	protected function fetch_terms ( ) {
		$wp_terms = array();
		$terms = get_terms( array('location', 'department', 'status'), array( 'hide_empty' => false ) );
		foreach ( $terms as $term ) {
			$term_custom = get_option("taxonomy_$term->term_id");
			$wp_terms[$term->taxonomy]['jobvite_slug'][$term->term_id] = $term_custom['jobvite_slug'];
			$wp_terms[$term->taxonomy]['wp_slug'][$term->term_id] = $term->slug;
		}
		return $wp_terms;
	}
	
	protected function process_jobvite( SimpleXMLElement $xml ) {
		/* Initialize Jobvite ID Array */
		$current_jobvite_ids = array();
		/* Initialize Formatted Job Data Array */
		$job_data = array();
		/* Loop Jobs */
		foreach($xml->job as $job){
			/* Cast ID as string */
			$id = (string) $job->id;
			/* Add ID to Jobvite ID Array */
			$current_jobvite_ids[] = $id;
			/* Add Data to Formatted Job Data Array */
			$job_data[$id]['id'] = $id;
			$job_data[$id]['date'] = strtotime($job->date);
			$job_data[$id]['title'] = (string) $job->title;
			$job_data[$id]['description'] = (string) $job->description;
			$job_data[$id]['requisitionid'] =  (integer) $job->requisitionid;
			$job_data[$id]['detail-url'] = (string) $job->{ 'detail-url' };
			$job_data[$id]['apply-url'] = (string) $job->{ 'apply-url' };
			$job_data[$id]['location']['title'] = $this->format_location( $job->location, $job->region, false);
			$job_data[$id]['location']['slug'] = $this->format_location( $job->location, $job->region);
			$job_data[$id]['status']['title'] = (string) $job->jobtype;
			$job_data[$id]['status']['slug'] = $this->format_taxonomy_slug( $job->jobtype );
			$job_data[$id]['department']['title'] = (string) $job->category;
			$job_data[$id]['department']['slug'] = $this->format_taxonomy_slug( $job->category );
		}
		$published_wp_careers = $this->career_post_statuses( $current_jobvite_ids );
		foreach( $job_data as $jobvite_id => $data ) {
			$job_data[$jobvite_id]['job_status'] = 'new';
			/* Is this new or existing in WordPress */
			if( array_key_exists( $jobvite_id, $published_wp_careers ) ) {
				$job_data[$jobvite_id]['job_status'] = $published_wp_careers[$jobvite_id];
			}
			/* Add/Update Career in WordPress */
			$this->process_career_entry( $job_data[$jobvite_id] );
		}
		/* Archive careers no longer provided by JobVite (assumed not currently available) */
		if( ! empty( $current_jobvite_ids ) ) {
			/*For Testing*/
			unset($current_jobvite_ids[0]);
			$this->archive_inactive_careers( $current_jobvite_ids );
		}
	}
	
	protected function archive_inactive_careers ( $current_jobvite_ids = array() ) {
		$args = array (
		  'meta_key'   => 'jobvite_id',
		  'meta_query' =>  array (
			  'key'    => 'jobvite_id',
			  'value'  => $current_jobvite_ids,
			  'compare'=> 'NOT IN'
		  ),
		  'post_type' => 'career',
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
	
	
	protected function format_location ( $location = '', $filter = '',$return_slug = true) {
		$slug = $location;
		/* Strip Country from City Title, but only if US-based */
		if( $filter == 'USA' ) {
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
	
	protected function format_taxonomy_slug ( $jobvite_taxonomy = '' ) {
		$slug = strtolower( str_replace ( array(',', ' '), '-', $jobvite_taxonomy ) );
		$slug = preg_replace('/-{2,}/', '-', $slug);
		/* Remove Trailing Dash if Present */
		return rtrim($slug, "-");
	}
	
	protected function career_post_statuses ( $jobvite_job_ids = array() ) {
		$careers = array();
		$args = array (
		  'meta_key'   => 'jobvite_id',
		  'meta_query' =>  array (
			  'key'    => 'jobvite_id',
			  'value'  => $jobvite_job_ids,
			  'compare'=> 'IN'
		  ),
		  'post_type' => 'career',
		  'posts_per_page' => -1,
		  'post_status' => 'any'
		);
		
		$query = new WP_Query( $args );
		if( $query->have_posts() ) {
			while( $query->have_posts() ) {
				$query->the_post();
				$wp_id = get_the_ID();
				$jobvite_id = get_post_meta( $wp_id, 'jobvite_id', true );
				$careers[$jobvite_id] = $wp_id;
			}
		}
		return $careers;
	}
	
	protected function process_career_entry ( $job_data = array() ) {
		$taxonomy_ids = $this->process_career_taxonomies( $job_data );
		$publish_date = date( 'Y-m-d H:i:s', $job_data['date'] );
		if( $job_data['job_status'] == 'new' ){	
			// Create post object
			$new_career = array(
			  'post_title'    => $job_data['title'],
			  'post_type'	  => 'career',
			  'post_content'  => $job_data['description'],
			  'post_status'   => 'publish',
			  'post_author'   => 1,
			  'post_date'	  => $publish_date
			);
			// Insert the post into the database
			$wp_id = wp_insert_post( $new_career, true );
			foreach($taxonomy_ids as $taxonomy => $taxonomy_id){
				$taxonomy_id = array_map( 'intval', $taxonomy_id );
				$taxonomy_id = array_unique( $taxonomy_id );
				wp_set_object_terms( $wp_id, $taxonomy_id, $taxonomy );
			}
			add_post_meta( $wp_id, 'jobvite_id', $job_data['id'] );
			add_post_meta( $wp_id, 'Apply Now Link', $job_data['apply-url'] );
			add_post_meta( $wp_id, 'Detail Link', $job_data['detail-url'] );
			add_post_meta( $wp_id, 'Requisition ID', $job_data['requisitionid'] );	
		} else {
			/* Update Post */
			$wp_id = $job_data['job_status'];
			// Create post object
			$updated_career = array(
			  'ID'			  => $wp_id,
			  'post_title'    => $job_data['title'],
			  'post_content'  => $job_data['description'],
			  'post_status'	  => 'publish'
			);
			// Insert the post into the database
			wp_update_post( $updated_career );
			foreach($taxonomy_ids as $taxonomy => $taxonomy_id){
				$taxonomy_id = array_map( 'intval', $taxonomy_id );
				$taxonomy_id = array_unique( $taxonomy_id );
				wp_set_object_terms( $post_id, $taxonomy_id, $taxonomy, true );
			}
			update_post_meta( $wp_id, 'Apply Now Link', $job_data['apply-url'] );
			update_post_meta( $wp_id, 'Detail Link', $job_data['detail-url'] );
			update_post_meta( $wp_id, 'Requisition ID', $job_data['requisitionid'] );
		}
	}
	
	protected function process_career_taxonomies ( $job_data = array() ) {
		$taxonomy_ids = array();
		$terms['department'] = $this->taxonomy_status( $job_data['department'], 'department' );
		$terms['location'] = $this->taxonomy_status( $job_data['location'], 'location' );
		$terms['status'] = $this->taxonomy_status( $job_data['status'], 'status' );
		foreach( $terms as $k => $term_status ) {
			if ( $term_status == 'new' && ! in_array( $job_data[$k]['slug'], $this->new_taxonomies ) ) {
				
				/* New Taxonomy */
				$insert = wp_insert_term(
				  $job_data[$k]['title'], // the term 
				  $k, // the taxonomy
				  array(
				    'slug' => $job_data[$k]['slug']
				  )
				);
				$this->new_taxonomies[] = $job_data[$k]['slug'];
				$taxonomy_ids[$k][] = $insert['term_id'];
			} else {
				/* Existing Taxonomy */
				$taxonomy_ids[$k][] = $term_status;
			}
		}
		return $taxonomy_ids;
	}
		
	/* Determines if taxonomy yet exists. Based on Jobvite-parsed taxonomy, 
	   not what WordPress may be using as the pretty URL slug */
	protected function taxonomy_status ( $jobvite_taxonomy = array(), $wp_taxonomy = '' ) {
		$term_check = array_search( $jobvite_taxonomy['slug'], $this->jobvite_term_meta[ $wp_taxonomy ][ 'jobvite_slug' ] );
		if( ! is_numeric( $term_check ) ) {
			/* Not Created Yet by Jobvite */
			$term_check = array_search( $jobvite_taxonomy['slug'], $this->jobvite_term_meta[ $wp_taxonomy ][ 'wp_slug' ] );
			if( ! is_numeric( $term_check ) ) {
				return 'new';
			} else {
				/* Existed in WordPress before added by Jobvite. Update Jobvite meta in WP to match. */
				$term_meta = get_option("taxonomy_$term_check");
				$term_meta['jobvite_slug'] = $jobvite_taxonomy['slug'];
				update_option("taxonomy_$term_check",$term_meta);
			}
		}
		return $term_check;
	}	
}

$run = new Jobvite_Career_Sync();
