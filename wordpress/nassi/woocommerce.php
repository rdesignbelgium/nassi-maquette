<?php
/** Boutique, catégories, fiche produit, panier, commande. */
get_header();
if ( is_shop() || is_product_category() || is_product_tag() ) : ?>
<section class="shop shop-page">
	<div class="wrap">
		<div class="sec-head">
			<div><div class="eyebrow">Boutique officielle</div><h1 class="h2"><?php echo is_shop() ? esc_html( nassi_mod( 'nassi_shop_title' ) ) : esc_html( single_term_title( '', false ) ); ?></h1></div>
			<p><?php echo esc_html( nassi_mod( 'nassi_shop_text' ) ); ?></p>
		</div>
		<div class="filters">
			<a class="chip<?php echo is_shop() ? ' on' : ''; ?>" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Tout</a>
			<?php $cats = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true ] ); if ( ! is_wp_error( $cats ) ) foreach ( $cats as $c ) echo '<a class="chip' . ( is_product_category( $c->slug ) ? ' on' : '' ) . '" href="' . esc_url( get_term_link( $c ) ) . '">' . esc_html( $c->name ) . '</a>'; ?>
		</div>
		<?php woocommerce_content(); ?>
	</div>
</section>
<?php else : ?>
<section class="woo-single"><div class="wrap"><?php woocommerce_content(); ?></div></section>
<?php endif;
get_footer();
