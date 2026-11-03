<?php
/** Pages standard (Elementor pleine largeur ou contenu classique). */
get_header();
while ( have_posts() ) : the_post();
	$elementor = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor();
	if ( $elementor ) { the_content(); }
	else { ?>
	<main class="wrap page-content">
		<header class="sec-head"><div><div class="eyebrow">Nassi</div><h1 class="page-title"><?php the_title(); ?></h1></div></header>
		<div class="prose"><?php the_content(); ?></div>
	</main>
	<?php }
endwhile;
get_footer();
