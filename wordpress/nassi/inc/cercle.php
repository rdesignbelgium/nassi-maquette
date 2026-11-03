<?php
/**
 * Le Cercle — inscriptions newsletter stockées dans WordPress (CPT privé),
 * exportables en CSV depuis l'admin. Remplaçable par un shortcode externe.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function () {
	register_post_type( 'nassi_cercle', [
		'label'        => 'Le Cercle',
		'labels'       => [ 'name' => 'Le Cercle', 'singular_name' => 'Membre', 'menu_name' => 'Le Cercle' ],
		'public'       => false,
		'show_ui'      => true,
		'menu_icon'    => 'dashicons-groups',
		'supports'     => [ 'title' ],
		'capability_type' => 'post',
		'map_meta_cap' => true,
	] );
} );

add_action( 'rest_api_init', function () {
	register_rest_route( 'nassi/v1', '/cercle', [
		'methods'  => 'POST',
		'permission_callback' => '__return_true',
		'args' => [
			'email' => [ 'required' => true, 'validate_callback' => fn( $v ) => is_email( $v ) ],
			'city'  => [ 'required' => false ],
			'hp'    => [ 'required' => false ], // honeypot
		],
		'callback' => function ( WP_REST_Request $r ) {
			if ( $r->get_param( 'hp' ) ) return new WP_REST_Response( [ 'ok' => true ], 200 );
			$email = sanitize_email( $r->get_param( 'email' ) );
			$city  = sanitize_text_field( (string) $r->get_param( 'city' ) );
			// anti-doublon
			$exists = get_posts( [ 'post_type' => 'nassi_cercle', 'title' => $email, 'post_status' => 'any', 'fields' => 'ids', 'numberposts' => 1 ] );
			if ( ! $exists ) {
				$id = wp_insert_post( [ 'post_type' => 'nassi_cercle', 'post_title' => $email, 'post_status' => 'publish' ] );
				update_post_meta( $id, 'city', $city );
				update_post_meta( $id, 'ip', wp_hash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
				do_action( 'nassi_cercle_subscribed', $email, $city );
			}
			return new WP_REST_Response( [ 'ok' => true ], 200 );
		},
	] );
} );

/* Colonne ville + export CSV */
add_filter( 'manage_nassi_cercle_posts_columns', function ( $c ) { $c['city'] = 'Ville'; return $c; } );
add_action( 'manage_nassi_cercle_posts_custom_column', function ( $col, $id ) { if ( $col === 'city' ) echo esc_html( get_post_meta( $id, 'city', true ) ); }, 10, 2 );
add_action( 'admin_menu', function () {
	add_submenu_page( 'edit.php?post_type=nassi_cercle', 'Exporter en CSV', 'Exporter en CSV', 'manage_options', 'nassi-cercle-export', function () {
		if ( ! current_user_can( 'manage_options' ) ) return;
		$rows = get_posts( [ 'post_type' => 'nassi_cercle', 'numberposts' => -1, 'post_status' => 'publish' ] );
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=cercle-' . date( 'Y-m-d' ) . '.csv' );
		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, [ 'email', 'ville', 'date' ], ';' );
		foreach ( $rows as $p ) fputcsv( $out, [ $p->post_title, get_post_meta( $p->ID, 'city', true ), $p->post_date ], ';' );
		fclose( $out ); exit;
	} );
} );
