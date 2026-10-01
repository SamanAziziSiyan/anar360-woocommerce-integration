# Anar360 WooCommerce Integration

## Overview

WordPress plugin that reads Anar360 catalogue data and maps it into WooCommerce products.

## Context

Developed inside Panjere Studio. This is the first-party plugin only, extracted from a full WordPress site backup.

## Architecture

Plugin bootstrap registers hooks; `admin/` holds settings and views; `includes/anar-ajax-request.php` handles admin actions; `includes/helper/` handles Anar requests, mappings, and product creation.

## Technology Stack

PHP, WordPress, WooCommerce, Composer HTTP client/Sentry dependencies, admin JavaScript.

## Key Features

Activation UI, Anar API calls, category and attribute pairing, and WooCommerce product creation. Variant synchronization is not claimed.

## My Contribution

The inspected parent repository has 52 Saman-alias commits of 59, supporting substantial direct integration work. It does not establish ownership of Anar, WooCommerce, or all bundled site files.

## Collaboration

Solaiman Naderi and Mohammad Reza also contributed. API behavior and branding are external to this code.

## Repository Scope

Only `anar-panjere/wp-content/plugins/anar-woocomerce-api` first-party files were extracted. WordPress, WooCommerce, unrelated plugins, vendor, site data, original history, and unverified media/fonts are excluded.

## Setup

On a disposable WordPress/WooCommerce instance run `composer install` in this plugin directory, then install/activate it in `wp-content/plugins/`. Use test Anar credentials only.

In WordPress admin, configure activation, pair categories/attributes, then request product creation. These actions require `manage_options` and form nonces.

## Configuration

Store the Anar activation key in the plugin settings on a local test site. Optional Sentry reporting uses a locally defined `AWCA_SENTRY_DSN` constant; no DSN is bundled.

## Testing

`php tests/ajax-handlers.php` passed 23 standalone checks, including role/nonce boundaries, invalid input, API failure, and product mapping failure. A full WordPress/WooCommerce integration test with synthetic Anar data remains.

## Screenshots / Demo

No approved screenshot or public demo is included. Use synthetic data and rights-cleared visuals for a future demo.

## Security

Public-release hardening removed anonymous AJAX hooks, gated three admin writes by capability and nonce, validated mapping input, removed a literal token and unsafe cookie unserialization, and made the optional Sentry DSN local. The original deployed code is not represented as secure; historical Anar tokens and database credentials require rotation or issuer invalidation.

## Limitations

External Anar service and WooCommerce runtime are required. Bundled assets are excluded; admin styling needs review. Product variation handling remains incomplete.

## Project Status

Public source snapshot of the standalone integration plugin. A real WooCommerce/Anar integration test and asset/API rights review remain open for operational use.

## License

The plugin source header states GPL-2.0-or-later. No repository-wide `LICENSE` file has been added; dependency and third-party licenses remain separate.

## GitHub metadata

**Description:** First-party WordPress plugin for Anar360 catalogue mapping into WooCommerce.

**Topics:** wordpress-plugin, woocommerce, php, ecommerce, integration

**Subtitle/tagline:** Catalogue mapping from Anar360 to WooCommerce.

**Suggested pinned-profile description:** WooCommerce integration with Anar catalogue mapping and hardened admin AJAX boundary.
