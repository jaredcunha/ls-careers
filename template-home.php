<?php
/**
 *
 * Template Name: Home Page 
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

?>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>
	<div class="hero">
		<div class="wrap">
			<h1 class="site-title"><?php the_block('Site Header',array('type' => 'one-liner','apply_filters' => false)) ?></h1>
			<p class="site-description"><?php the_block('Site Descriptive Text',array('type' => 'one-liner','apply_filters' => false)) ?></p>
		</div>
	</div>
	<form method="get" action="<?php esc_url( home_url( '/' ) ); ?>" id="findJobs" class="jobs-search">
		<div class="search-wrap">       
		   	<span class="select-wrap"><?php custom_taxonomy_dropdown( 'department' ); ?></span>
		   	<span class="select-wrap"><?php custom_taxonomy_dropdown( 'location' ); ?></span>
		   	<button type="submit"><span class="text">go</span><span class="icon search"></span></button>
	   	</div>
	</form>
</header>


		<section class="module module--values" id="#values">
			<div class="wrap">
				<header class="module__intro">
					<h2><?php the_block('Values Header',array('type' => 'one-liner','apply_filters' => false)) ?></h2>
					<?php the_block('Values Descriptive Text') ?>
				</header>
				<ul class="ls-vaules-list">
					<li class="live-hungry">
						<span class="icon live-hungry"></span>
						<h3><?php the_block('Live Hungry Header',array('type' => 'one-liner','apply_filters' => false)) ?></h3>
						<p><?php the_block('Live Hungry Text',array('type' => 'one-liner','apply_filters' => false)) ?></p>
					</li><li class="recognize-others">
						<span class="icon recognize-others"></span>
						<h3><?php the_block('Recognize Others Header',array('type' => 'one-liner','apply_filters' => false)) ?></h3>
						<p><?php the_block('Recognize Others Text',array('type' => 'one-liner','apply_filters' => false)) ?></p>
					</li><li class="surprise-and-delight">
						<span class="icon surprise-and-delight"></span>
						<h3><?php the_block('Surprise and Delight Header',array('type' => 'one-liner','apply_filters' => false)) ?></h3>
						<p><?php the_block('Surprise and Delight Text',array('type' => 'one-liner','apply_filters' => false)) ?></p>
					</li><li class="champion-good-ideas">
						<span class="icon champion-good-ideas"></span>
						<h3><?php the_block('Champion Good Ideas Header',array('type' => 'one-liner','apply_filters' => false)) ?></h3>
						<p><?php the_block('Champion Good Ideas Text',array('type' => 'one-liner','apply_filters' => false)) ?></p>
					</li><li class="make-strong-moves">
						<span class="icon make-strong-moves"></span>
						<h3><?php the_block('Make Strong Moves Header',array('type' => 'one-liner','apply_filters' => false)) ?></h3>
						<p><?php the_block('Make Strong Moves Text',array('type' => 'one-liner','apply_filters' => false)) ?></p>
					</li>
				</ul>
			</div>
		</section>

		<section class="module module--by-department" id="departments">
			<div class="wrap">
				<header class="module__intro">
					<h2><?php the_block('Jobs by Department Header',array('type' => 'one-liner','apply_filters' => false)) ?></h2>
				</header>
				<?php $terms = apply_filters( 'taxonomy-images-get-terms', '', array(
				    'taxonomy' => 'department',
				    'term_args' => array(
						'hide_empty' => 0, ## change to 0 to show empty categories 
						),
				    ) );
					if ( ! empty( $terms ) ) {
				    	print '<ul class="department-tiles">';
				    	foreach( (array) $terms as $term ) {
				        	print '<li class="tile"><a class="tile-link" href="' . esc_url( get_term_link( $term, $term->taxonomy ) ) . '"><div class="overlay"></div>' . wp_get_attachment_image( $term->image_id, 'full' ) . '<span class="department-name"><span class="inner">' . esc_html( $term->name ) . '</span></span></a></li>';
				    	}
				    	print '</ul>';
					}
				?>
			</div>
		</section>

		<section class="module module--by-location" id="locations">
			<div class="wrap">
				<header class="module__intro">
					<h2><?php the_block('Jobs by Location Header',array('type' => 'one-liner','apply_filters' => false)) ?></h2>
				</header>
				<div class="office-locations-images">
					<span class="location-map us-locations"><img src="/wp-content/themes/ls-careers/images/us-locations.png" alt="" /></span>
					<span class="location-map europe-locations"><img src="/wp-content/themes/ls-careers/images/europe-locations.png" alt="" /></span>
				</div>
				<div class="office-locations-list">
					<h3>Office Locations</h3>
					<?php location_list( 'location' ); ?>
				</div>
			</div>
		</section>



<section class="module module--perks-and-benefits" id="perks">
	<div class="wrap">
		<header class="module__intro">
			<h2><?php the_block('Perks and Benefits Header',array('type' => 'one-liner','apply_filters' => false)) ?></h2>
		</header>
		<p><?php the_block('Office Photo') ?></p>
		<?php the_block('Perks - Detailed') ?>
		<div class="perks-quick-list">
			<?php the_block('Perks - Quick List') ?>
		</div>
	</div>
</section>

<ul>
	<li></li>
	<li></li>
	<li></li>
	<li></li>
	<li></li>
</ul>


<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>