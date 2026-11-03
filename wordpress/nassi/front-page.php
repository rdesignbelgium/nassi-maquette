<?php
/**
 * Page d'accueil — toutes les sections sont pilotées par Apparence → Personnaliser → Nassi.
 * Si la page d'accueil est construite avec Elementor, ce gabarit est ignoré (voir page.php).
 */
get_header();
$title = array_pad( explode( '|', nassi_mod( 'nassi_hero_title', 'Entre|deux|rives.' ) ), 3, '' );
?>
<section class="hero" id="top">
	<div class="hero-bg"><img src="<?php echo esc_url( nassi_img( 'nassi_hero_image', 'r3a7417.jpg' ) ); ?>" alt=""></div>
	<div class="hero-veil"></div>
	<canvas class="hero-grain" id="grain" aria-hidden="true"></canvas>
	<div class="wrap">
		<div class="hero-eyebrow"><b>Live</b> <?php echo esc_html( nassi_mod( 'nassi_hero_badge' ) ); ?></div>
		<h1><?php echo esc_html( $title[0] ); ?><br><span class="outline"><?php echo esc_html( $title[1] ); ?></span><em><?php echo esc_html( $title[2] ); ?></em></h1>
		<p><?php echo esc_html( nassi_mod( 'nassi_hero_text' ) ); ?></p>
		<div class="hero-cta">
			<a class="btn btn-primary" href="<?php echo esc_url( nassi_mod( 'nassi_concert_url', '#concert' ) ); ?>" target="_blank" rel="noopener"><?php echo nassi_icon( 'ticket' ); ?>Réserver mon billet</a>
			<a class="btn btn-ghost" href="#musique"><?php echo nassi_icon( 'play' ); ?>Écouter</a>
		</div>
		<div class="hero-meta">
			<div><strong><?php echo esc_html( nassi_mod( 'nassi_album', 'Bent nass' ) ); ?></strong>Dernière sortie</div>
			<div><strong><?php echo esc_html( nassi_mod( 'nassi_concert_venue', 'La Maroquinerie' ) ); ?></strong>Prochain concert</div>
			<?php if ( function_exists( 'wc_get_products' ) ) : ?><div><strong><?php echo (int) wp_count_posts( 'product' )->publish; ?> pièces</strong>Boutique officielle</div><?php endif; ?>
		</div>
	</div>
	<div class="scroll-cue">Scroll<i></i></div>
</section>

<div class="marquee" aria-hidden="true"><div class="marquee-track" id="marquee">
	<?php foreach ( explode( '|', nassi_mod( 'nassi_marquee', 'Arabesque' ) ) as $t ) echo '<span>' . esc_html( trim( $t ) ) . '</span>'; ?>
</div></div>

<section class="univers" id="univers">
	<div class="wrap">
		<div class="sec-head reveal">
			<div><div class="eyebrow">L’univers</div><h2><?php echo esc_html( nassi_mod( 'nassi_univers_title' ) ); ?></h2></div>
			<p>Studio, scène, coulisses : les images qui racontent Nassi.</p>
		</div>
		<div class="univers-grid">
			<?php
			$defaults = [ 'r3a7615.jpg', '1x6a8596.jpg', 'r3a7234.jpg', 'r3a7562.jpg', 'r3a7398.jpg' ];
			$caps = [ 'Session studio', 'Backstage', 'Lumière de scène', 'Portrait', 'Ambre' ];
			for ( $i = 1; $i <= 5; $i++ ) :
				if ( $i === 4 ) echo '<div class="tile-text reveal"><strong>III</strong><span>volumes Arabesque, une seule signature.</span></div>'; ?>
				<figure class="tile t<?php echo $i; ?> reveal"><img src="<?php echo esc_url( nassi_img( "nassi_univers_$i", $defaults[ $i - 1 ], 'nassi-tile' ) ); ?>" alt="Nassi"><figcaption class="cap"><?php echo esc_html( $caps[ $i - 1 ] ); ?></figcaption></figure>
			<?php endfor; ?>
		</div>
		<div class="univers-copy">
			<div class="reveal">
				<h3>Une musique qui parle à ceux qui vivent entre deux cultures.</h3>
				<p><?php echo esc_html( nassi_mod( 'nassi_univers_text' ) ); ?></p>
			</div>
			<div class="quote reveal">« <?php echo esc_html( nassi_mod( 'nassi_quote' ) ); ?> »<small>Nassi</small></div>
		</div>
	</div>
