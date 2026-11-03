<?php
/**
 * WooCommerce — intégration au thème (cartes produit, pastilles couleur, panier).
 * Les produits viennent de Gelato (plugin officiel) ou de l'import CSV fourni.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Wrappers Woo dans le thème */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', fn() => print '<main class="wrap woo-main">', 10 );
add_action( 'woocommerce_after_main_content', fn() => print '</main>', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
add_filter( 'woocommerce_enqueue_styles', '__return_true' );

/** Couleurs des pastilles (slug de terme pa_couleur → hex). */
function nassi_swatch_colors() {
	return apply_filters( 'nassi_swatch_colors', [ 'noir' => '#111111', 'gris' => '#b7b7b7', 'blanc' => '#f4f4f4', 'rouge' => '#e4452b', 'orange' => '#f2a21b' ] );
}

/** Pour un produit variable : [ ['slug','name','image'] ... ] par couleur. */
function nassi_color_variants( WC_Product $product ) {
	if ( ! $product->is_type( 'variable' ) ) return [];
	$attr = null;
	foreach ( $product->get_variation_attributes() as $name => $vals ) {
		if ( stripos( $name, 'couleur' ) !== false || stripos( $name, 'color' ) !== false ) { $attr = $name; break; }
	}
	if ( ! $attr ) return [];
	$key = 'attribute_' . sanitize_title( $attr );
	$out = [];
	foreach ( $product->get_available_variations() as $v ) {
		$slug = $v['attributes'][ $key ] ?? '';
		if ( $slug === '' || isset( $out[ $slug ] ) ) continue;
		$term = get_term_by( 'slug', $slug, sanitize_title( $attr ) );
		$out[ $slug ] = [ 'slug' => $slug, 'name' => $term ? $term->name : ucfirst( $slug ), 'image' => $v['image']['src'] ?? '', 'attr' => $key ];
	}
	// Noir en premier
	uksort( $out, fn( $a, $b ) => ( $a === 'noir' ? -1 : ( $b === 'noir' ? 1 : 0 ) ) );
	return array_values( $out );
}

/** Carte produit (accueil + boutique). */
function nassi_product_card( WC_Product $product, $i = 0 ) {
	$colors = nassi_color_variants( $product );
	$img    = $colors ? $colors[0]['image'] : wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
	$cat    = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'names' ] );
	$badge  = $product->is_featured() ? 'Nouveau' : ( $product->is_on_sale() ? 'Promo' : '' );
	$href   = $product->get_permalink();
	?>
	<article class="product" style="animation-delay:<?php echo (int) $i * 40; ?>ms" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
		<a class="ph" href="<?php echo esc_url( $href ); ?>">
			<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
			<?php if ( $badge ) : ?><span class="badge hot"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
		</a>
		<div class="info">
			<span class="col"><?php echo esc_html( $cat[0] ?? 'Boutique officielle' ); ?></span>
			<h3><a href="<?php echo esc_url( $href ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
			<?php if ( count( $colors ) > 1 ) : ?>
			<div class="swatches" aria-label="Couleur">
				<?php foreach ( $colors as $j => $c ) : $hex = nassi_swatch_colors()[ $c['slug'] ] ?? '#888'; ?>
				<button class="sw<?php echo $j === 0 ? ' on' : ''; ?>" data-img="<?php echo esc_url( $c['image'] ); ?>" data-c="<?php echo esc_attr( $c['name'] ); ?>" data-href="<?php echo esc_url( add_query_arg( $c['attr'], $c['slug'], $href ) ); ?>" style="background:<?php echo esc_attr( $hex ); ?>" title="<?php echo esc_attr( $c['name'] ); ?>" aria-label="<?php echo esc_attr( $c['name'] ); ?>"></button>
				<?php endforeach; ?>
				<span class="swl"><?php echo esc_html( $colors[0]['name'] ); ?></span>
			</div>
			<?php endif; ?>
			<div class="row">
				<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			</div>
			<a class="add" href="<?php echo esc_url( $href ); ?>"><?php echo nassi_icon( 'plus' ); ?> Choisir ma taille</a>
		</div>
	</article>
	<?php
}

/* Compteur panier dans le header, mis à jour en AJAX */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $f ) {
	$n = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	$f['.cart-count'] = '<span class="cart-count' . ( $n ? ' on' : '' ) . '">' . (int) $n . '</span>';
	return $f;
} );

/* Boutique : grille du thème au lieu de la grille Woo */
add_filter( 'woocommerce_product_loop_start', fn() => '<div class="products">' );
add_filter( 'woocommerce_product_loop_end', fn() => '</div>' );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop', function () {}, 10 );
add_filter( 'wc_get_template_part', function ( $template, $slug, $name ) {
	if ( $slug === 'content' && $name === 'product' ) return NASSI_DIR . '/woocommerce/content-product.php';
	return $template;
}, 10, 3 );

/* Attribut global « Couleur » créé si absent (utile avant l'import Gelato) */
add_action( 'init', function () {
	if ( ! function_exists( 'wc_attribute_taxonomy_id_by_name' ) ) return;
	if ( get_option( 'nassi_attrs_done' ) ) return;
	foreach ( [ 'Couleur' => 'couleur', 'Taille' => 'taille' ] as $label => $slug ) {
		if ( ! wc_attribute_taxonomy_id_by_name( $slug ) ) {
			wc_create_attribute( [ 'name' => $label, 'slug' => $slug, 'type' => 'select', 'order_by' => 'menu_order', 'has_archives' => false ] );
		}
	}
	update_option( 'nassi_attrs_done', 1 );
}, 20 );
