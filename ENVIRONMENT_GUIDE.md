# Environment Configuration Guide

## Environment Files Overview

This project contains multiple environment configuration files. Use the appropriate file based on your deployment environment.

## File Usage

### `.env` (Local Development)
- **Purpose:** Local development environment
- **Usage:** Copy from `.env.example` and configure for local development
- **Database:** SQLite or local MySQL/MariaDB
- **Cache:** Database or file cache
- **Debug Mode:** Enabled (`APP_DEBUG=true`)

### `.env.example` (Template)
- **Purpose:** Template for creating new environment files
- **Usage:** Copy this file to create `.env` for local development
- **Contains:** All required environment variables with example values

### `.env.production.remote` (Production Server)
- **Purpose:** Production environment on remote server
- **Usage:** Deploy to production server
- **Database:** Production MySQL/MariaDB
- **Cache:** Redis (optimized for production)
- **Session:** Redis (optimized for production)
- **Debug Mode:** Disabled (`APP_DEBUG=false`)

### `.env.remote` (Remote Development)
- **Purpose:** Remote development/staging environment
- **Usage:** Deploy to staging server
- **Database:** Staging MySQL/MariaDB
- **Cache:** Database or Redis
- **Debug Mode:** Can be enabled for debugging

## Production Configuration

### Cache Configuration
Production environment automatically uses Redis for cache and session storage for optimal performance:

```php
// config/cache.php
'default' => env('CACHE_STORE', env('APP_ENV') === 'production' ? 'redis' : 'database'),

// config/session.php  
'driver' => env('SESSION_DRIVER', env('APP_ENV') === 'production' ? 'redis' : 'database'),
```

### Required Production Environment Variables

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://alumni-steman.my.id

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=steman_alumni
DB_USERNAME=app_user
DB_PASSWORD=your_secure_password

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache & Session
CACHE_STORE=redis
SESSION_DRIVER=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@alumni-steman.my.id
MAIL_FROM_NAME="Steman Alumni"
```

## Security Notes

1. **Never commit `.env` files** to version control
2. **Use strong passwords** for production database
3. **Disable debug mode** in production (`APP_DEBUG=false`)
4. **Use HTTPS** in production
5. **Rotate secrets** regularly (API keys, passwords)
6. **Limit file permissions** on environment files (600)

## Deployment Steps

1. Copy appropriate environment file:
   ```bash
   cp .env.production.remote .env
   ```

2. Update production values in `.env`

3. Generate application key:
   ```bash
   php artisan key:generate
   ```

4. Clear and cache configuration:
   ```bash
   php artisan config:clear
   php artisan config:cache
   php artisan route:clear
   php artisan route:cache
   ```

5. Run migrations:
   ```bash
   php artisan migrate --force
   ```

## Troubleshooting

### Cache Issues
If cache issues occur in production:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Session Issues
If users are being logged out unexpectedly:
- Check Redis connection
- Verify session lifetime in `.env` (`SESSION_LIFETIME`)
- Check session domain configuration

### Database Issues
If database connection fails:
- Verify database credentials in `.env`
- Check database container is running (`docker ps`)
- Test database connection: `php artisan db:show`
