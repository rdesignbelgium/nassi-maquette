<?php
/**
 * Import du CSV produits avec l'importateur natif WooCommerce.
 * Usage : wp eval-file import-products.php produits-nassi.csv
 */
if ( ! defined( 'WP_CLI' ) ) exit( 'WP-CLI uniquement' );
$file = $args[0] ?? '';
if ( ! $file || ! file_exists( $file ) ) WP_CLI::error( 'Fichier CSV introuvable : ' . $file );
if ( ! class_exists( 'WC_Product_CSV_Importer' ) ) {
	include_once WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';
}
$params = [
	'parse'           => true,
	'update_existing' => true,
	'mapping'         => [], // en-têtes standard WooCommerce → mapping automatique
	'lines'           => 0,
	'start_pos'       => 0,
	'prevent_timeouts'=> false,
];
$importer = new WC_Product_CSV_Importer( $file, $params );
$results  = $importer->import();
WP_CLI::log( sprintf( '  importés : %d · mis à jour : %d · ignorés : %d · erreurs : %d',
	count( $results['imported'] ), count( $results['updated'] ), count( $results['skipped'] ), count( $results['failed'] ) ) );
foreach ( $results['failed'] as $err ) {
	if ( is_wp_error( $err ) ) WP_CLI::warning( $err->get_error_message() );
}
WP_CLI::success( 'Import terminé.' );
