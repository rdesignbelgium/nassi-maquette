<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="nav" id="nav">
	<div class="wrap">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?> — accueil">
			<i></i>
			<?php if ( has_custom_logo() ) { the_custom_logo(); } else { ?>
				<img src="<?php echo esc_url( NASSI_URI . '/assets/img/logo-white.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
			<?php } ?>
		</a>
		<nav class="nav-links" id="navLinks" aria-label="Navigation">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( [ 'theme_location' => 'primary', 'container' => false, 'items_wrap' => '%3$s', 'depth' => 1, 'walker' => new Nassi_Flat_Walker() ] );
			} else {
				$home = home_url( '/' );
				foreach ( [ 'univers' => 'Univers', 'musique' => 'Musique', 'concert' => 'Concert', 'shop' => 'Shop', 'cercle' => 'Le Cercle' ] as $a => $l ) {
					echo '<a href="' . esc_url( $home . '#' . $a ) . '">' . esc_html( $l ) . '</a>';
				}
			}
			?>
		</nav>
		<div class="nav-actions">
			<a class="btn btn-primary btn-nav" href="<?php echo esc_url( nassi_mod( 'nassi_concert_url', '#concert' ) ); ?>" target="_blank" rel="noopener">Billets</a>
			<?php if ( function_exists( 'wc_get_cart_url' ) ) : $n = WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?>
			<a class="cart-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="Panier">
				<?php echo nassi_icon( 'cart' ); ?>
				<span class="cart-count<?php echo $n ? ' on' : ''; ?>"><?php echo (int) $n; ?></span>
			</a>
			<?php endif; ?>
			<button class="burger" id="burger" aria-label="Menu" aria-expanded="false"><span></span><span></span><span></span></button>
		</div>
	</div>
</header>
