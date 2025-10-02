# Copilot Instructions - Thème WordPress Siklane# Copilot Instructions - Thème WordPress Siklane



## Architecture Générale## Architecture Générale



Ce projet est un thème WordPress personnalisé basé sur **Understrap 1.2.4** (framework combinant Underscores + Bootstrap). Il s'agit d'un thème e-commerce avec intégration WooCommerce pour le projet "Silk Lane".Ce projet est un thème WordPress personnalisé basé sur **Understrap 1.2.4** (framework combinant Underscores + Bootstrap). Il s'agit d'un thème e-commerce avec intégration WooCommerce pour le projet "Silk Lane".



### Structure Clé### Structure Clé



- **Base Framework**: Understrap 1.2.4 (Underscores + Bootstrap)- **Base Framework**: Understrap 1.2.4 (Underscores + Bootstrap)

- **E-commerce**: Intégration WooCommerce complète avec templates personnalisés- **E-commerce**: Intégration WooCommerce complète avec templates personnalisés

- **Build System**: NPM/Sass avec compilation Bootstrap 4/5 conditionnelle- **Build System**: NPM/Sass avec compilation Bootstrap 4/5 conditionnelle

- **Assets**: CSS/JS minifiés avec sourcemaps, fonts FontAwesome incluses- **Assets**: CSS/JS minifiés avec sourcemaps, fonts FontAwesome incluses

- **Qualité Code**: PHP_CodeSniffer (WordPress standards), PHPStan niveau max, PHPMD- **Qualité Code**: PHP_CodeSniffer (WordPress standards), PHPStan niveau max, PHPMD



## Modularité du Code## Modularité du Code



### Organisation des Fichiers PHP### Organisation des Fichiers PHP



``````

functions.php -> Point d'entrée principal, charge tous les modules depuis /inc/functions.php -> Point d'entrée principal, charge tous les modules depuis /inc/

inc/inc/

  ├── setup.php          # Configuration thème et supports WordPress  ├── setup.php          # Configuration thème et supports WordPress

  ├── enqueue.php        # Gestion assets CSS/JS  ├── enqueue.php        # Gestion assets CSS/JS

  ├── woocommerce.php    # Intégration WooCommerce (700+ lignes)  ├── woocommerce.php    # Intégration WooCommerce (700+ lignes)

  ├── customizer.php     # Options thème WordPress  ├── customizer.php     # Options thème WordPress

  └── class-wp-bootstrap-navwalker.php  # Navigation Bootstrap  └── class-wp-bootstrap-navwalker.php  # Navigation Bootstrap

``````



Le système de chargement modulaire dans `functions.php` utilise un tableau `$understrap_includes` pour charger conditionnellement les fonctionnalités (WooCommerce, Jetpack).Le système de chargement modulaire dans `functions.php` utilise un tableau `$understrap_includes` pour charger conditionnellement les fonctionnalités (WooCommerce, Jetpack).



### Architecture WooCommerce### Architecture WooCommerce



- Templates WooCommerce surchargés dans `/woocommerce/`- Templates WooCommerce surchargés dans `/woocommerce/`

- Composants réutilisables dans `/components/` (ex: `card-product-item.php`)- Composants réutilisables dans `/components/` (ex: `card-product-item.php`)

- Support galerie produit, zoom, lightbox via `inc/woocommerce.php`- Support galerie produit, zoom, lightbox via `inc/woocommerce.php`

- Panier drawer personnalisé (`cart-drawer.php`)- Panier drawer personnalisé (`cart-drawer.php`)



## Workflows de Développement## Workflows de Développement



### Build Assets### Build Assets



```powershell```powershell

# CSS principal (Bootstrap 5 par défaut)# CSS principal (Bootstrap 5 par défaut)

npm run css              # Compile Sass -> CSS + PostCSS + Minifynpm run css              # Compile Sass -> CSS + PostCSS + Minify



# CSS Bootstrap 4 (version alternative)  # CSS Bootstrap 4 (version alternative)  

