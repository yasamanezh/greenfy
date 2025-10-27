# راهنمای تنظیم دیتابیس

## مشکل فعلی:
پروژه `new` دارد از دیتابیس `larasmart` استفاده می‌کند!

## راه حل:

### 1. ساخت فایل .env در پروژه new:

در پوشه `d:\project\new` فایل `.env` را بسازید:

```env
APP_NAME="LaraSmart API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://new.test

APP_LOCALE=fa
APP_FALLBACK_LOCALE=fa
APP_FAKER_LOCALE=fa_IR

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# ⚠️ مهم: دیتابیس جدید
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=new_db
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=
```

### 2. ساخت دیتابیس:

```bash
# اتصال به mysql
mysql -u root -p

# ساخت دیتابیس
CREATE DATABASE new_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 3. ساخت کلید:

```bash
cd d:\project\new
php artisan key:generate
```

### 4. اجرای migration:

```bash
php artisan migrate:fresh --seed
```

### 5. اجرای سرور:

```bash
php artisan serve
```

## تست:

```bash
curl http://localhost:8000/api/plans
```

اگر خطا داد، از localhost:8000 استفاده کنید.
