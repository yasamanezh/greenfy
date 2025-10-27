# راهنمای راه‌اندازی سریع

## مشکل فعلی:
خطا می‌دهد چون جدول `user_websites` در دیتابیس `larasmart` وجود ندارد. 
اما API باید در دیتابیس پروژه `new` به دنبال اطلاعات بگردد!

## راه حل:

### 1. در پروژه `new`:

```bash
# برو به پوشه new
cd d:\project\new

# اجرای migration و seeder
php artisan migrate:fresh --seed
```

این دستور:
- تمام جدول‌ها را می‌سازد
- کاربران، پلن‌ها، پکیج‌ها، سایت‌ها را ایجاد می‌کند

### 2. اجرای سرور پروژه new:

```bash
php artisan serve
# یا اگر پورت دیگری می‌خواهی:
php artisan serve --port=8001
```

### 3. در پروژه larasmart:

در فایل `.env` اضافه کن:
```env
API_BASE_URL=http://new.test/api
```

یا اگر پروژه new روی پورت دیگری است:
```env
API_BASE_URL=http://localhost:8001/api
```

### 4. دامین‌های قابل استفاده:

بعد از اجرای seeder، این دامین‌ها ایجاد می‌شوند:

- `site-1-1.example.com` یا `site-1-1` (کاربر 1 - سایت 1)
- `site-1-2.example.com` یا `site-1-2` (کاربر 1 - سایت 2)
- `site-2-1.example.com` یا `site-2-1` (کاربر 2 - سایت 1)
- `site-2-2.example.com` یا `site-2-2` (کاربر 2 - سایت 2)

### 5. تست API:

```bash
# تست لیست پلن‌ها
curl http://new.test/api/plans

# تست اطلاعات سایت
curl http://new.test/api/websites/site-1-1.example.com

# یا با subdomain
curl http://new.test/api/websites/site-1-1
```

## نکات مهم:

1. **دو پروژه**: `larasmart` و `new` دو پروژه جداگانه هستند
2. **API در new**: تمام API ها در پروژه `new` اجرا می‌شوند
3. **فراخوانی از larasmart**: پروژه `larasmart` به API های `new` درخواست می‌زند
4. **دامین متفاوت**: دامین هایی که در larasmart استفاده می‌کنید با دامین هایی که در seeder ساخته شده متفاوت هستند

## راه حل سریع:

برای تست سریع، می‌توانید از دامین هایی که در seeder ساخته شده‌اند استفاده کنید:

```php
// در loadWebsiteData
$domain = 'site-1-1.example.com'; // به جای larasmart.test
```

یا

```php
// در loadWebsiteData  
$domain = 'site-1-1'; // subdomain
```

