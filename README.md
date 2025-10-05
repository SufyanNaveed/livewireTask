# Livewire Dynamic Data Table

## Requirements
- PHP 8.1+
- Composer
- Laravel 10+
- MySQL / Postgres / SQLite

## Install & run
1. `composer install`
2. configure `.env` DB connection
3. `php artisan migrate`
4. `php artisan db:seed --class=ItemSeeder`
5. `php artisan serve`
6. Visit `http://127.0.0.1:8000/items`

## What is implemented
- Dynamic columns via `:columns` prop
- Dynamic filters via `:filter-config` prop (text, number, date)
- Soft-deleted handling (`without|with|only`) via prop
- Add new item form (toggleable), validation included
- Filters are serialized in URL and the URL is reusable

## Files of interest
- `app/Http/Livewire/DynamicDataTable.php`
- `resources/views/livewire/dynamic-data-table.blade.php`
- `app/Models/Item.php`
- `database/migrations/...create_items_table.php`
- `database/seeders/ItemSeeder.php`
