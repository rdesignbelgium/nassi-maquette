<footer>
	<div class="wrap">
		<div class="foot-grid">
			<div>
				<div class="logo"><img src="<?php echo esc_url( NASSI_URI . '/assets/img/logo-white.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>"></div>
				<p class="foot-tag">Site officiel : musique, concerts et boutique.</p>
				<div class="socials">
					<?php foreach ( [ 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'youtube' => 'YouTube', 'spotify' => 'Spotify' ] as $k => $l ) :
						$u = nassi_mod( "nassi_social_$k" ); if ( ! $u ) continue; ?>
						<a href="<?php echo esc_url( $u ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $l ); ?>"><?php echo nassi_icon( $k ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
			<div><h4>Explorer</h4><ul>
				<li><a href="<?php echo esc_url( home_url( '/#univers' ) ); ?>">Univers</a></li>
				<li><a href="<?php echo esc_url( home_url( '/#musique' ) ); ?>">Musique</a></li>
				<li><a href="<?php echo esc_url( home_url( '/#concert' ) ); ?>">Concert &amp; billets</a></li>
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?><li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Boutique</a></li><?php endif; ?>
			</ul></div>
			<div><h4>Boutique</h4><ul>
				<?php
				$cats = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 5 ] );
				if ( ! is_wp_error( $cats ) ) foreach ( $cats as $c ) echo '<li><a href="' . esc_url( get_term_link( $c ) ) . '">' . esc_html( $c->name ) . '</a></li>';
				?>
			</ul></div>
			<div><h4>Infos</h4>
				<?php if ( has_nav_menu( 'footer' ) ) { wp_nav_menu( [ 'theme_location' => 'footer', 'container' => false, 'depth' => 1 ] ); } else { ?>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact &amp; booking</a></li>
					<li><a href="<?php echo esc_url( home_url( '/cgv/' ) ); ?>">CGV &amp; CGU</a></li>
					<li><a href="<?php echo esc_url( home_url( '/confidentialite/' ) ); ?>">Confidentialité</a></li>
					<li><a href="<?php echo esc_url( home_url( '/mentions-legales/' ) ); ?>">Mentions légales</a></li>
				</ul>
				<?php } ?>
			</div>
		</div>
		<div class="big-logo" aria-hidden="true"><img src="<?php echo esc_url( NASSI_URI . '/assets/img/logo-white.png' ); ?>" alt=""></div>
		<div class="foot-bottom">
			<span>© <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. Tous droits réservés.</span>
			<span>Site conçu par <a href="https://rdesignbelgium.be" target="_blank" rel="noopener">R Design Belgium</a></span>
		</div>
	</div>
</footer>
<div class="sticky-cta" id="stickyCta">
	<a class="btn btn-primary" href="<?php echo esc_url( nassi_mod( 'nassi_concert_url', '#concert' ) ); ?>" target="_blank" rel="noopener">Billets</a>
	<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?><a class="btn btn-ghost" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Shop</a><?php endif; ?>
</div>
<div class="toast" id="toast"></div>
<?php wp_footer(); ?>
</body>
</html>
