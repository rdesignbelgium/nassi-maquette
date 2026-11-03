# Nassi — structure WordPress

Package d'installation du site officiel de Nassi : thème sur mesure (reprise à l'identique de la maquette), boutique WooCommerce reliée à Gelato (impression à la demande), billetterie DICE, newsletter « Le Cercle ».

Réalisé par R Design Belgium.

## Contenu

```
nassi.zip                 Thème WordPress « Nassi » (à installer via Apparence → Thèmes → Ajouter)
nassi/                    Le même thème, décompressé
setup/setup.sh            Script WP-CLI : extensions, réglages Woo, catégories, pages, menus
setup/produits-nassi.csv  14 produits (hoodies, t-shirts, posters) avec variantes Couleur × Taille
setup/import-products.php Import du CSV par l'importateur natif WooCommerce
```

## Installation rapide

1. WordPress vierge (PHP 8+), WP-CLI disponible.
2. Copier ce dossier sur le serveur, puis depuis la racine du site :
   ```bash
   bash chemin/vers/setup/setup.sh
   ```
   Le script installe et active : WooCommerce, Elementor, Gelato Integration for WooCommerce, Mollie, Yoast SEO, WP Mail SMTP ; configure la boutique (EUR, TVA incluse, pays livrés), crée les attributs Couleur / Taille / Format, les catégories, les pages (Accueil, Contact, CGV, Confidentialité, Mentions légales), les menus, et importe les 14 produits.
3. Sans WP-CLI : installer `nassi.zip` comme thème, installer les extensions à la main, puis WooCommerce → Produits → Importer → `produits-nassi.csv` (cocher « Mettre à jour les produits existants »).

## Gelato (impression à la demande)

Le CSV sert à avoir une boutique complète immédiatement. Pour que les commandes partent chez Gelato :

1. WordPress → menu **Gelato** → **Connect to Gelato** (compte Gelato de Nassi). Le site apparaît dans son dashboard Gelato comme nouvelle boutique.
2. Dans Gelato, republier chaque produit vers la boutique WooCommerce (les designs existent déjà). Gelato crée ses propres produits dans Woo, avec variantes et visuels ; supprimer ensuite les produits importés par CSV (ou lancer `setup.sh --no-csv` dès le départ).
3. WooCommerce → Réglages → Expédition → activer **Gelato Shipping** (tarifs calculés en direct, marge réglable).
4. Passer une commande test de bout en bout avant d'ouvrir.

Une fois validé : rediriger `nassi.store` vers le nouveau domaine, déconnecter la boutique Shopify dans Gelato, résilier Shopify.

## Page d'accueil

Tout se règle dans **Apparence → Personnaliser → Nassi — Accueil** : photos du hero et de la galerie, pochette et liens d'écoute, concert (date, salle, prix, lien DICE), textes de la boutique, newsletter, réseaux sociaux.

- **Billetterie DICE** : par défaut, la date est saisie à la main. Si DICE fournit une clé API partenaires, la coller dans le Customizer : les événements sont alors lus côté serveur (`/wp-json/nassi/v1/dice`, cache 30 min) et la clé n'est jamais exposée.
- **Le Cercle** : les inscriptions sont stockées dans WordPress (menu « Le Cercle », export CSV). Pour utiliser MailPoet, Brevo ou Fluent Forms, coller le shortcode du formulaire dans le Customizer.
- **Elementor** : les pages construites avec Elementor s'affichent en pleine largeur dans le thème (header et footer du thème conservés). La page d'accueil peut aussi être refaite avec Elementor : le gabarit `front-page.php` s'efface automatiquement.

## Couleurs des pastilles

Les pastilles couleur des cartes produit lisent l'attribut global **Couleur** (noir, gris, blanc, rouge, orange). Pour une nouvelle couleur, ajouter le terme dans Produits → Attributs, et le code hexadécimal via le filtre `nassi_swatch_colors`.
