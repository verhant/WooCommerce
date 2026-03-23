# Verhant DPP — Development Guide

## Local Development Setup

### Using Local by Flywheel

1. Download and install [Local](https://localwp.com/).
2. Create a new WordPress site (PHP 8.0+, WordPress 6.0+).
3. Install and activate WooCommerce from the WordPress plugin directory.
4. Clone or symlink this repository into `wp-content/plugins/`:
   ```bash
   ln -s /path/to/verhant-dpp /path/to/local-site/app/public/wp-content/plugins/verhant-dpp
   ```
5. Activate the plugin from the WordPress admin.

### Using wp-env

1. Install Docker and Node.js.
2. Install `@wordpress/env` globally:
   ```bash
   npm install -g @wordpress/env
   ```
3. Create a `.wp-env.json` in the plugin root:
   ```json
   {
     "core": null,
     "phpVersion": "8.0",
     "plugins": [
       ".",
       "https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip"
     ]
   }
   ```
4. Start the environment:
   ```bash
   wp-env start
   ```
5. Access the site at `http://localhost:8888` (admin: `admin` / `password`).

## Testing

### PHP Syntax Check

```bash
find . -name "*.php" -exec php -l {} \;
```

### WordPress Coding Standards

Install PHP_CodeSniffer with WordPress standards:

```bash
composer require --dev wp-coding-standards/wpcs dealerdirect/phpcodesniffer-composer-installer
./vendor/bin/phpcs --standard=WordPress verhant-dpp.php includes/ admin/views/ uninstall.php
```

### Manual Testing

1. Configure a Verhant API token in WooCommerce > Verhant DPP.
2. Click "Verify Connection" to confirm the API connection works.
3. Create several WooCommerce products with SKUs.
4. Go to WooCommerce > Verhant Sync and run an import.
5. After DPPs are generated on Verhant, run an export.
6. Verify the DPP badge appears on product pages.
7. Check the DPP column in the admin products list.

## Internationalization

### Generate the .pot file

```bash
wp i18n make-pot . languages/verhant-dpp.pot --domain=verhant-dpp
```

### Update .po files

After updating the .pot file, update each .po file:

```bash
wp i18n update-po languages/verhant-dpp.pot languages/
```

### Generate .mo files

```bash
wp i18n make-mo languages/
```

## Submitting to WordPress.org

### Prerequisites

- A WordPress.org account with plugin developer access.
- SVN client installed.

### Steps

1. Submit the plugin for review at https://wordpress.org/plugins/developers/add/.
2. Once approved, you will receive SVN access. Check out the repository:
   ```bash
   svn co https://plugins.svn.wordpress.org/verhant-dpp/ verhant-dpp-svn
   ```
3. Copy the plugin files into the `trunk/` directory:
   ```bash
   cp -r verhant-dpp/* verhant-dpp-svn/trunk/
   ```
4. Add and commit:
   ```bash
   cd verhant-dpp-svn
   svn add trunk/*
   svn ci -m "Initial release 1.0.0"
   ```
5. Tag the release:
   ```bash
   svn cp trunk tags/1.0.0
   svn ci -m "Tag version 1.0.0"
   ```

### Release Checklist

- [ ] All strings use the `verhant-dpp` text domain.
- [ ] `readme.txt` stable tag matches plugin header version.
- [ ] No development files are included (node_modules, .git, tests, etc.).
- [ ] PHP syntax check passes on all files.
- [ ] Plugin activates and deactivates without errors.
- [ ] Uninstall removes all plugin data cleanly.
