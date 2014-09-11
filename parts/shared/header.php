<header>
	<h1><a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a></h1>
	<?php bloginfo( 'description' ); ?>
	<form method="get" action="<?php esc_url( home_url( '/' ) ); ?>" id="findfood">       
   <?php custom_taxonomy_dropdown( 'department' ); ?>
   <?php custom_taxonomy_dropdown( 'location' ); ?>
   <button type="submit">submit</button>
</form>
</header>
