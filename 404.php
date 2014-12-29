<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/empty-header' ) ); ?>



<main role="main" class="module module__404">
	<div class="wrap">
		<img src="/wp-content/themes/ls-careers/images/blink.gif" alt="">
		<h1><?php echo do_shortcode('[contentblock id=404heading]') ?></h1>
		<p> <?php echo do_shortcode('[contentblock id=404text]') ?></p>
		<a href="/" class="btn btn-inverse">view all jobs</a>
	</div>
</main>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>