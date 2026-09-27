# النشر على الخادم — Deployment

## المتطلبات

| المكوّن | الحد الأدنى |
|---|---|
| PHP | **8.3** أو أحدث. قفل الاعتماديات مضبوط على 8.3 (`config.platform.php`). |
| امتدادات PHP | `pdo_mysql`, `mbstring`, `intl`, `fileinfo`, `gd` بدعم **WebP**, `exif`, `openssl`, `tokenizer`, `xml`, `ctype`, `bcmath` |
| قاعدة البيانات | MySQL 8+ أو MariaDB 10.6+، بترميز `utf8mb4_unicode_ci` |
| خادم الويب | Nginx أو Apache، ويجب أن يشير جذر الموقع (Document root) إلى مجلد **`public/`** |
| HTTPS | إلزامي في الإنتاج |

ملفات الواجهة المبنية (`public/build`) مرفقة مع الشيفرة، فلا يلزم Node.js على الخادم.

## خطوات النشر (خادم VPS مع SSH)

```bash
# 1) الشيفرة والاعتماديات
git clone <repo> /var/www/n-alqaseem && cd /var/www/n-alqaseem
composer install --no-dev --optimize-autoloader

# 2) البيئة
cp .env.example .env
php artisan key:generate
#   حرّر .env: APP_ENV=production، APP_DEBUG=false، APP_URL=https://n-alqaseem.com،
#   بيانات قاعدة البيانات، SESSION_SECURE_COOKIE=true، وإعدادات البريد عند الحاجة.

# 3) قاعدة البيانات والمحتوى الأولي (يعالج صور السلايدر المرخّصة تلقائياً)
php artisan migrate --force
php -d memory_limit=512M artisan db:seed --force

# 4) روابط التخزين والصلاحيات
php artisan storage:link
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# 5) إنشاء حساب المدير (إدخال تفاعلي مخفي، دون كلمة مرور افتراضية)
php artisan admin:create

#    الملف التعريفي: يُنشر Web_profile.pdf (في مجلد المشروع) تلقائياً أثناء db:seed؛
#    ولتحديثه لاحقاً: استبدل الملف ثم
php artisan profile:import

# 6) تسريع التشغيل
php artisan optimize
```

بعد كل تحديث للشيفرة:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```

إعادة تشغيل `db:seed` آمنة، فلا تكرر البيانات ولا تستبدل التعديلات المجراة من لوحة الإدارة.

### إعداد Nginx (مثال)

```nginx
server {
    listen 443 ssl http2;
    server_name n-alqaseem.com www.n-alqaseem.com;
    root /var/www/n-alqaseem/public;
    index index.php;

    client_max_body_size 24M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # الملفات المرفوعة لا تُنفَّذ أبداً
    location ^~ /storage/ {
        location ~* \.(php|phtml|phar|pl|py|cgi|sh|svg|html?)$ { return 403; }
        add_header X-Content-Type-Options nosniff;
        expires 30d;
        try_files $uri =404;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known) { deny all; }
}
```

إعدادات PHP المقترحة (`php.ini`): `upload_max_filesize = 20M`، `post_max_size = 24M`، `memory_limit = 256M` (أو 512M لمعالجة الصور الكبيرة)، `expose_php = Off`.

## الاستضافة المشتركة (cPanel)

1. أنشئ قاعدة بيانات MySQL ومستخدماً لها من cPanel.
2. **على جهازك** جهّز الحزمة:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
   ثم ارفع المشروع كاملاً (بما فيه `vendor/` و`public/build/`) إلى مجلد **خارج** `public_html`، مثل `~/n-alqaseem`.
3. **جذر الموقع:**
   - إن أمكن، غيّر Document Root للنطاق إلى `~/n-alqaseem/public`.
   - وإلا، انقل محتويات `public/` إلى `public_html/`، وعدّل مسارين في `public_html/index.php`:
     ```php
     require __DIR__.'/../n-alqaseem/vendor/autoload.php';
     $app = require_once __DIR__.'/../n-alqaseem/bootstrap/app.php';
     ```
4. أنشئ `.env` من `.env.example` واضبط القيم (`APP_ENV=production`، `APP_DEBUG=false`، `APP_KEY`، قاعدة البيانات، `SESSION_SECURE_COOKIE=true`).
5. إذا توفرت «Terminal» في cPanel فشغّل أوامر الخطوات 3–6 أعلاه. وإن لم تتوفر:
   - نفّذ الترحيل والتعبئة على جهازك مقابل نسخة محلية من القاعدة، ثم صدّرها (`mysqldump`) واستوردها من phpMyAdmin.
   - انسخ مجلد `storage/app/public/media` إلى الخادم.
   - أنشئ الرابط `public_html/storage → ../n-alqaseem/storage/app/public` من مدير الملفات أو عبر مهمة Cron لمرة واحدة: `ln -s ~/n-alqaseem/storage/app/public ~/public_html/storage`.
6. اختر إصدار PHP 8.3+ من «MultiPHP Manager»، وفعّل امتدادات `gd` و`intl` و`fileinfo` و`exif`.
7. ملف `storage/app/public/.htaccess` المرفق يمنع تنفيذ أي سكربت داخل مجلد الملفات المرفوعة على Apache.

## البريد (Microsoft 365)

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=info@n-alqaseem.com
MAIL_PASSWORD=********
MAIL_FROM_ADDRESS=info@n-alqaseem.com
CONTACT_NOTIFY=true
```

يجب تفعيل **Authenticated SMTP** للصندوق من مركز إدارة Microsoft 365. يُرسل الإشعار من الصندوق نفسه، ويُضبط «الرد إلى» على بريد الزائر. فشل الإرسال لا يؤثر على حفظ الرسالة، ويُسجَّل في `storage/logs`.

## الحماية الإضافية الاختيارية

- **Cloudflare Turnstile** لنموذج التواصل: أضف `TURNSTILE_SITE_KEY` و`TURNSTILE_SECRET_KEY` في `.env`.
- إن كان الموقع خلف Cloudflare أو موازن أحمال، اضبط الوكلاء الموثوقين (`trustProxies` في `bootstrap/app.php`) كي يعمل تحديد معدل الطلبات على عنوان الزائر الحقيقي.
- خلف HTTPS: `SESSION_SECURE_COOKIE=true`. ويمكن تفعيل `SESSION_EXPIRE_ON_CLOSE=true` لجلسات الإدارة.

## النسخ الاحتياطي

- قاعدة البيانات: `mysqldump --single-transaction noor_alqaseem > backup.sql`
- الملفات: `storage/app/public/media` (الصور) و`storage/app/private/profile` (ملفات PDF).
