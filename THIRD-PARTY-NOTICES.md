# Third-party code retained in this export

- `admin/js/toastify.js` identifies itself as Toastify JS 1.12.0, copyright 2018 Varun A P, MIT licensed. Its minified source header is retained. `admin/css/toastify.min.css` is the matching bundled stylesheet. Verify and include the upstream full license notice in the final package.
- PHP packages such as Sentry, Symfony HTTP Client, and Nyholm PSR-7 are declared in `composer.json`/`composer.lock` but their `vendor/` distributions are excluded. Their own licenses apply when installed.
- WordPress and WooCommerce are runtime dependencies; their distributions are not in this export.

This file does not establish a license for the first-party Panjere plugin. Confirm that separately before publication.
