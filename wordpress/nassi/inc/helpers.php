<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/** Image du Customizer ou image par défaut du thème. */
function nassi_img( $mod, $fallback, $size = 'full' ) {
	$id = (int) get_theme_mod( $mod );
	if ( $id ) {
		$src = wp_get_attachment_image_url( $id, $size );
		if ( $src ) return $src;
	}
	return NASSI_URI . '/assets/img/' . $fallback;
}

function nassi_mod( $k, $d = '' ) { return get_theme_mod( $k, $d ); }

/** Icônes SVG inline. */
function nassi_icon( $name ) {
	$i = [
		'ticket' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4z"/><path d="M14 6v12" stroke-dasharray="2 2"/></svg>',
		'play'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>',
		'cart'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6L5 3H2"/><circle cx="9" cy="20" r="1.3"/><circle cx="17" cy="20" r="1.3"/></svg>',
		'plus'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>',
		'instagram' => '<svg viewBox="0 0 24 24"><path d="M12 2.2c3.2 0 3.6 0 4.8.1 3.3.1 4.8 1.7 4.9 4.9.1 1.3.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 3.2-1.7 4.8-4.9 4.9-1.3.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8C2.4 3.9 4 2.4 7.2 2.3c1.2-.1 1.6-.1 4.8-.1zM12 0C8.7 0 8.3 0 7.1.1 2.7.3.3 2.7.1 7.1 0 8.3 0 8.7 0 12s0 3.7.1 4.9c.2 4.4 2.6 6.8 7 7 1.2.1 1.6.1 4.9.1s3.7 0 4.9-.1c4.4-.2 6.8-2.6 7-7 .1-1.2.1-1.6.1-4.9s0-3.7-.1-4.9c-.2-4.4-2.6-6.8-7-7C15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 1 0 0 12.4 6.2 6.2 0 0 0 0-12.4zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.8a1.4 1.4 0 1 0 0 2.9 1.4 1.4 0 0 0 0-2.9z"/></svg>',
		'tiktok' => '<svg viewBox="0 0 24 24"><path d="M16.5 2h-3.3v13.4a2.9 2.9 0 1 1-2.9-2.9c.3 0 .6 0 .9.1V9.3a6.2 6.2 0 1 0 5.3 6.1V8.6a7.4 7.4 0 0 0 4.4 1.4V6.7A4.4 4.4 0 0 1 16.5 2z"/></svg>',
		'youtube' => '<svg viewBox="0 0 24 24"><path d="M23 7.2c-.3-1-1-1.8-2-2C19.2 4.7 12 4.7 12 4.7s-7.2 0-9 .5c-1 .3-1.8 1-2 2C.5 9 .5 12 .5 12s0 3 .5 4.8c.3 1 1 1.8 2 2 1.8.5 9 .5 9 .5s7.2 0 9-.5c1-.3 1.8-1 2-2 .5-1.8.5-4.8.5-4.8s0-3-.5-4.8zM9.7 15.3V8.7l6 3.3-6 3.3z"/></svg>',
		'spotify' => '<svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm4.3 14.5c-.2.3-.6.4-.9.2-2.5-1.5-5.6-1.9-9.3-1-.4.1-.7-.1-.8-.5s.1-.7.5-.8c4-.9 7.5-.5 10.3 1.2.3.2.4.6.2.9zm1.1-2.6c-.2.4-.7.5-1.1.3-2.8-1.7-7.2-2.2-10.5-1.2-.5.1-.9-.1-1-.6s.1-.9.6-1c3.8-1.2 8.6-.6 11.9 1.4.3.2.4.7.1 1.1zm.1-2.7C14.2 9.3 8.5 9.1 5.3 10.1c-.5.2-1.1-.1-1.2-.7-.2-.5.1-1.1.7-1.2 3.7-1.1 9.9-.9 13.8 1.4.5.3.6.9.4 1.4-.3.5-1 .6-1.5.4z"/></svg>',
		'apple' => '<svg viewBox="0 0 24 24"><path d="M16.4 12.6c0-2.3 1.9-3.4 2-3.5-1.1-1.6-2.8-1.8-3.4-1.8-1.4-.2-2.8.9-3.5.9s-1.9-.8-3.1-.8C6.8 7.4 5.3 8.3 4.5 9.8c-1.7 3-.4 7.4 1.2 9.8.8 1.2 1.8 2.5 3 2.4 1.2 0 1.7-.8 3.1-.8s1.9.8 3.1.8c1.3 0 2.1-1.2 2.9-2.4.9-1.3 1.3-2.6 1.3-2.7 0 0-2.6-1-2.7-4.3zM14.1 5.8c.6-.8 1.1-1.9 1-3-1 0-2.1.7-2.8 1.5-.6.7-1.2 1.8-1 2.9 1 .1 2.1-.6 2.8-1.4z"/></svg>',
	];
	return $i[ $name ] ?? '';
}

/** Walker minimal : liens à plat, sans <ul>/<li>. */
if ( ! class_exists( 'Nassi_Flat_Walker' ) ) {
	class Nassi_Flat_Walker extends Walker_Nav_Menu {
		public function start_lvl( &$o, $d = 0, $a = null ) {}
		public function end_lvl( &$o, $d = 0, $a = null ) {}
		public function start_el( &$o, $item, $d = 0, $a = null, $id = 0 ) {
			$o .= '<a href="' . esc_url( $item->url ) . '"' . ( $item->target ? ' target="_blank" rel="noopener"' : '' ) . '>' . esc_html( $item->title ) . '</a>';
		}
		public function end_el( &$o, $item, $d = 0, $a = null ) {}
	}
}
