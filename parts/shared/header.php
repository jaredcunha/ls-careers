<header id="site-header" class="hdr-main <?php if( is_front_page() ) : ?>hdr-main--extended<?php endif;?>" role="banner">
	<div class="wrap">
		<div class="top-bar">
			<p class="logo-main"><a href="<?php echo home_url(); ?>"><img src="/wp-content/themes/ls-careers/dist/prod/images/svg/logo.svg" onerror="this.src=/wp-content/themes/ls-careers/images/logo@2x.png" alt="<?php bloginfo( 'name' ); ?>"></a></p>
			<nav class="nav-main">
				<?php wp_nav_menu( array('menu' => 'main navigation', 'container' => 'none' )); ?>
			</nav>	
			</div>
	</div>

<?php if( ! is_front_page() ) : ?>
</header>
<?php endif;?>

	
		