npm run css-bs4          # Version Bootstrap 4 avec support spécifiquenpm run css-bs4          # Version Bootstrap 4 avec support spécifique



# JavaScript (Rollup + Terser avec sourcemaps)# JavaScript (Rollup + Terser avec sourcemaps)

npm run js               # ES6 modules -> UMD bundle minifiénpm run js               # ES6 modules -> UMD bundle minifié



# Développement avec live-reload# Développement avec live-reload

npm run bs               # BrowserSync sur fichiers Sass/JSnpm run bs               # BrowserSync sur fichiers Sass/JS

npm run watch            # Watch mode CSS + JS simultanénpm run watch            # Watch mode CSS + JS simultané

npm run watch-bs         # Watch + BrowserSync combinésnpm run watch-bs         # Watch + BrowserSync combinés



# Distribution complète# Distribution complète

npm run dist             # CSS + JS optimisés pour productionnpm run dist             # CSS + JS optimisés pour production

``````



### Qualité de Code### Qualité de Code



```powershell```powershell

# PHP_CodeSniffer (standards WordPress)# PHP_CodeSniffer (standards WordPress)

vendor/bin/phpcs         # Analyse complète selon phpcs.xml.distvendor/bin/phpcs         # Analyse complète selon phpcs.xml.dist



# PHPStan (analyse statique niveau max)# PHPStan (analyse statique niveau max)

vendor/bin/phpstan analyze  # Utilise phpstan.neon.dist + baselinevendor/bin/phpstan analyze  # Utilise phpstan.neon.dist + baseline



# Scripts de debug JavaScript # Scripts de debug JavaScript 

# debug-test.js et submenu-debug.js pour tester fonctionnalités frontend# debug-test.js et submenu-debug.js pour tester fonctionnalités frontend

``````



### Système de Versioning Bootstrap### Système de Versioning Bootstrap



Le thème supporte Bootstrap 4 et 5 via:Le thème supporte Bootstrap 4 et 5 via:



- Theme customizer: `understrap_bootstrap_version` (bootstrap4/bootstrap5)- Theme customizer: `understrap_bootstrap_version` (bootstrap4/bootstrap5)

- Assets séparés: `theme.css` (BS5) vs `theme-bootstrap4.css` (BS4)- Assets séparés: `theme.css` (BS5) vs `theme-bootstrap4.css` (BS4)

- Configuration conditionnelle dans `header.php`: `$bootstrap_version`- Configuration conditionnelle dans `header.php`: `$bootstrap_version`



### Enqueue Strategy### Enqueue Strategy



Dans `inc/enqueue.php`:Dans `inc/enqueue.php`:



- CSS: Version détectée dynamiquement (.min en production)- CSS: Version détectée dynamiquement (.min en production)

- JS: Chargement conditionnel Bootstrap + scripts personnalisés  - JS: Chargement conditionnel Bootstrap + scripts personnalisés  

- CDN: Swiper.js, Bootstrap Icons via jsdelivr- CDN: Swiper.js, Bootstrap Icons via jsdelivr

- Custom: `submenu-boutique.js` pour navigation, scripts debug temporaires- Custom: `submenu-boutique.js` pour navigation, scripts debug temporaires

- Fonts Google: Chargement asynchrone avec fallbacks système- Fonts Google: Chargement asynchrone avec fallbacks système



## Conventions Spécifiques## Conventions Spécifiques



### Composants Réutilisables### Composants Réutilisables



Les composants dans `/components/` acceptent des paramètres via `$args`:Les composants dans `/components/` acceptent des paramètres via `$args`:



```php```php

get_template_part('components/card-product-item', null, [get_template_part('components/card-product-item', null, [

    'product' => $product,    'product' => $product,

    'post_id' => $post_id,    'post_id' => $post_id,

    'custom_data' => $value    'custom_data' => $value

]);]);

``````



### Gestion des Assets### Gestion des Assets



- **Images**: Placeholder WooCommerce par défaut dans `/wp-content/uploads/`- **Images**: Placeholder WooCommerce par défaut dans `/wp-content/uploads/`

