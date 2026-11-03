#!/usr/bin/env bash
# =====================================================================
#  Nassi — installation de la structure WordPress / WooCommerce / Gelato
#  R Design Belgium
#
#  Prérequis : WordPress installé, WP-CLI disponible, exécuter depuis la
#  racine du site (là où se trouve wp-config.php).
#
#  Usage :  bash setup.sh            (installation complète)
#           bash setup.sh --no-csv   (sans l'import CSV, si les produits
#                                     seront poussés depuis Gelato)
# =====================================================================
set -euo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
IMPORT_CSV=1; [[ "${1:-}" == "--no-csv" ]] && IMPORT_CSV=0

echo "▶ Thème"
wp theme install "$HERE/../nassi.zip" --activate --force 2>/dev/null || { cp -r "$HERE/../nassi" "$(wp theme path)/" && wp theme activate nassi; }

echo "▶ Extensions"
wp plugin install woocommerce elementor gelato-integration-for-woocommerce mollie-payments-for-woocommerce wordpress-seo wp-mail-smtp --activate
wp plugin uninstall hello-dolly akismet --deactivate 2>/dev/null || true

echo "▶ Réglages généraux"
wp option update blogname "Nassi"
wp option update blogdescription "Site officiel — musique, concerts, boutique"
wp option update timezone_string "Europe/Paris"
wp option update date_format "j F Y"
wp option update permalink_structure "/%postname%/"
wp language core install fr_FR --activate 2>/dev/null || true
wp site switch-language fr_FR 2>/dev/null || true

echo "▶ WooCommerce"
wp option update woocommerce_store_address "23 rue Boyer"
wp option update woocommerce_store_city "Paris"
wp option update woocommerce_default_country "FR"
wp option update woocommerce_store_postcode "75020"
wp option update woocommerce_currency "EUR"
wp option update woocommerce_currency_pos "right_space"
wp option update woocommerce_price_decimal_sep ","
wp option update woocommerce_price_thousand_sep " "
wp option update woocommerce_calc_taxes "yes"
wp option update woocommerce_prices_include_tax "yes"
wp option update woocommerce_allowed_countries "specific"
wp option update woocommerce_specific_allowed_countries '["FR","BE","LU","CH","NL","DE","ES","IT","MA"]' --format=json
wp option update woocommerce_enable_guest_checkout "yes"
wp option update woocommerce_enable_checkout_login_reminder "no"
wp option update woocommerce_single_image_width 800
wp option update woocommerce_thumbnail_image_width 600
wp option update woocommerce_thumbnail_cropping "1:1"
wp wc tool run install_pages --user=1 2>/dev/null || wp eval 'WC_Install::create_pages();'

echo "▶ Attributs globaux (Couleur, Taille, Format)"
for a in "Couleur:couleur" "Taille:taille" "Format:format"; do
  n="${a%%:*}"; s="${a##*:}"
  wp eval "if(!wc_attribute_taxonomy_id_by_name('$s')){wc_create_attribute(['name'=>'$n','slug'=>'$s','type'=>'select','order_by'=>'menu_order','has_archives'=>false]);echo \"  + $n\n\";}"
done

echo "▶ Catégories produits"
for c in "Hoodies" "T-shirts" "Posters" "Collection Arabesque" "Collection Rifia" "Collection Dark Nador" "Collection Amazigh"; do
  wp term create product_cat "$c" 2>/dev/null || true
done

if [[ $IMPORT_CSV -eq 1 ]]; then
  echo "▶ Import des produits (CSV)"
  wp eval-file "$HERE/import-products.php" "$HERE/produits-nassi.csv"
fi

echo "▶ Pages"
mk() { # titre slug [contenu]
  if ! wp post list --post_type=page --name="$2" --field=ID | grep -q .; then
    wp post create --post_type=page --post_status=publish --post_title="$1" --post_name="$2" --post_content="${3:-}" >/dev/null && echo "  + $1"
  fi
}
mk "Accueil" "accueil"
mk "Contact & booking" "contact" "<p>Booking, presse, partenariats : <a href=\"mailto:contact@exemple.com\">contact@exemple.com</a></p>"
mk "CGV & CGU" "cgv" "<p>Conditions générales de vente et d’utilisation — à compléter.</p>"
mk "Politique de confidentialité" "confidentialite" "<p>À compléter.</p>"
mk "Mentions légales" "mentions-legales" "<p>À compléter.</p>"
HOME_ID=$(wp post list --post_type=page --name=accueil --field=ID)
wp option update show_on_front page
wp option update page_on_front "$HOME_ID"

echo "▶ Menus"
if ! wp menu list --fields=slug | grep -q "^principal$"; then
  wp menu create "Principal" >/dev/null
  wp menu item add-custom principal "Univers" "/#univers" >/dev/null
  wp menu item add-custom principal "Musique" "/#musique" >/dev/null
  wp menu item add-custom principal "Concert" "/#concert" >/dev/null
  SHOP_ID=$(wp option get woocommerce_shop_page_id); wp menu item add-post principal "$SHOP_ID" --title="Shop" >/dev/null
  wp menu item add-custom principal "Le Cercle" "/#cercle" >/dev/null
  wp menu location assign principal primary
fi
if ! wp menu list --fields=slug | grep -q "^infos$"; then
  wp menu create "Infos" >/dev/null
  for s in contact cgv confidentialite mentions-legales; do wp menu item add-post infos "$(wp post list --post_type=page --name=$s --field=ID)" >/dev/null; done
  wp menu location assign infos footer
fi

echo "▶ Réglages du thème (Customizer)"
wp eval '
set_theme_mod("nassi_social_youtube","https://youtu.be/WWba-sSMEQk");
set_theme_mod("nassi_social_spotify","https://open.spotify.com/intl-fr/artist/1qKKI6tBqJZCZfAmXjYFjN");
set_theme_mod("nassi_concert_url","https://dice.fm/event/l8dqkr-nassi-19th-dec-la-maroquinerie-paris-tickets?lng=fr");
set_theme_mod("nassi_concert_date","2026-12-19T20:00:00+01:00");
echo "  ok\n";'

wp rewrite flush --hard
echo
echo "✔ Terminé. Reste à faire à la main :"
echo "  1. WordPress → Gelato → « Connect to Gelato » (compte Gelato de Nassi), puis republier les produits depuis Gelato."
echo "  2. WooCommerce → Réglages → Expédition : activer « Gelato Shipping »."
echo "  3. WooCommerce → Paiements : Mollie (Bancontact, cartes, PayPal) avec la clé API live."
echo "  4. Apparence → Personnaliser → Nassi : photos, pochette, réseaux sociaux, clé API DICE si disponible."
echo "  5. Pages CGV / Confidentialité / Mentions légales : contenu définitif."
