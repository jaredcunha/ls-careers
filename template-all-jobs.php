<?php
/**
 *
 * Template Name: All Jobs
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>
<main role="main" class="module module__two-cols">
	<div class="wrap">
		<div class="primary-col">
			<div class="jobs-list">
				<div class="page-module-header pad-horiz">
					<h2>all jobs</h2>
				</div>
				<?php
				// The Query
				$args = array( 
					'post_type' => 'career', 
					'posts_per_page' => -1 ); ?>

				<?php $the_query = new WP_Query( $args )  ?>

					<?php if ( $the_query->have_posts() ):  ?>
						
						<?php while ($the_query->have_posts()): ?>
							<?php $the_query->the_post(); ?>
							<article class="job-listing">
								<a href="<?php esc_url( the_permalink() ); ?>" class="job-listing-link pad-horiz">
									<div class="title-and-department">
										<h3 class="listing-title"><?php echo the_title(); ?></h3>
										<p class="listing-department">
											<?php
												$terms = get_the_terms($post->ID, 'department');
												echo '';
												foreach ($terms as $taxindex => $taxitem) {
												echo $taxitem->name;
												}
												echo ''
											?>
										</p>
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
						<p>hey</p>
					<?php endif; ?>


				<?php wp_reset_postdata(); ?>
			</div>
		</div>
		<div class="secondary-col">
			<div class="sidebar-module">
				<h2>openings</h2>
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

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer') ); ?>