- **Fonts**: FontAwesome local dans `/fonts/`- **Fonts**: FontAwesome local dans `/fonts/`

- **CSS**: Structure SASS avec variables Bootstrap personnalisées- **CSS**: Structure SASS avec variables Bootstrap personnalisées

- **JS**: Modules ES6 compilés via Rollup- **JS**: Modules ES6 compilés via Rollup



### WordPress Hooks Pattern### WordPress Hooks Pattern



Utilisation intensive des hooks WordPress:Utilisation intensive des hooks WordPress:



- `after_setup_theme` pour configuration- `after_setup_theme` pour configuration

- `wp_enqueue_scripts` pour assets- `wp_enqueue_scripts` pour assets

- Hooks WooCommerce surchargés dans `inc/woocommerce.php`- Hooks WooCommerce surchargés dans `inc/woocommerce.php`



### Navigation Personnalisée### Navigation Personnalisée



- `Walker_Nav_Menu_HTML` dans `functions.php` pour HTML custom- `Walker_Nav_Menu_HTML` dans `functions.php` pour HTML custom

- `submenu-boutique.js` avec MutationObserver pour contrôle CSS dynamique- `submenu-boutique.js` avec MutationObserver pour contrôle CSS dynamique

- Support Bootstrap navwalker pour navigation responsive- Support Bootstrap navwalker pour navigation responsive

- Scripts debug (`submenu-debug.js`, `debug-test.js`) pour troubleshooting navigation- Scripts debug (`submenu-debug.js`, `debug-test.js`) pour troubleshooting navigation



## Points d'Intégration## Points d'Intégration



### WooCommerce### WooCommerce



- Templates surchargés: `single-product.php`, `archive-product.php`, `cart/`, etc.- Templates surchargés: `single-product.php`, `archive-product.php`, `cart/`, etc.

- Hooks personnalisés pour formulaires Bootstrap dans `inc/woocommerce.php`- Hooks personnalisés pour formulaires Bootstrap dans `inc/woocommerce.php`

- Support galerie produit complète (zoom, lightbox, slider)- Support galerie produit complète (zoom, lightbox, slider)

- Panier drawer personnalisé (`cart-drawer.php`)- Panier drawer personnalisé (`cart-drawer.php`)



### Thème Customizer### Thème Customizer



Options disponibles via `understrap_*` theme mods:Options disponibles via `understrap_*` theme mods:



- `understrap_bootstrap_version`: Version Bootstrap- `understrap_bootstrap_version`: Version Bootstrap

- `understrap_navbar_type`: Type navigation (collapse/offcanvas)- `understrap_navbar_type`: Type navigation (collapse/offcanvas)



### Performance### Performance



- Loader plein écran sur homepage uniquement (`is_front_page()`)- Loader plein écran sur homepage uniquement (`is_front_page()`)

- Assets minifiés avec versioning automatique- Assets minifiés avec versioning automatique

- Lazy loading et optimisations images- Lazy loading et optimisations images



## Fichiers Critiques à Comprendre## Fichiers Critiques à Comprendre



1. **`functions.php`** - Point d'entrée, architecture modulaire1. **`functions.php`** - Point d'entrée, architecture modulaire

2. **`inc/woocommerce.php`** - Logique e-commerce principale2. **`inc/woocommerce.php`** - Logique e-commerce principale

3. **`inc/enqueue.php`** - Stratégie de chargement assets3. **`inc/enqueue.php`** - Stratégie de chargement assets

4. **`header.php`** - Configuration Bootstrap + loader4. **`header.php`** - Configuration Bootstrap + loader

5. **`components/card-product-item.php`** - Pattern composant réutilisable5. **`components/card-product-item.php`** - Pattern composant réutilisable

## Fichiers Critiques à Comprendre

1. **`functions.php`** - Point d'entrée, architecture modulaire
2. **`inc/woocommerce.php`** - Logique e-commerce principale
3. **`inc/enqueue.php`** - Stratégie de chargement assets
4. **`header.php`** - Configuration Bootstrap + loader
5. **`components/card-product-item.php`** - Pattern composant réutilisable
