	
	<footer class="site-footer" role="contentinfo">
		<div class="wrap">
			<div class="column">
				<h5 class="footer-links-heading"><?php echo do_shortcode('[block id="2"]');?></h5>
				<?php wp_nav_menu( array('menu' => 'deals menu', 'container' => 'none', 'menu_class' => 'footer-links' )); ?>
			</div>
			<div class="column">
				<h5 class="footer-links-heading"><?php echo do_shortcode('[block id="3"]');?></h5>
				<?php wp_nav_menu( array('menu' => 'company menu', 'container' => 'none', 'menu_class' => 'footer-links' )); ?>
			</div>
			<div class="column">
				<h5 class="footer-links-heading"><?php echo do_shortcode('[block id="4"]');?></h5>
				<?php wp_nav_menu( array('menu' => 'customer service menu', 'container' => 'none', 'menu_class' => 'footer-links' )); ?>
			</div>
			<div class="column">
				<h5 class="footer-links-heading"><?php echo do_shortcode('[block id="5"]');?></h5>
				<?php wp_nav_menu( array('menu' => 'for business menu', 'container' => 'none', 'menu_class' => 'footer-links' )); ?>
			</div>
		</div>
		<div class="wrap">
			<div class="site-summary">
				<p><?php echo do_shortcode('[block id="1"]');?></p>
			</div>
			&copy; <?php echo date("Y"); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.
		</div>
		
	</footer>
