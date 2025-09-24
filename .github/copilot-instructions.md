# Copilot Instructions - Thème WordPress Siklane

## Architecture Générale

Ce projet est un thème WordPress personnalisé basé sur **Understrap** (framework combinant Underscores + Bootstrap). Il s'agit d'un thème e-commerce avec intégration WooCommerce pour le projet "Silk Lane".

### Structure Clé

- **Base Framework**: Understrap 1.2.4 (Underscores + Bootstrap)
- **E-commerce**: Intégration WooCommerce complète avec templates personnalisés
- **Build System**: NPM/Sass avec compilation Bootstrap 4/5 conditionnelle
- **Assets**: CSS/JS minifiés avec sourcemaps, fonts FontAwesome incluses

## Modularité du Code

### Organisation des Fichiers PHP

```
functions.php -> Point d'entrée principal, charge tous les modules depuis /inc/
inc/
  ├── setup.php          # Configuration thème et supports WordPress
  ├── enqueue.php        # Gestion assets CSS/JS
  ├── woocommerce.php    # Intégration WooCommerce (700+ lignes)
  ├── customizer.php     # Options thème WordPress
  └── class-wp-bootstrap-navwalker.php  # Navigation Bootstrap
```

Le système de chargement modulaire dans `functions.php` utilise un tableau `$understrap_includes` pour charger conditionnellement les fonctionnalités (WooCommerce, Jetpack).

### Architecture WooCommerce

- Templates WooCommerce surchargés dans `/woocommerce/`
- Composants réutilisables dans `/components/` (ex: `card-product-item.php`)
- Support galerie produit, zoom, lightbox via `inc/woocommerce.php`
- Panier drawer personnalisé (`cart-drawer.php`)

## Workflows de Développement

### Build Assets

```bash
# CSS principal (Bootstrap 5 par défaut)
npm run css              # Compile Sass -> CSS + PostCSS + Minify

# CSS Bootstrap 4 (version alternative)
npm run css-bs4          # Version Bootstrap 4

# JavaScript
npm run js               # Rollup + Terser avec sourcemaps

# Développement avec live-reload
npm run bs               # BrowserSync
```

### Système de Versioning Bootstrap

Le thème supporte Bootstrap 4 et 5 via:

- Theme customizer: `understrap_bootstrap_version` (bootstrap4/bootstrap5)
- Assets séparés: `theme.css` (BS5) vs `theme-bootstrap4.css` (BS4)
- Configuration conditionnelle dans `header.php`: `$bootstrap_version`

### Enqueue Strategy

Dans `inc/enqueue.php`:

- CSS: Version détectée dynamiquement (.min en production)
- JS: Chargement conditionnel Bootstrap + scripts personnalisés
- CDN: Swiper.js, Bootstrap Icons
- Custom: `submenu-boutique.js` pour navigation spécialisée

## Conventions Spécifiques

### Composants Réutilisables

Les composants dans `/components/` acceptent des paramètres via `$args`:

```php
get_template_part('components/card-product-item', null, [
    'product' => $product,
    'post_id' => $post_id,
    'custom_data' => $value
]);
```

### Gestion des Assets

- **Images**: Placeholder WooCommerce par défaut dans `/wp-content/uploads/`
- **Fonts**: FontAwesome local dans `/fonts/`
- **CSS**: Structure SASS avec variables Bootstrap personnalisées
- **JS**: Modules ES6 compilés via Rollup

### WordPress Hooks Pattern

Utilisation intensive des hooks WordPress:

- `after_setup_theme` pour configuration
- `wp_enqueue_scripts` pour assets
- Hooks WooCommerce surchargés dans `inc/woocommerce.php`

### Navigation Personnalisée

- `Walker_Nav_Menu_HTML` dans `functions.php` pour HTML custom
- `submenu-boutique.js` avec MutationObserver pour contrôle CSS dynamique
- Support Bootstrap navwalker pour navigation responsive

## Points d'Intégration

### WooCommerce

- Templates surchargés: `single-product.php`, `archive-product.php`, `cart/`, etc.
- Hooks personnalisés pour formulaires Bootstrap dans `inc/woocommerce.php`
- Support galerie produit complète (zoom, lightbox, slider)

### Thème Customizer

Options disponibles via `understrap_*` theme mods:

- `understrap_bootstrap_version`: Version Bootstrap
- `understrap_navbar_type`: Type navigation (collapse/offcanvas)

### Performance

- Loader plein écran sur homepage uniquement (`is_front_page()`)
- Assets minifiés avec versioning automatique
- Lazy loading et optimisations images

## Fichiers Critiques à Comprendre

1. **`functions.php`** - Point d'entrée, architecture modulaire
2. **`inc/woocommerce.php`** - Logique e-commerce principale
3. **`inc/enqueue.php`** - Stratégie de chargement assets
4. **`header.php`** - Configuration Bootstrap + loader
5. **`components/card-product-item.php`** - Pattern composant réutilisable
