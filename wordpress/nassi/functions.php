<?php
/**
 * Thème Nassi — fonctions principales.
 * Réalisé par R Design Belgium.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'NASSI_VERSION', '1.1.1' );
define( 'NASSI_DIR', get_template_directory() );
define( 'NASSI_URI', get_template_directory_uri() );

require NASSI_DIR . '/inc/defaults.php';
require NASSI_DIR . '/inc/customizer.php';
require NASSI_DIR . '/inc/woocommerce.php';
require NASSI_DIR . '/inc/dice.php';
require NASSI_DIR . '/inc/cercle.php';
require NASSI_DIR . '/inc/helpers.php';

/* ---------- Réglages du thème ---------- */
add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'nassi', NASSI_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'script', 'style' ] );
	add_theme_support( 'custom-logo', [ 'height' => 40, 'width' => 300, 'flex-width' => true ] );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'align-wide' );
	register_nav_menus( [
		'primary' => __( 'Navigation principale', 'nassi' ),
		'footer'  => __( 'Pied de page — Infos', 'nassi' ),
	] );
	add_image_size( 'nassi-tile', 1200, 1200, false );
} );

/* ---------- Scripts et styles ---------- */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'nassi-fonts', 'https://fonts.googleapis.com/css2?family=Unbounded:wght@400;600;800;900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap', [], null );
	wp_enqueue_style( 'nassi-style', home_url( '/nassi-assets/v' . NASSI_VERSION . '/style.css' ), [ 'nassi-fonts' ], null );
	wp_enqueue_script( 'nassi-main', home_url( '/nassi-assets/v' . NASSI_VERSION . '/js/main.js' ), [], null, true );
	wp_localize_script( 'nassi-main', 'NASSI', [
		'rest'       => esc_url_raw( rest_url( 'nassi/v1/' ) ),
		'nonce'      => wp_create_nonce( 'wp_rest' ),
		'concertAt'  => get_theme_mod( 'nassi_concert_date', '2026-12-19T20:00:00+01:00' ),
		'cartUrl'    => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
	] );
} );

/* ---------- Elementor : largeur pleine, pas de conflit ---------- */
add_action( 'elementor/theme/register_locations', function ( $manager ) {
	$manager->register_all_core_location();
} );

/* ---------- Nettoyage ---------- */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'body_class', function ( $c ) { $c[] = 'nassi'; return $c; } );

/**
 * Assets servis sous une URL versionnée (/nassi-assets/v1.2.3/js/main.js) :
 * chaque mise à jour du thème change l'URL, ce qui contourne les caches serveur (Varnish/CDN)
 * qui ignorent le paramètre ?ver=.
 */
add_action( 'init', function () {
	add_rewrite_rule( '^nassi-assets/v[0-9.]+/(.+)$', 'index.php?nassi_asset=$matches[1]', 'top' );
	if ( get_option( 'nassi_rewrite_version' ) !== NASSI_VERSION ) {
		flush_rewrite_rules( false );
		update_option( 'nassi_rewrite_version', NASSI_VERSION );
	}
} );
add_filter( 'query_vars', function ( $v ) { $v[] = 'nassi_asset'; return $v; } );
add_action( 'parse_request', function ( $wp ) {
	if ( empty( $wp->query_vars['nassi_asset'] ) ) return;
	$rel = $wp->query_vars['nassi_asset'];
	if ( $rel === 'style.css' ) $file = NASSI_DIR . '/style.css';
	else $file = NASSI_DIR . '/assets/' . $rel;
	$real = realpath( $file );
	$ext  = strtolower( pathinfo( (string) $real, PATHINFO_EXTENSION ) );
	$types = [ 'css' => 'text/css', 'js' => 'text/javascript', 'svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'webp' => 'image/webp', 'woff2' => 'font/woff2' ];
	if ( ! $real || strpos( $real, realpath( NASSI_DIR ) ) !== 0 || ! isset( $types[ $ext ] ) ) { status_header( 404 ); exit; }
	header( 'Content-Type: ' . $types[ $ext ] . ( in_array( $ext, [ 'css', 'js', 'svg' ], true ) ? '; charset=UTF-8' : '' ) );
	header( 'Cache-Control: public, max-age=31536000, immutable' );
	header( 'Content-Length: ' . filesize( $real ) );
	readfile( $real );
	exit;
} );
