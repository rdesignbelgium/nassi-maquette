<?php
/**
 * Réglages de la page d'accueil (Apparence → Personnaliser → Nassi).
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', function ( WP_Customize_Manager $wp ) {

	$wp->add_panel( 'nassi', [ 'title' => 'Nassi — Accueil', 'priority' => 10 ] );

	$sections = [
		'hero'    => 'Hero (haut de page)',
		'univers' => 'Univers (galerie)',
		'musique' => 'Musique',
		'concert' => 'Concert & billetterie DICE',
		'shop'    => 'Boutique',
		'cercle'  => 'Le Cercle (newsletter)',
		'social'  => 'Réseaux sociaux',
	];
	foreach ( $sections as $id => $title ) {
		$wp->add_section( "nassi_$id", [ 'title' => $title, 'panel' => 'nassi' ] );
	}

	$f = function ( $id, $label, $section, $type = 'text', $default = '', $choices = [] ) use ( $wp ) {
		$wp->add_setting( $id, [ 'default' => $default, 'sanitize_callback' => $type === 'textarea' ? 'wp_kses_post' : ( $type === 'url' ? 'esc_url_raw' : 'sanitize_text_field' ) ] );
		$args = [ 'label' => $label, 'section' => "nassi_$section", 'type' => $type ];
		if ( $choices ) $args['choices'] = $choices;
		$wp->add_control( $id, $args );
	};
	$img = function ( $id, $label, $section ) use ( $wp ) {
		$wp->add_setting( $id, [ 'sanitize_callback' => 'absint' ] );
		$wp->add_control( new WP_Customize_Media_Control( $wp, $id, [ 'label' => $label, 'section' => "nassi_$section", 'mime_type' => 'image' ] ) );
	};

	// Hero
	$img( 'nassi_hero_image', 'Photo de fond', 'hero' );
	$f( 'nassi_hero_badge', 'Badge (ex. « 19 décembre · La Maroquinerie, Paris »)', 'hero', 'text', '19 décembre · La Maroquinerie, Paris · Billets en vente' );
	$f( 'nassi_hero_title', 'Titre (3 lignes séparées par |)', 'hero', 'text', 'Entre|deux|rives.' );
	$f( 'nassi_hero_text', 'Texte', 'hero', 'textarea', 'Des mélodies qui traversent la Méditerranée, des textes qui parlent au cœur. Bienvenue dans l’univers de Nassi : la musique, la scène, et les pièces à porter.' );
	$f( 'nassi_marquee', 'Bandeau défilant (titres séparés par |)', 'hero', 'text', 'Arabesque|Ya Lalla|Layali|Boussa|Rifia|Dark Nador|Bent nass|Mes yeux' );

	// Univers
	for ( $i = 1; $i <= 5; $i++ ) $img( "nassi_univers_$i", "Photo $i", 'univers' );
	$f( 'nassi_univers_title', 'Titre', 'univers', 'text', 'La lumière, le micro, et une voix.' );
	$f( 'nassi_univers_text', 'Texte', 'univers', 'textarea', 'Nassi chante ce qu’on ressent sans toujours savoir le dire : l’attachement, la nostalgie, les retrouvailles, la fête.' );
	$f( 'nassi_quote', 'Citation', 'univers', 'textarea', 'La scène, c’est l’endroit où la distance disparaît. Chaque concert, c’est une maison qu’on ouvre.' );

	// Musique
	$img( 'nassi_cover', 'Pochette', 'musique' );
	$f( 'nassi_album', 'Titre de la sortie', 'musique', 'text', 'Bent nass' );
	$f( 'nassi_album_text', 'Texte', 'musique', 'textarea', 'Le nouveau single de Nassi, disponible sur toutes les plateformes.' );
	$f( 'nassi_link_all', 'Lien « toutes les plateformes » (orcd.co, linktree…)', 'musique', 'url', 'https://orcd.co/arabesque3' );
	$f( 'nassi_link_spotify', 'Spotify', 'musique', 'url', 'https://open.spotify.com/intl-fr/artist/1qKKI6tBqJZCZfAmXjYFjN' );
	$f( 'nassi_link_apple', 'Apple Music', 'musique', 'url', 'https://music.apple.com/fr/artist/nassi/385335071' );
	$f( 'nassi_link_youtube', 'YouTube (clip)', 'musique', 'url', 'https://youtu.be/WWba-sSMEQk' );
	$f( 'nassi_tracks', 'Titres (Titre:durée séparés par |)', 'musique', 'text', 'Ya Lalla:3:12|Layali:3:28|Boussa:2:57|Rifia:3:41|Dark Nador:3:05' );

	// Concert
	$f( 'nassi_concert_date', 'Date et heure (ISO, ex. 2026-12-19T20:00:00+01:00)', 'concert', 'text', '2026-12-19T20:00:00+01:00' );
	$f( 'nassi_concert_name', 'Nom de l’événement', 'concert', 'text', 'Nassi live · +18' );
	$f( 'nassi_concert_venue', 'Salle', 'concert', 'text', 'La Maroquinerie' );
	$f( 'nassi_concert_city', 'Ville', 'concert', 'text', 'Paris 20e' );
	$f( 'nassi_concert_price', 'Prix affiché', 'concert', 'text', '24 €' );
	$f( 'nassi_concert_status', 'Statut', 'concert', 'select', 'En vente', [ 'En vente' => 'En vente', 'Dernières places' => 'Dernières places', 'Complet' => 'Complet', 'Bientôt' => 'Bientôt' ] );
	$f( 'nassi_concert_url', 'Lien billetterie DICE', 'concert', 'url', 'https://dice.fm/event/l8dqkr-nassi-19th-dec-la-maroquinerie-paris-tickets?lng=fr' );
	$f( 'nassi_concert_text', 'Texte', 'concert', 'textarea', 'Nassi en live à La Maroquinerie, 23 rue Boyer, Paris 20e. Ouverture des portes à 20 h, prix final 24 € sans frais cachés, billet mobile sécurisé via DICE.' );
	$img( 'nassi_concert_image', 'Photo', 'concert' );
	$f( 'nassi_dice_api_key', 'Clé API partenaires DICE (optionnel : remplace la date manuelle)', 'concert', 'text', '' );
	$f( 'nassi_dice_artist', 'Filtre DICE (nom d’artiste ou ID)', 'concert', 'text', 'Nassi' );

	// Shop
	$f( 'nassi_shop_title', 'Titre', 'shop', 'text', 'Portez les couleurs d’Arabesque.' );
	$f( 'nassi_shop_text', 'Texte', 'shop', 'textarea', 'Hoodies, t-shirts et posters en édition officielle. Expédition en Belgique et en Europe.' );
	$f( 'nassi_shop_count', 'Nombre de produits affichés', 'shop', 'number', 8 );

	// Cercle
	$f( 'nassi_cercle_title', 'Titre', 'cercle', 'text', 'Soyez les premiers au courant.' );
	$f( 'nassi_cercle_text', 'Texte', 'cercle', 'textarea', 'Pré-ventes de billets, sorties en avant-première, pièces de la collection en édition limitée : les membres du Cercle reçoivent tout avant tout le monde.' );
	$f( 'nassi_cercle_shortcode', 'Shortcode d’un formulaire externe (MailPoet, Fluent Forms…) — vide = formulaire intégré', 'cercle', 'text', '' );

	// Social
	foreach ( [ 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'youtube' => 'YouTube', 'spotify' => 'Spotify' ] as $k => $l ) {
		$f( "nassi_social_$k", $l, 'social', 'url', '' );
	}
} );
