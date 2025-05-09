# Importing EDD Content

## Import database

1. Have a local sql file
2. Run: `sail mysql < path/to/db/dump.sql`
3. Data is now in MySQL `edd_import` DB. Confirm via:
4. `sail mysql`
5. `show databases`

## Set current source

Edit `config/imports.php` and set `currentSource` value.

## Run imports

Execute in this order:

### Customers

```shell
php artisan import:customers
```

### Products

```shell
php artisan import:products
```

#### Edit products to add Stripe IDs

Manually edit each product and price to add their Stripe IDs.

#### Manually add bundles

Manually create any bundles.

### Orders

```shell
php artisan import:orders
```
