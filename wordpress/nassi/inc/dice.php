<?php
/**
 * Billetterie DICE.
 * - Sans clé API : la date vient des réglages du Customizer.
 * - Avec clé API partenaires : les événements sont lus côté serveur (clé jamais exposée)
 *   et mis en cache 30 minutes. Endpoint public : /wp-json/nassi/v1/dice
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function nassi_dice_events() {
	$key = trim( (string) get_theme_mod( 'nassi_dice_api_key' ) );
	$manual = [ [
		'name'   => nassi_mod( 'nassi_concert_name', 'Nassi live' ),
		'start'  => nassi_mod( 'nassi_concert_date', '2026-12-19T20:00:00+01:00' ),
		'venue'  => nassi_mod( 'nassi_concert_venue', 'La Maroquinerie' ),
		'city'   => nassi_mod( 'nassi_concert_city', 'Paris' ),
		'price'  => nassi_mod( 'nassi_concert_price', '24 €' ),
		'status' => nassi_mod( 'nassi_concert_status', 'En vente' ),
		'url'    => nassi_mod( 'nassi_concert_url', 'https://dice.fm/' ),
	] ];
	if ( ! $key ) return $manual;

	$cached = get_transient( 'nassi_dice_events' );
	if ( is_array( $cached ) ) return $cached;

	$filter = rawurlencode( (string) get_theme_mod( 'nassi_dice_artist', 'Nassi' ) );
	$res = wp_remote_get( "https://partners-endpoint.dice.fm/api/v2/events?page[size]=10&filter[artists]={$filter}", [
		'headers' => [ 'x-api-key' => $key, 'Accept' => 'application/json' ],
		'timeout' => 8,
	] );
	if ( is_wp_error( $res ) || wp_remote_retrieve_response_code( $res ) !== 200 ) return $manual;

	$json = json_decode( wp_remote_retrieve_body( $res ), true );
	$out = [];
	foreach ( (array) ( $json['data'] ?? [] ) as $e ) {
		$price = $e['ticket_types'][0]['price']['face_value'] ?? null;
		$out[] = [
			'name'   => $e['name'] ?? '',
			'start'  => $e['dates']['event_start_date'] ?? ( $e['date'] ?? '' ),
			'venue'  => $e['venues'][0]['name'] ?? '',
			'city'   => $e['venues'][0]['city']['name'] ?? '',
			'price'  => $price !== null ? number_format( $price / 100, 0, ',', ' ' ) . ' €' : '—',
			'status' => ! empty( $e['sold_out'] ) ? 'Complet' : 'En vente',
			'url'    => $e['url'] ?? 'https://dice.fm/',
		];
	}
	if ( ! $out ) $out = $manual;
	set_transient( 'nassi_dice_events', $out, 30 * MINUTE_IN_SECONDS );
	return $out;
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'nassi/v1', '/dice', [
		'methods'  => 'GET',
		'callback' => fn() => rest_ensure_response( [ 'data' => nassi_dice_events() ] ),
		'permission_callback' => '__return_true',
	] );
} );

add_action( 'customize_save_after', fn() => delete_transient( 'nassi_dice_events' ) );
