<?php
/** Carte produit de la boucle boutique. */
if ( ! defined( 'ABSPATH' ) ) exit;
global $product;
if ( empty( $product ) || ! $product->is_visible() ) return;
nassi_product_card( $product, (int) ( wc_get_loop_prop( 'loop' ) ?: 0 ) );
