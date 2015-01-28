<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts() 
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>


<main role="main" class="module module__two-cols">
	<div class="wrap">
		<div class="primary-col">
			<div class="page-module-header pad-horiz">
				<h1><?php echo single_cat_title(); ?></h1>
				<div class="supplement">
					<p class="secondary">
						<?php printf( __( '%d %s' ), $wp_query->found_posts, _n( 'job opening', 'job openings', $wp_query->found_posts), get_search_query() ); ?>
					</p>
				</div>
			</div>
			

			<div class="pad-horiz post-content">
				<?php 
					//Get the correct taxonomy ID by slug
					$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
					//Get Taxonomy Meta
					$saved_data = get_tax_meta($term->term_id,'display_image');
					$metaboxtext = esc_attr( get_tax_meta($term->term_id,'display_image', true ) );
					 if (strlen($metaboxtext)>0){ 
					echo '<img class="feature-image" src="'.$saved_data['src'].'" />'; 
					} else {
					echo ''; 
					}
				?>

				<?php echo category_description(); ?>

				<?php 
					//Get the correct taxonomy ID by slug
					$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
					//Get Taxonomy Meta
					$saved_data = get_tax_meta($term->term_id,'extra_links_header');
					$metaboxtext = esc_attr( get_tax_meta($term->term_id,'extra_links_header', true ) );
					if (strlen($metaboxtext)>0){ 
						echo '<h2>' . $saved_data . '</h2>'; 
					} else {
						echo ''; 
					}
				?>

				<?php 
					//Get the correct taxonomy ID by slug
					$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
					//Get Taxonomy Meta
					$saved_data = get_tax_meta($term->term_id,'section_links');
					$metaboxtext = esc_attr( get_tax_meta($term->term_id,'section_links', true ) );
					if (strlen($metaboxtext)>0){ 
						echo $saved_data; 
					} else {
						echo ''; 
					}
				?>

			</div>
			
			
			<div class="jobs-list">
				<div class="page-module-header pad-horiz">
					<h2>careers in <?php echo single_cat_title(); ?></h2>
				</div>
				<?php if ( have_posts() ): ?>
					<?php $posts = query_posts($query_string .'&orderby=title&order=asc&posts_per_page=-1'); ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<article class="job-listing" data-job-location="<?php
										$terms = get_the_terms($post->ID, 'location');
										echo '';
										foreach ($terms as $taxindex => $taxitem) {
										echo $taxitem->name;
										}
										echo ''
									?>">
							<a href="<?php esc_url( the_permalink() ); ?>" class="job-listing-link pad-horiz">
								<div class="title-and-department">
									<h3 class="listing-title"><?php echo the_title(); ?></h3>
									<p class="listing-department"><?php echo single_cat_title(); ?></p>
								</div>
								<div class="job-location">
									<?php
										$terms = get_the_terms($post->ID, 'location');
										echo '';
										foreach ($terms as $taxindex => $taxitem) {
										echo '<p>' . $taxitem->name . '</p>';
										}
										echo ''
									?>
								</div>
								<div class="additional-job-info">
									<?php
										$terms = get_the_terms($post->ID, 'status');
										echo '';
										foreach ($terms as $taxindex => $taxitem) {
										echo '<p class="status status-'. $taxitem->slug .'">' . $taxitem->name . '</p>';
										}
										echo ''
									?>
									<?php
									//display message if post is less than 46 days old
									$mylimit=14 * 86400; //days * seconds per day
									//$post_age = date('U') - get_post_time('U');
									$post_age = date('U') - mysql2date('U', $post->post_date_gmt);
									if ($post_age < $mylimit) {
									echo '<p class="new">NEW</p>';
									}
								?>
								</div>
								
							</a>		
						</article>
					<?php endwhile; ?>

					<?php else: ?>
						<p class="no-content">no current openings</p>	
					<?php endif; ?>
				</div>
		</div>
		<div class="secondary-col">
			<div class="sidebar-module">
				<h2>departments</h2>
				<?php $terms = apply_filters( 'taxonomy-images-get-terms', '', array(
				    'taxonomy' => 'department',
					'having_images' => false,
				    'term_args' => array(
						'hide_empty' => 1, ## change to 0 to show empty categories 
						),
				    ) );
					if ( ! empty( $terms ) ) {
				    	print '<ul class="link-list">';
				    	foreach( (array) $terms as $term ) {
				        	print '<li><a href="' . esc_url( get_term_link( $term, $term->taxonomy ) ) . '">' . esc_html( $term->name ) . '</a></li>';
				    	}
				    	print '</ul>';
					}
				?>
			</div>
		</div>
	</div>
</main>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>