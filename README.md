# موقع شركة نور القسيم — Noor AlQaseem Website

موقع مؤسسي ثنائي اللغة (العربية RTL أساسية، والإنجليزية LTR تحت `/en`) مع لوحة إدارة محمية، مبني على **Laravel 13** و**MySQL** وواجهة **Blade + CSS + JavaScript** دون أطر إضافية.

A bilingual corporate website (Arabic RTL at the root, English LTR under `/en`) with a protected admin panel. Built with Laravel 13, MySQL, Blade, plain CSS and vanilla JavaScript.

---

## المحتويات

- **الصفحات:** الرئيسية، عن الشركة، خدماتنا (صفحة جامعة و10 صفحات أنشطة)، تواصل معنا، سياسة الخصوصية، الشروط والإشعار القانوني، `sitemap.xml`، `robots.txt`.
- **لوحة الإدارة `/admin`:** بيانات الشركة والتواصل بحالة «معتمد / بانتظار الاعتماد»، نصوص الصفحات، شرائح العرض، فئات الخدمات والخدمات، بيانات SEO وصور المشاركة، ملف PDF التعريفي، صندوق الرسائل، وتغيير كلمة المرور.
- **التواصل:** بالبريد الإلكتروني ونموذج التواصل فقط؛ لا يُعرض أي رقم هاتف في الموقع.
- **الملف التعريفي:** أزرار «الملف التعريفي» (رأس الصفحة، قائمة الجوال، الشريحة الأولى، قسم التعريف، التذييل) تفتح ملف `Web_profile.pdf` في نافذة جديدة. لتحديثه: استبدل الملف في مجلد المشروع ثم نفّذ `php artisan profile:import`، أو ارفعه من لوحة الإدارة.
- **المحتوى:** مأخوذ من ملفات الشركة فقط. الحقول غير المؤكدة محفوظة بحالة «بانتظار الاعتماد» ولا تُنشر (راجع [`docs/DATA-TO-CONFIRM.md`](docs/DATA-TO-CONFIRM.md)).

## التشغيل المحلي — Local setup

المتطلبات: PHP 8.3+ (مع `gd` بدعم WebP، و`intl`، و`fileinfo`، و`exif`)، وComposer، وNode.js 20+، وMySQL 8 أو MariaDB 10.6+.

```bash
composer install
cp .env.example .env
php artisan key:generate
# اضبط DB_* في .env، ثم:
php artisan migrate
php -d memory_limit=512M artisan db:seed    # المحتوى الأولي + معالجة الصور المرخّصة
php artisan storage:link                    # يُنشر Web_profile.pdf تلقائياً أثناء db:seed إن وُجد في مجلد المشروع
npm install && npm run build
php artisan admin:create                    # إنشاء حساب المدير (تفاعلي)
composer dev                                # الخادم على http://127.0.0.1:8010
```

**على هذا الجهاز (macOS):** يعمل المشروع بقاعدة MySQL محلية خاصة به في `.dev/mysql` على المنفذ 3308، مستقلة عن XAMPP وعن مشروع madar. لتشغيلها بعد إعادة التشغيل:

```bash
composer local-db   # يشغّل mysqld في الخلفية على 127.0.0.1:3308
composer dev        # خادم Laravel + Vite، مع حدود رفع 20MB من .dev/uploads.ini
```

## حساب المدير — Admin account

لا يوجد حساب أو كلمة مرور افتراضية. لإنشاء حساب:

```bash
php artisan admin:create           # يطلب البريد والاسم وكلمة مرور مخفية (12 حرفاً على الأقل، حروف وأرقام)
php artisan admin:create --update  # إعادة تعيين كلمة مرور حساب موجود
```

The login page is `/admin/login`. Login attempts are rate-limited, sessions are regenerated on login, admin pages are sent with `no-store` and `noindex`, and every change is recorded in an audit log shown on the dashboard.

## الاختبارات — Tests

```bash
php artisan test              # 74 اختبار PHPUnit على SQLite في الذاكرة
vendor/bin/pint --test        # تنسيق الشيفرة
npx playwright install chromium   # مرة واحدة
npx playwright test           # 44 اختبار متصفح + فحص axe للوصولية (يشغّل خادماً على المنفذ 8011)
```

تفاصيل ما تم التحقق منه في [`docs/QA.md`](docs/QA.md).

## الهيكل — Structure

| المسار | الوصف |
|---|---|
| `routes/web.php`, `routes/admin.php` | مسارات الموقع (مسجلة مرتين: العربية في الجذر والإنجليزية تحت `/en`) ومسارات الإدارة |
| `app/Support/LocaleUrl.php`, `app/helpers.php` | `lroute()` لروابط اللغتين، وروابط hreflang وتبديل اللغة |
| `app/Support/SiteSettings.php` | قراءة الإعدادات المعتمدة فقط (`site('key')`) |
| `app/Services/Media/*` | رفع الصور الآمن وإعادة ترميزها (WebP بعدة أحجام)، والتحقق من ملفات PDF |
| `app/Services/Contact/SpamGuard.php` | حماية نموذج التواصل (حقل مخفي، ومهلة زمنية مشفرة، وتحديد معدل الطلبات، وTurnstile اختياري) |
| `database/seeders/content/*.php` | المحتوى الأولي بالعربية والإنجليزية |
| `resources/views/components/*` | مكونات الواجهة (السلايدر، الصور المتجاوبة، البطاقات…) |
| `resources/css`, `resources/js/modules` | التصميم بخصائص منطقية تناسب RTL وLTR، ووحدات JavaScript (القائمة، السلايدر، الحركة، النموذج) |
| `tests/Feature`, `tests/browser` | اختبارات PHPUnit وPlaywright |

## الوثائق — Documentation

- [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md): النشر على VPS أو استضافة cPanel، وإعداد Nginx، والبريد، والنسخ الاحتياطي.
- [`docs/DATA-TO-CONFIRM.md`](docs/DATA-TO-CONFIRM.md): البيانات التي تحتاج تأكيداً قبل النشر الكامل.
- [`docs/IMAGE-CREDITS.md`](docs/IMAGE-CREDITS.md): مصادر الصور وتراخيصها.
- [`docs/QA.md`](docs/QA.md): تقرير التحقق مع لقطات الشاشة.

## الأمان — Security notes

- الأسرار في `.env` فقط، ولا يتضمن `.env.example` أي قيمة سرية.
- الصور المرفوعة يُتحقق من نوعها الحقيقي (finfo و`getimagesize`) ثم يُعاد ترميزها، فلا يُحفظ الملف الأصلي. لا تُقبل ملفات SVG أو GIF، ولا يُنفَّذ أي سكربت في مجلد التخزين (`storage/app/public/.htaccess`).
- ملفات PDF تُحفظ خارج المجلد العام وتُرفض إن احتوت JavaScript أو ملفات مضمّنة، ولا تُعرض إلا بعد اعتمادها.
- نصوص الإدارة تُعرض دائماً مُهرَّبة (escaped)، مع تنسيق بسيط (فقرات، وقوائم «- »، وعناوين «## »).
- ترويسات أمان وسياسة CSP صارمة (`self` فقط) في الإنتاج.
