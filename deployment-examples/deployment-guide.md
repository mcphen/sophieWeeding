# Deployment Guide for Sophie Wedding Dream on Shared Hosting

This guide provides instructions for deploying the Sophie Wedding Dream application on a shared hosting environment.

## Prerequisites

- A shared hosting account with PHP 8.0+ support
- MySQL database
- SSH access (recommended but not always available on shared hosting)
- FTP/SFTP access

## Deployment Steps

### 1. Prepare Your Local Project

1. Update your `.env` file for production:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   
   DB_CONNECTION=mysql
   DB_HOST=your-database-host
   DB_PORT=3306
   DB_DATABASE=your-database-name
   DB_USERNAME=your-database-username
   DB_PASSWORD=your-database-password
   
   MAIL_MAILER=smtp
   MAIL_HOST=your-mail-host
   MAIL_PORT=587
   MAIL_USERNAME=your-mail-username
   MAIL_PASSWORD=your-mail-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=your-email@example.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

2. Generate an optimized autoload file:
   ```
   composer install --optimize-autoloader --no-dev
   ```

3. Compile assets for production:
   ```
   npm ci
   npm run build
   ```

### 2. Upload Files to Shared Hosting

#### Option A: If your hosting allows setting document root to a subdirectory

1. Upload the entire Laravel project to your hosting (e.g., to `public_html/sophiewedding/`)
2. Set the document root to the `public` folder (e.g., `public_html/sophiewedding/public/`)

#### Option B: If you cannot set a custom document root (most common scenario)

1. Create a new directory outside the public directory (e.g., `sophiewedding_core`)
2. Upload all Laravel files and directories EXCEPT the `public` directory to this folder
3. Upload the contents of the `public` directory to your public directory (e.g., `public_html`)
4. Edit the `index.php` file in your public directory to update paths (see `modified-index-example.php` for a complete example)

```php
// Change these lines
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// To point to your actual Laravel installation
require __DIR__.'/../sophiewedding_core/vendor/autoload.php';
$app = require_once __DIR__.'/../sophiewedding_core/bootstrap/app.php';
```

5. Add a `.htaccess` file to the root directory to protect sensitive files and redirect requests (see `root-htaccess-example.txt` for a complete example)

### 3. Configure the Database

1. Create a new database on your shared hosting
2. Import your database or run migrations if possible:
   ```
   php artisan migrate --force
   ```

### 4. Set File Permissions

The following directories need to be writable by the web server:
- `storage/app`
- `storage/framework`
- `storage/logs`
- `bootstrap/cache`

On shared hosting, you typically need to set permissions to 755 for directories and 644 for files:
```
find /path/to/your/laravel/storage -type d -exec chmod 755 {} \;
find /path/to/your/laravel/storage -type f -exec chmod 644 {} \;
chmod 755 /path/to/your/laravel/bootstrap/cache
```

### 5. Create Storage Symbolic Link

If your shared hosting supports SSH:
```
php artisan storage:link
```

If SSH is not available, you may need to manually create a symbolic link or copy the files from `storage/app/public` to `public/storage`.

### 6. Additional Shared Hosting Configurations

#### .htaccess for subdirectory installation

If your Laravel application is installed in a subdirectory, you might need to add the following to your `.htaccess` file in the public directory:

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /your-subdirectory/
    
    # Rest of the standard Laravel .htaccess rules...
</IfModule>
```

#### PHP Configuration

Some shared hosts might require a custom `php.ini` file in your public directory. See `php-ini-example.txt` for a comprehensive example with recommended settings:

```
memory_limit = 128M
upload_max_filesize = 10M
post_max_size = 12M
max_execution_time = 60
```

Place this file in your public directory. Some hosts may require you to name it `php.ini`, while others might use `.user.ini` or another variation. Check with your hosting provider for the correct filename.

### 7. Optimize for Shared Hosting

#### Caching Configuration

On shared hosting, it's important to optimize your application's performance. Run these commands locally before deploying, or on the server if you have SSH access:

```
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If you make changes to your configuration, routes, or views, you'll need to clear these caches:

```
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Queue Configuration

If your application uses queues, consider using the database driver instead of Redis or other services that might not be available on shared hosting:

```
QUEUE_CONNECTION=database
```

Make sure to run the queue migration:

```
php artisan queue:table
php artisan migrate
```

You may need to set up a cron job to process the queue if your hosting allows it:

```
* * * * * cd /path/to/your/project && php artisan queue:work --stop-when-empty
```

### 8. Troubleshooting Common Issues

1. **500 Internal Server Error**: Check your `.htaccess` file and ensure it's compatible with your hosting.
2. **White Screen**: Enable error reporting in your `index.php` file temporarily:
   ```php
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);
   ```
3. **File Permission Issues**: Contact your hosting provider for the correct permission settings.
4. **Symbolic Link Issues**: If you can't create symbolic links, consider using the `public` disk instead of `storage` for file uploads.

### 9. Maintenance and Updates

When updating your application:
1. Put your site in maintenance mode if possible
   ```
   php artisan down
   ```
2. Pull the latest changes
3. Run migrations if needed
   ```
   php artisan migrate --force
   ```
4. Clear and regenerate caches
   ```
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. Take your site out of maintenance mode
   ```
   php artisan up
   ```

## Conclusion

Deploying Laravel on shared hosting can be challenging, but following these steps should help you get your Sophie Wedding Dream application running smoothly. If you encounter specific issues with your hosting provider, consult their documentation or support team for guidance.
