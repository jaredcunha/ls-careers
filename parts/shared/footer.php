	
	<footer class="site-footer" role="contentinfo">
		<div class="wrap">
			<div class="column">
				<h5 class="footer-links-heading"><?php echo do_shortcode('[contentblock id=lsfooternavtitle]');?></h5>
				<?php wp_nav_menu( array('menu' => 'deals menu', 'container' => 'none', 'menu_class' => 'footer-links' )); ?>
			</div>
			<div class="column">
				<h5 class="footer-links-heading"><?php echo do_shortcode('[contentblock id=lsfootercompanytitle]');?></h5>
				<?php wp_nav_menu( array('menu' => 'company menu', 'container' => 'none', 'menu_class' => 'footer-links' )); ?>
			</div>
			<div class="column">
				<h5 class="footer-links-heading"><?php echo do_shortcode('[contentblock id=lsfootercstitle]');?></h5>
				<?php wp_nav_menu( array('menu' => 'customer service menu', 'container' => 'none', 'menu_class' => 'footer-links' )); ?>
			</div>
			<div class="column">
				<h5 class="footer-links-heading"><?php echo do_shortcode('[contentblock id=lsfooterbusinesstitle]');?></h5>
				<?php wp_nav_menu( array('menu' => 'for business menu', 'container' => 'none', 'menu_class' => 'footer-links' )); ?>
			</div>
		</div>
		<div class="wrap">
			<div class="site-summary">
				<p><?php echo do_shortcode('[contentblock id=footertext]');?></p>
			</div>
			<p class="copyright">&copy; <?php echo date("Y"); ?>, LivingSocial, Inc. or its affiliated companies. All rights reserved. <a href="https://www.livingsocial.com/terms">Terms of Use</a>. <a href="https://www.livingsocial.com/privacy_policy">Privacy Policy</a>.</p>
		</div>
		
	</footer>
