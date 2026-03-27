# eternal-product-meta — Project Rules

WordPress plugin: registers custom taxonomies and product meta fields for the Eternal Labs storefront. Provides a Gutenberg-native sidebar in the product editor. No cart logic, no pricing — content and display only.

---

## Stack

| Layer | Technology |
|---|---|
| PHP | 8.3 — WordPress plugin APIs |
| JS/JSX | React via `@wordpress/scripts` (Webpack 5) |
| Linter (PHP) | PHPCS 3.x + WordPress Coding Standards 3.x |
| Static analysis (PHP) | PHPStan level 6 + szepeviktor/phpstan-wordpress + WooCommerce stubs |
| Linter (JS) | ESLint via `@wordpress/eslint-plugin` recommended |
| Formatter (JS/CSS) | Prettier 3.x |
| CSS linter | Stylelint 16.x + stylelint-config-standard |
| Build | `@wordpress/scripts` ^30 (wp-scripts) |
| Package manager | npm |
| Dependency manager (PHP) | Composer |
| Git hooks | Husky 9 + lint-staged |

---

## Directory Structure

```
eternal-product-meta/
├── eternal-product-meta.php   # Bootstrap — constants, init hook, editor enqueue
├── inc/
│   ├── class-taxonomies.php   # Registers product-type, skin-type, product-benefit
│   └── class-meta-registration.php  # Registers all 18 post meta fields
├── src/
│   ├── index.js               # Gutenberg plugin entry — registerPlugin
│   └── components/
│       ├── PanelIdentity.jsx
│       ├── PanelFragrance.jsx
│       ├── PanelIngredients.jsx
│       ├── PanelHowToUse.jsx
│       ├── PanelIngredientCards.jsx
│       └── PanelEditorial.jsx
├── src/build/                 # Compiled output — never edit manually
├── phpcs.xml                  # PHPCS ruleset (WordPress + exclusions)
├── phpstan.neon               # PHPStan config (level 6, WC stubs)
├── .eslintrc.json             # ESLint (@wordpress/eslint-plugin recommended)
├── .prettierrc                # Prettier config
├── .stylelintrc.json          # Stylelint config
└── .husky/pre-commit          # Runs lint-staged on every commit
```

---

## PHP Rules

- **Standard:** WordPress Coding Standards (`phpcs.xml`)
- **Indentation:** Tabs (not spaces)
- **Array syntax:** Long form `array()` — WordPress standard requires it
- **Callbacks:** Always use `array( $this, 'method' )` for hooks, not arrow functions or short closures
- **Sanitisation:** All `register_post_meta()` fields must have a `sanitize_callback`. Never save unsanitised data.
- **Escaping:** All output must be escaped — `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()` as appropriate
- **Nonces:** Not required inside `register_post_meta` — WP REST API handles nonce verification
- **DocBlocks:** Required on every class and every public/private method
- **File headers:** Every PHP file must have an `@package EternalProductMeta` DocBlock
- **No dead code:** No `var_dump`, `print_r`, `error_log` left in committed code
- **PHP version:** Target PHP 8.3 — use typed properties, union types, named arguments where they improve clarity

### PHPStan

Level 6. Runs against `inc/` and `eternal-product-meta.php`. WooCommerce stubs provide type definitions for WC functions.

Run: `npm run lint:phpstan`

---

## JavaScript / JSX Rules

- **Imports:** Only `@wordpress/*` packages — never `react` directly
- **Meta reads/writes:** Always use `useEntityProp( 'postType', 'product', 'field_key' )` from `@wordpress/core-data`
- **Guard:** All panels must check `useSelect( select => select('core/editor').getCurrentPostType() ) === 'product'` — do not render on non-product post types
- **Components:** Use only `@wordpress/components` (`TextControl`, `TextareaControl`, `Button`, `ColorPicker`, `Notice`) and `@wordpress/block-editor` (`MediaUpload`, `MediaUploadCheck`)
- **No inline styles:** Use WordPress component props or CSS classes
- **JSON meta fields:** `product_ingredient_cards` stores a JSON string — always parse on read, serialise on write, never store a raw JS object
- **Formatter:** Prettier handles formatting — run `npm run format` before committing

### Build

Source: `src/index.js` → Output: `src/build/index.js` + `src/build/index.asset.php`

- `npm run build` — production build
- `npm run start` — watch mode for development
- **Never commit `src/build/`** — it is in `.gitignore`. The build runs on the server.

---

## CSS Rules

- Stylelint enforces `stylelint-config-standard`
- `selector-class-pattern` is disabled — WordPress BEM class names are used
- Run: `npm run lint:style`

---

## Quality Gate

`npm run ai:check` must pass before every merge.

```
npm run lint:php       # PHPCS — WordPress coding standards
npm run lint:phpstan   # PHPStan level 6
npm run format:check   # Prettier (check only, no write)
npm run lint:js        # ESLint via wp-scripts
```

All four must exit 0. If any fails, fix before committing.

---

## Git Hooks (Husky + lint-staged)

Pre-commit hook runs automatically on `git commit`. Staged files only:

| File pattern | Check |
|---|---|
| `src/**/*.{js,jsx}` | Prettier format + ESLint |
| `src/**/*.{css,scss}` | Stylelint --fix |
| `**/*.php` | PHPCS |

To bypass in an emergency (not recommended): `git commit --no-verify`

---

## Plugin Boundaries

This plugin is **content and display only**. Never add:
- Cart price overrides → belongs in `eternal-subscription`
- Per-currency pricing → belongs in `custom-multi-currency`
- Recurring billing of any kind

If a new field is needed, add it to `inc/class-meta-registration.php` and create a corresponding panel component in `src/components/`.

---

## Key Hooks

| Hook | File | Purpose |
|---|---|---|
| `woocommerce_loaded` | `eternal-product-meta.php` | Loads inc/ classes after WC is ready |
| `init` (priority 10) | `class-taxonomies.php` | Registers 3 custom taxonomies |
| `init` (priority 10) | `class-meta-registration.php` | Registers 18 meta fields |
| `enqueue_block_editor_assets` | `eternal-product-meta.php` | Enqueues `src/build/index.js` in block editor |

---

## REST API

All 18 meta fields are exposed at:
`GET /wp-json/wp/v2/product/<id>` → `meta` object

All 3 taxonomies are exposed at:
`GET /wp-json/wp/v2/product-type`, `/skin-type`, `/product-benefit`

---

## Related Plugin

`eternal-subscription` — `https://github.com/mrvedmutha/wp-product-subscription-multiplier`
