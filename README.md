# TRUCEMS Lumen
```bash
git clone https://github.com/trusursidik12/trucems_lumen.git
```
```bash
cd trucems_lumen
```
```bash
cp .env.example .env
```
## Configuration
Configuration database and change the app environtment
APP_ENV=production
```bash
composer install
```
## Configuration DB Default
Change content in `vendor/laravel/lumen-framework/config/database.php`
```php
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'forge'), // Change 'forge' to DB name production
            'username' => env('DB_USERNAME', 'forge'), // Change 'forge' to DB username production
            'password' => env('DB_PASSWORD', ''), // Change '' to DB username production
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', true),
            'engine' => env('DB_ENGINE', null),
            'timezone' => env('DB_TIMEZONE', '+00:00'),
        ],
```
```bash
npm install
```
## CHMOD
```bash
chmod 0777 -R <user>:<user> storage
```
Change <user> to actualy user machine if `root` change <user> to `root`

### Updated at
7/22/2022