<?php
/** Gabarit par défaut (articles, archives). */
get_header(); ?>
<main class="wrap page-content">
	<header class="sec-head"><div><div class="eyebrow">Actualités</div><h1 class="page-title"><?php echo is_home() ? 'Actualités' : wp_strip_all_tags( get_the_archive_title() ); ?></h1></div></header>
	<div class="prose">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?>>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php is_singular() ? the_content() : the_excerpt(); ?>
		</article>
	<?php endwhile; the_posts_pagination(); else : ?><p>Rien à afficher pour le moment.</p><?php endif; ?>
	</div>
</main>
<?php get_footer();
