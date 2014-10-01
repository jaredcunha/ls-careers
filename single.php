<?php
/**
 * The Template for displaying all single posts
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<main role="main" class="module module__two-cols">
	<div class="wrap">
		<div class="primary-col">
			<article>
				<div class="page-module-header pad-horiz">
					<h1><?php the_title(); ?></h1>
					<div class="supplement">
						<?php
							$terms = get_the_terms($post->ID, 'status');
							echo '';
							foreach ($terms as $taxindex => $taxitem) {
							echo '<p class="status status-'. $taxitem->slug .'">' . $taxitem->name . '</p>';
							}
							echo ''
						?>
						<p class="job-location">
							<?php
								$terms = get_the_terms($post->ID, 'location');
								echo '';
								foreach ($terms as $taxindex => $taxitem) {
								echo $taxitem->name;
								}
								echo ''
							?>
						</p>
					</div>
				</div>
				<div class="post-content pad-horiz">	
					<?php the_content(); ?>			
					<?php 
					    $url = get_post_meta($post->ID, 'Apply Now Link', true); 

						if ($url) {
						    echo "<div class='apply-now'><p><a href='$url' class='btn'>Apply Now</a></p></div>";
						}
					?>
					 
				</div>
		


			</article>
		</div>

	</div>
</main>
<?php endwhile; ?>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>