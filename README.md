# Eternal Product Meta

Version 1.0.0 | PHP 8.3+ | WordPress 6.4+ | WooCommerce 8.0+

## Overview

Eternal Product Meta is a standalone WordPress plugin that registers three custom hierarchical taxonomies and eighteen product meta fields for the Eternal Labs storefront. It provides six native Gutenberg sidebar panels that let content editors manage everything from French subtitles and fragrance notes to ingredient cards and an editorial section — no Advanced Custom Fields required. The plugin is a pure content and display layer; it has no opinion about cart behaviour, pricing logic, or checkout flow.

## Requirements

- WordPress 6.4+
- WooCommerce 8.0+
- PHP 8.3+
- Node 18+ (for development build)
- Composer (for development)

## Installation

1. Clone the repository into your WordPress plugins directory:
   ```bash
   git clone https://github.com/mrvedmutha/wp-custom-product-meta \
     wp-content/plugins/eternal-product-meta
   ```
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install Node dependencies:
   ```bash
   npm install
   ```
4. Build the Gutenberg assets:
   ```bash
   npm run build
   ```
5. Log in to the WordPress admin, navigate to **Plugins**, and activate **Eternal Product Meta**.

## Taxonomies

| Taxonomy | Slug | UI Label | Example Terms |
|---|---|---|---|
| Product Type | `product-type` | Product Types | Serum, Moisturiser, Cleanser, Toner |
| Skin Type | `skin-type` | Skin Types | Oily, Dry, Combination, Sensitive, Normal |
| Product Benefit | `product-benefit` | Benefits | Anti-Ageing, Brightening, Hydration, Barrier Repair |

## Meta Fields

| Field Key | Type | Sanitization | Panel | Description |
|---|---|---|---|---|
| `product_name_fr` | string | `sanitize_text_field` | Identity | French subtitle shown beneath the product name |
| `product_eyebrow` | string | `sanitize_text_field` | Identity | Small label rendered above the product name on the PDP |
| `product_tagline` | string | `sanitize_text_field` | Identity | One-line marketing tagline |
| `product_display_tags` | string | `sanitize_text_field` | Identity | Comma-separated list of pill labels |
| `product_card_bg` | string | `sanitize_hex_color` | Identity | Hex colour for the product card background (default `#f5f5f5`) |
| `product_notes_top` | string | `sanitize_text_field` | Fragrance | Top fragrance notes |
| `product_notes_middle` | string | `sanitize_text_field` | Fragrance | Middle (heart) fragrance notes |
| `product_notes_base` | string | `sanitize_text_field` | Fragrance | Base fragrance notes |
| `product_inci` | string | `wp_kses_post` | Ingredients | Full INCI ingredient list, HTML allowed |
| `product_ingredients_disclaimer` | string | `sanitize_textarea_field` | Ingredients | Legal disclaimer shown below the ingredient list |
| `product_allergy_info` | string | `sanitize_text_field` | Ingredients | Short allergy advisory text |
| `product_how_to_use` | string | `wp_kses_post` | How To Use | Application instructions, HTML allowed |
| `product_storage_warnings` | string | `wp_kses_post` | How To Use | Storage conditions and safety warnings, HTML allowed |
| `product_dosage_instructions` | string | `sanitize_text_field` | How To Use | Recommended dosage or usage quantity |
| `product_ingredient_cards` | string | JSON validate & re-encode | Ingredient Cards | JSON array of `{ name, description, image_id }` objects |
| `product_editorial_headline` | string | `sanitize_text_field` | Editorial | Headline for the editorial feature section |
| `product_editorial_body` | string | `wp_kses_post` | Editorial | Body copy for the editorial section, HTML allowed |
| `product_editorial_image_id` | integer | `absint` | Editorial | WordPress attachment ID for the editorial section image |

## Gutenberg Sidebar Panels

- **Product Identity & Card Display** — Controls the French subtitle, eyebrow label, tagline, comma-separated display tag pills, and the card background colour picker.
- **Fragrance Notes** — Three text fields for top, middle, and base notes. All three must be populated for the Key Notes front-end block to render.
- **Ingredients & Safety** — Rich-text areas for the INCI list and ingredients disclaimer, plus a plain text field for allergy information.
- **How To Use** — Rich-text areas for application instructions and storage/warnings, plus a plain text field for dosage instructions.
- **Ingredient Cards** — A dynamic list editor. Each card has a name, description, and media-library image. Cards can be added, reordered by removing and re-adding, and deleted. The entire array is serialised as JSON.
- **Editorial Section** — Headline, rich-text body, and a media-library image picker for the editorial feature section. Leaving the headline blank suppresses the section on the storefront.

## Development Scripts

| Script | Purpose |
|---|---|
| `npm run build` | Production webpack build — outputs compiled assets to `src/build/` |
| `npm run start` | Development watch mode with hot module replacement |
| `npm run lint:js` | ESLint check over all JS and JSX source files |
| `npm run lint:style` | Stylelint check over all CSS and SCSS source files |
| `npm run lint:php` | PHPCS check against WordPress coding standards |
| `npm run lint:phpstan` | PHPStan static analysis at level 6 |
| `npm run format` | Prettier format check (add `--write` to auto-fix) |
| `npm run ai:check` | Full quality gate: PHPCS + PHPStan + Prettier + ESLint |

## Quality Gate (`npm run ai:check`)

Running `npm run ai:check` executes the full quality pipeline in sequence:

1. **PHPCS** — validates all PHP files against the WordPress coding standards ruleset defined in `phpcs.xml`.
2. **PHPStan** — performs static analysis at level 6, catching type errors and undefined variables without executing the code (configured via `phpstan.neon`).
3. **Prettier** — checks that all JS, JSX, and CSS source files match the formatting rules in `.prettierrc`. The gate fails if any file would be changed by an auto-format pass.
4. **ESLint** — lints all JS and JSX files using the rules in `.eslintrc.json`.

All four checks must pass before a pull request can be merged.

## Git Hooks

A Husky pre-commit hook is configured in `.husky/pre-commit` and runs `lint-staged` automatically on every `git commit`. The `lint-staged` configuration (in `package.json`) applies the following checks to staged files only:

- **Staged PHP files** are checked with PHPCS against WordPress coding standards.
- **Staged JS and JSX files** are formatted with Prettier and then linted with ESLint.

Fixing lint errors before they reach CI catches regressions early and keeps the commit history clean.

## Plugin Architecture

Because taxonomies and meta fields are registered inside a plugin rather than a theme, they survive theme switches and child-theme changes without data loss. Every meta field is optional — if a field is left empty the corresponding front-end UI block simply does not render, so partial data never produces broken layouts.

## Related

- [eternal-subscription — wp-product-subscription-multiplier](https://github.com/mrvedmutha/wp-product-subscription-multiplier)
