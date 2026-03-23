# Verhant — Digital Product Passport (ESPR)

WordPress/WooCommerce plugin for generating and publishing **Digital Product Passports (DPP)** compliant with the EU **Ecodesign for Sustainable Products Regulation (ESPR)**.

Official integration by [Verhant](https://verhant.com) — the European platform for DPP generation and management.

---

## What it does

This plugin connects your WooCommerce store to Verhant, enabling you to:

- **Import** your product catalog to Verhant with one click
- **Generate** ESPR-compliant Digital Product Passports on the Verhant platform
- **Export** DPP links back to WooCommerce and attach them to product pages automatically
- **Display** a branded DPP badge on each product page, linking customers to the full passport
- **Monitor** DPP status for every product directly from the WordPress admin

## What is ESPR?

The [Ecodesign for Sustainable Products Regulation (ESPR)](https://commission.europa.eu/energy-climate-change-environment/standards-tools-and-labels/products-labelling-rules-and-requirements/sustainable-products/ecodesign-sustainable-products-regulation_en) is an EU regulation requiring many product categories to carry a **Digital Product Passport** — a standardized digital record containing information about a product's composition, origin, repairability, recyclability, and environmental impact.

ESPR applies to: **textiles, footwear, furniture, electronics, batteries, tyres**, and more categories being added progressively.

## Requirements

| Requirement | Version |
|-------------|---------|
| WordPress   | 6.0+    |
| WooCommerce | 7.0+    |
| PHP         | 8.0+    |

You also need a [Verhant account](https://verhant.com) (free tier available).

## Installation

### From WordPress.org (recommended)

1. Go to **Plugins → Add New** in your WordPress admin
2. Search for **"Verhant DPP"**
3. Click **Install Now**, then **Activate**
4. Go to **WooCommerce → Verhant DPP** and enter your API token

### Manual installation

1. Download or clone this repository
2. Upload the contents to `/wp-content/plugins/verhant-dpp/`
3. Activate through the **Plugins** screen
4. Configure at **WooCommerce → Verhant DPP**

## Configuration

1. Get your API token from [verhant.com/settings/api](https://verhant.com/settings/api)
2. Go to **WooCommerce → Verhant DPP** in your WordPress admin
3. Paste the token and click **Save Settings**
4. Click **Verify Connection** to confirm it works

## Usage

### Import products to Verhant

Go to **WooCommerce → Verhant Sync** and click **Start Import**. All published WooCommerce products will be sent to Verhant with their name, description, SKU, barcode, categories, and images.

Product categories are automatically mapped to Verhant verticals:

| WooCommerce Category | Verhant Vertical |
|---------------------|-----------------|
| Clothing, Apparel, Fashion | Textiles |
| Shoes, Footwear | Footwear |
| Furniture, Home | Furniture |
| Electronics, Tech | Electronics |
| Batteries | Batteries |
| Tires, Tyres | Tyres |

### Export DPP links to WooCommerce

After Verhant generates your DPPs, go to **Verhant Sync** and click **Start Export**. DPP links are saved to each product and a badge appears on the product page automatically.

### Automatic sync

Enable **"Automatically sync when you save a WooCommerce product"** on the Sync page. Every time you create or update a product, it will be sent to Verhant automatically.

### DPP badge

Once a product has a DPP link, a badge is displayed after the product summary:

> **Digital Product Passport**
> ESPR compliant

The badge links to the full DPP on Verhant. You can customize its appearance with CSS targeting the `.verhant-dpp-badge` class.

### Admin products list

A **DPP** column is added to the WooCommerce products list showing:

- **✓ Generated** — DPP is ready and linked
- **○ Pending** — Product synced, DPP in progress
- **— Not synced** — Product not yet sent to Verhant

## Plugin structure

```
├── verhant-dpp.php              # Main plugin file (header + bootstrap)
├── readme.txt                   # WordPress.org readme
├── uninstall.php                # Cleanup on uninstall
├── LICENSE                      # GPL-2.0
├── includes/
│   ├── class-verhant-api.php    # HTTP client for api.verhant.com
│   ├── class-verhant-admin.php  # Admin pages, settings, AJAX handlers
│   ├── class-verhant-sync.php   # Import/export logic
│   └── class-verhant-dpp.php    # DPP badge + admin column
├── admin/
│   ├── views/
│   │   ├── settings-page.php    # Settings page template
│   │   └── sync-page.php        # Sync page template
│   └── assets/
│       ├── verhant-admin.js     # Vanilla JS for AJAX
│       └── verhant-admin.css    # Admin styles
├── languages/
│   ├── verhant-dpp.pot          # Translation template
│   ├── verhant-dpp-it_IT.po     # Italian
│   ├── verhant-dpp-de_DE.po     # German
│   ├── verhant-dpp-fr_FR.po     # French
│   └── verhant-dpp-es_ES.po     # Spanish
└── assets/
    └── verhant-logo.svg         # Logo
```

## Translations

The plugin ships with translations for:

- 🇮🇹 Italian
- 🇩🇪 German
- 🇫🇷 French
- 🇪🇸 Spanish

To generate the `.pot` template:

```bash
wp i18n make-pot . languages/verhant-dpp.pot --domain=verhant-dpp
```

## Security

- All forms protected with WordPress nonces
- All inputs sanitized (`sanitize_text_field`, `esc_url_raw`)
- All outputs escaped (`esc_html`, `esc_attr`, `esc_url`)
- Capability checks (`manage_woocommerce`) on every admin page and AJAX endpoint
- API token stored securely, never exposed in frontend HTML
- All API communication over HTTPS
- No customer or order data is ever sent to Verhant — only product catalog data

## Privacy

This plugin sends **product catalog data only** (name, description, SKU, barcode, categories, images) to the Verhant API at `api.verhant.com`. No customer data, order data, or personal information is transmitted.

See the [Verhant Privacy Policy](https://verhant.com/privacy) for details on how product data is processed.

## Development

See [DEVELOPMENT.md](DEVELOPMENT.md) for local setup, testing, and WordPress.org submission instructions.

## License

[GPL-2.0-or-later](LICENSE)

Copyright (c) Verhant — [verhant.com](https://verhant.com)
