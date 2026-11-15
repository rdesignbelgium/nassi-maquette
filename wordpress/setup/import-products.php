<?php
/**
 * Import du CSV produits avec l'importateur natif WooCommerce.
 * Usage : wp eval-file import-products.php produits-nassi.csv [purge]
 *   purge : supprime d'abord tous les produits existants.
 */
if ( ! defined( 'WP_CLI' ) ) exit( 'WP-CLI uniquement' );
$file = $args[0] ?? '';
if ( ! $file || ! file_exists( $file ) ) WP_CLI::error( 'Fichier CSV introuvable : ' . $file );
if ( in_array( 'purge', $args, true ) ) {
	$ids = get_posts( [ 'post_type' => [ 'product', 'product_variation' ], 'numberposts' => -1, 'post_status' => 'any', 'fields' => 'ids' ] );
	foreach ( $ids as $id ) wp_delete_post( $id, true );
	WP_CLI::log( '  purge : ' . count( $ids ) . ' éléments supprimés' );
}
if ( ! class_exists( 'WC_Product_CSV_Importer' ) ) include_once WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';
if ( ! class_exists( 'WC_Product_CSV_Importer_Controller' ) ) include_once WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';

// Mapping des en-têtes (identique à l'écran d'import de l'admin WooCommerce).
$probe   = new WC_Product_CSV_Importer( $file, [ 'parse' => false, 'lines' => 1 ] );
$headers = $probe->get_raw_keys();
$ctrl    = new WC_Product_CSV_Importer_Controller();
$m       = new ReflectionMethod( $ctrl, 'auto_map_columns' );
$m->setAccessible( true );
$mapping = $m->invoke( $ctrl, $headers, false ); // en-tête brut → clé interne
$mapping = array_map( function ( $v ) { return $v ?: ''; }, $mapping );
WP_CLI::log( '  colonnes reconnues : ' . count( array_filter( $mapping ) ) . '/' . count( $headers ) );

$params = [
	'parse'            => true,
	'update_existing'  => false,
	'mapping'          => [ 'from' => $headers, 'to' => array_values( $mapping ) ],
	'lines'            => 0,
	'start_pos'        => 0,
	'prevent_timeouts' => false,
];
// Selon la version de WooCommerce, le mapping attendu est from/to ou clé→valeur.
$ref = new ReflectionClass( 'WC_Product_CSV_Importer' );
$src = file_get_contents( $ref->getFileName() );
if ( strpos( $src, "params['mapping']['from']" ) === false ) $params['mapping'] = $mapping;

$importer = new WC_Product_CSV_Importer( $file, $params );
$results  = $importer->import();
WP_CLI::log( sprintf( '  importés : %d · mis à jour : %d · ignorés : %d · erreurs : %d',
	count( $results['imported'] ), count( $results['updated'] ), count( $results['skipped'] ), count( $results['failed'] ) ) );
foreach ( $results['failed'] as $err ) {
	if ( is_wp_error( $err ) ) WP_CLI::warning( $err->get_error_message() );
}
WP_CLI::success( 'Import terminé.' );