</section>

<section class="musique" id="musique">
	<div class="wrap">
		<div class="sec-head reveal">
			<div><div class="eyebrow">Musique</div><h2>Dernière sortie</h2></div>
			<p>Disponible sur toutes les plateformes. Lancez l’écoute, montez le son.</p>
		</div>
		<div class="release">
			<div class="cover reveal">
				<img src="<?php echo esc_url( nassi_img( 'nassi_cover', 'r3a7306.jpg' ) ); ?>" alt="<?php echo esc_attr( nassi_mod( 'nassi_album' ) ); ?>">
				<span class="vol"><img src="<?php echo esc_url( NASSI_URI . '/assets/img/logo-white.png' ); ?>" alt="Nassi"></span>
				<a class="play" href="<?php echo esc_url( nassi_mod( 'nassi_link_all' ) ); ?>" target="_blank" rel="noopener" aria-label="Écouter"><?php echo nassi_icon( 'play' ); ?></a>
			</div>
			<div class="reveal">
				<div class="eyebrow">Nouveau</div>
				<h3><?php echo esc_html( nassi_mod( 'nassi_album' ) ); ?></h3>
				<p><?php echo esc_html( nassi_mod( 'nassi_album_text' ) ); ?></p>
				<div class="platforms">
					<?php foreach ( [ 'spotify' => 'Spotify', 'apple' => 'Apple Music', 'youtube' => 'YouTube', 'all' => 'Toutes les plateformes' ] as $k => $l ) :
						$u = nassi_mod( "nassi_link_$k" ); if ( ! $u ) continue; ?>
						<a href="<?php echo esc_url( $u ); ?>" target="_blank" rel="noopener"><?php echo nassi_icon( $k === 'all' ? 'play' : $k ); ?><?php echo esc_html( $l ); ?></a>
					<?php endforeach; ?>
				</div>
				<div class="tracklist" id="tracklist">
					<?php $n = 0; foreach ( explode( '|', nassi_mod( 'nassi_tracks' ) ) as $t ) { $p = explode( ':', $t, 2 ); if ( count( $p ) < 2 ) continue; $n++; ?>
					<div class="track"><span class="n"><?php printf( '%02d', $n ); ?></span><span class="t"><?php echo esc_html( trim( $p[0] ) ); ?></span><span class="bars"><i></i><i></i><i></i></span><span class="d"><?php echo esc_html( trim( $p[1] ) ); ?></span></div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="concert" id="concert">
	<div class="spot"></div>
	<div class="wrap">
		<div class="concert-grid">
			<div class="reveal">
				<div class="eyebrow">Concert</div>
				<h2 class="h2"><?php echo esc_html( nassi_mod( 'nassi_concert_city', 'Paris' ) ); ?>, le <?php echo esc_html( date_i18n( 'j F', strtotime( nassi_mod( 'nassi_concert_date', '2026-12-19' ) ) ) ); ?>. Une seule soirée.</h2>
				<p class="lead"><?php echo esc_html( nassi_mod( 'nassi_concert_text' ) ); ?></p>
				<div class="countdown" id="countdown" aria-live="polite">
					<div><b id="cdD">00</b><small>jours</small></div><div><b id="cdH">00</b><small>heures</small></div><div><b id="cdM">00</b><small>min</small></div><div><b id="cdS">00</b><small>sec</small></div>
				</div>
				<div class="events" id="events">
					<?php foreach ( nassi_dice_events() as $e ) : $d = strtotime( $e['start'] ); $cls = stripos( $e['status'], 'complet' ) !== false ? ' soldout' : ''; ?>
					<a class="event" href="<?php echo esc_url( $e['url'] ); ?>" target="_blank" rel="noopener">
						<div class="date"><b><?php echo esc_html( date_i18n( 'd', $d ) ); ?></b><span><?php echo esc_html( date_i18n( 'M', $d ) ); ?></span></div>
						<div><div class="venue"><?php echo esc_html( $e['venue'] ); ?></div><div class="city"><?php echo esc_html( $e['city'] ); ?> · <?php echo esc_html( date_i18n( 'H:i', $d ) ); ?> · <?php echo esc_html( $e['name'] ); ?></div></div>
						<div class="price"><b><?php echo esc_html( $e['price'] ); ?></b><span class="status<?php echo $cls; ?>"><?php echo esc_html( $e['status'] ); ?></span></div>
					</a>
					<?php endforeach; ?>
				</div>
				<div class="dice-note"><span class="dice">DICE</span> Billetterie officielle · paiement sécurisé · billets sur l’app DICE</div>
			</div>
			<div class="concert-photo reveal">
				<img src="<?php echo esc_url( nassi_img( 'nassi_concert_image', 'r3a7319.jpg' ) ); ?>" alt="Nassi sur scène">
				<span class="tag">Sur scène</span>
			</div>
		</div>
	</div>
</section>

<?php if ( function_exists( 'wc_get_products' ) ) : ?>
<section class="shop" id="shop">
	<div class="wrap">
		<div class="sec-head reveal">
			<div><div class="eyebrow">Boutique officielle</div><h2><?php echo esc_html( nassi_mod( 'nassi_shop_title' ) ); ?></h2></div>
			<p><?php echo esc_html( nassi_mod( 'nassi_shop_text' ) ); ?></p>
		</div>
		<div class="filters">
			<a class="chip on" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Tout</a>
			<?php $cats = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true ] ); if ( ! is_wp_error( $cats ) ) foreach ( $cats as $c ) echo '<a class="chip" href="' . esc_url( get_term_link( $c ) ) . '">' . esc_html( $c->name ) . '</a>'; ?>
		</div>
		<div class="products">
			<?php $i = 0; foreach ( wc_get_products( [ 'limit' => (int) nassi_mod( 'nassi_shop_count', 8 ), 'status' => 'publish', 'orderby' => 'menu_order title', 'order' => 'ASC' ] ) as $p ) nassi_product_card( $p, $i++ ); ?>
		</div>
		<div class="shop-foot reveal">
			<div class="items">
				<div><b>Paiement sécurisé</b>Bancontact, Visa, Mastercard, PayPal</div>
				<div><b>Impression à la demande</b>Expédié depuis l’Europe</div>
				<div><b>Retours</b>14 jours pour changer d’avis</div>
			</div>
			<a class="btn btn-ghost" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Voir toute la boutique</a>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="cercle" id="cercle">
	<div class="pattern"></div>
	<div class="wrap">
		<div class="reveal">
			<div class="eyebrow">Le Cercle</div>
			<h2><?php echo esc_html( nassi_mod( 'nassi_cercle_title' ) ); ?></h2>
			<p><?php echo esc_html( nassi_mod( 'nassi_cercle_text' ) ); ?></p>
			<div class="perks"><span>Pré-vente 48 h</span><span>Code -10 % sur le shop</span><span>Nouveautés en avant-première</span></div>
		</div>
		<?php if ( $sc = nassi_mod( 'nassi_cercle_shortcode' ) ) : ?>
			<div class="form reveal"><?php echo do_shortcode( $sc ); ?></div>
		<?php else : ?>
		<form class="form reveal" id="joinForm" novalidate>
			<div class="f">
				<label for="jEmail">E-mail</label>
				<input id="jEmail" name="email" type="email" placeholder="vous@exemple.be" autocomplete="email" required>
				<label for="jCity">Ville (pour les alertes concerts)</label>
				<input id="jCity" name="city" type="text" placeholder="Bruxelles, Paris, Liège…" autocomplete="address-level2">
				<input type="text" name="hp" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
				<button class="btn btn-primary" type="submit">Rejoindre le Cercle</button>
				<p class="legal">En vous inscrivant, vous acceptez de recevoir les actualités de Nassi. Désinscription en un clic, à tout moment.</p>
			</div>
			<div class="ok">✦ Bienvenue dans le Cercle. Votre code -10 % arrive par e-mail.</div>
		</form>
		<?php endif; ?>
	</div>
</section>
<?php get_footer();
