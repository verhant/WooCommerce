=== Verhant — Digital Product Passport (ESPR) ===
Contributors: verhant
Tags: woocommerce, ESPR, digital product passport, DPP, sustainability
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 8.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate ESPR-compliant Digital Product Passports for your WooCommerce products and publish DPP links automatically.

== Description ==

**Verhant — Digital Product Passport (ESPR)** is the official WooCommerce integration for the [Verhant](https://verhant.com) platform. It enables European e-commerce merchants to generate, manage, and publish Digital Product Passports (DPP) that comply with the EU Ecodesign for Sustainable Products Regulation (ESPR).

Starting from 2026, the European Union requires that many product categories carry a Digital Product Passport — a standardized digital record containing information about a product's composition, origin, repairability, recyclability, and environmental impact. The DPP is a cornerstone of the EU's circular economy strategy and applies to categories including textiles, footwear, furniture, electronics, batteries, and tyres.

Verhant is a European SaaS platform that simplifies DPP generation and management. This plugin connects your WooCommerce store directly to Verhant, allowing you to import your product catalog, generate compliant DPPs, and publish DPP links on your product pages — all without leaving your WordPress dashboard.

= Features =

* **One-click catalog import** — Send your entire WooCommerce product catalog to Verhant with a single click. Product names, descriptions, SKUs, barcodes, images, and categories are mapped automatically.
* **DPP link export** — Once Verhant generates your Digital Product Passports, export the DPP links back to WooCommerce and attach them to each product automatically.
* **DPP badge on product pages** — A clean, branded badge is displayed on your product pages, linking customers directly to the Digital Product Passport. Fully ESPR compliant.
* **Admin DPP column** — See at a glance which products have a generated DPP, which are pending, and which have not been synced yet, directly in your WooCommerce products list.
* **Automatic sync** — Optionally sync products to Verhant every time you create or update a product in WooCommerce.
* **Secure API connection** — Your Verhant API token is stored securely and never exposed in the frontend. All communication uses HTTPS.
* **Multi-language ready** — The plugin is fully translatable and ships with translations for Italian, German, French, and Spanish.

= What is ESPR? =

The Ecodesign for Sustainable Products Regulation (ESPR) is an EU regulation that establishes a framework for setting ecodesign requirements for sustainable products. A key component of ESPR is the Digital Product Passport (DPP), which provides standardized, machine-readable information about a product's environmental sustainability throughout its lifecycle.

The DPP must be accessible via a unique identifier (typically a QR code or URL) and must contain information about materials, manufacturing origin, repairability, recyclability, carbon footprint, and compliance with applicable EU standards.

ESPR applies to a wide range of product categories and will be rolled out progressively. Businesses selling in the EU market need to prepare their product data and digital infrastructure now to ensure compliance when the regulation takes effect for their product category.

= Who is Verhant? =

Verhant is a European technology company headquartered in Italy, focused on making regulatory compliance simple and accessible for businesses of all sizes. The Verhant platform automates the generation of Digital Product Passports, handles data validation, and ensures that your DPPs meet the latest ESPR requirements. Visit [verhant.com](https://verhant.com) to learn more.

= Requirements =

* WordPress 6.0 or higher
* WooCommerce 7.0 or higher
* PHP 8.0 or higher
* A Verhant account (free tier available at [verhant.com](https://verhant.com))

== Installation ==

1. Upload the `verhant-dpp` folder to the `/wp-content/plugins/` directory, or install the plugin directly through the WordPress plugin screen.
2. Activate the plugin through the "Plugins" screen in WordPress.
3. Go to WooCommerce > Verhant DPP to access the settings page.
4. Enter your Verhant API token (find it at [verhant.com/settings/api](https://verhant.com/settings/api)) and click "Save Settings".
5. Navigate to WooCommerce > Verhant Sync to import your products and export DPP links.

== Frequently Asked Questions ==

= Do I need a Verhant account? =

Yes. You need a Verhant account to generate Digital Product Passports. You can sign up for free at [verhant.com](https://verhant.com). A free tier is available that lets you generate DPPs for a limited number of products.

= Does the plugin work without WooCommerce? =

No. This plugin is specifically designed to integrate WooCommerce with the Verhant platform. WooCommerce must be installed and activated for the plugin to function.

= Where do I find my API token? =

Log in to your Verhant account and navigate to Settings > API. Your API token is displayed there. Copy it and paste it into the plugin settings page at WooCommerce > Verhant DPP.

= Which product categories does ESPR cover? =

ESPR applies to a wide range of product categories including textiles, footwear, furniture, electronics, batteries, and tyres. The regulation is being rolled out progressively, with different categories becoming subject to DPP requirements at different dates. The plugin automatically maps your WooCommerce product categories to the appropriate Verhant vertical.

= Is my customer data shared with Verhant? =

No. The plugin only sends product catalog data (names, descriptions, SKUs, images, categories) to Verhant. No customer data, order data, or personal information is ever transmitted to Verhant.

= Can I customize the DPP badge appearance? =

The badge uses a clean, minimal design that works with any theme. You can customize its appearance using CSS in your theme. The badge is wrapped in a `verhant-dpp-badge` class for easy targeting.

== Screenshots ==

1. Settings page — connect your Verhant account with your API token.
2. Synchronization page — import products and export DPP links.
3. DPP badge displayed on a WooCommerce product page.
4. DPP status column in the WordPress admin products list.

== Changelog ==

= 1.0.0 =
* Initial release.
* Import WooCommerce products to Verhant.
* Export DPP links from Verhant to WooCommerce.
* Display DPP badge on product pages.
* DPP status column in admin products list.
* Automatic sync on product save.
* Translations for Italian, German, French, and Spanish.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
