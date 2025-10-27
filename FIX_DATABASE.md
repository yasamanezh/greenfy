# راهنمای رفع مشکل دیتابیس

## مشکل:
API در حال استفاده از دیتابیس اشتباه است (`larasmart`) به جای دیتابیس خودش (`new_db`).

## راه حل سریع:

### 1. ساخت فایل `.env` در پروژه `new`

در پوشه `d:\project\new` فایل `.env` را بسازید:

```env
APP_NAME="API"
APP_ENV=local
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://new.test

APP_LOCALE=fa
APP_FALLBACK_LOCALE=fa
APP_FAKER_LOCALE=fa_IR

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=new_db
DB_USERNAME=root
DB_PASSWORD=
```

### 2. ساخت دیتابیس

```bash
mysql -u root -p
```

سپس:
```sql
CREATE DATABASE new_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 3. تنظیم کلید

```bash
cd d:\project\new
php artisan key:generate
```

### 4. اجرای migration و seeder

```bash
php artisan migrate:fresh --seed
```

### 5. اجرای سرور

```bash
php artisan serve
```

### 6. تست

```bash
curl http://localhost:8000/api/plans
```

اگر جواب داد یعنی درست کار می‌کند!

## نکته مهم:
- پروژه `larasmart` باید از دیتابیس `larasmart` استفاده کند
- پروژه `new` باید از دیتابیس `new_db` استفاده کند
- API ها در پروژه `new` هستند و باید به دیتابیس `new_db` متصل باشند
