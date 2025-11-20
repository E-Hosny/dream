# إعداد DigitalOcean Spaces للتخزين السحابي

## المتطلبات

تم تكوين التطبيق لاستخدام **DigitalOcean Spaces** لتخزين جميع الملفات (واجبات، حلول، تصحيحات).

## متغيرات البيئة المطلوبة

أضف المتغيرات التالية إلى ملف `.env`:

```env
# نظام الملفات الافتراضي
FILESYSTEM_DISK=spaces

# DigitalOcean Spaces Configuration
DO_SPACES_KEY=your_spaces_key_here
DO_SPACES_SECRET=your_spaces_secret_here
DO_SPACES_REGION=your_region_here
DO_SPACES_BUCKET=your_bucket_name_here
DO_SPACES_ENDPOINT=https://your_region.digitaloceanspaces.com
```

## مثال على القيم الحقيقية

```env
FILESYSTEM_DISK=spaces

DO_SPACES_KEY=D000QDHMV3FVZAKEXEBU
DO_SPACES_SECRET=UuVCpYftOo7lyJBDwT0zer4FQtG5IcZjL+LdM2iF8ug
DO_SPACES_REGION=sfo3
DO_SPACES_BUCKET=inskola-files
DO_SPACES_ENDPOINT=https://sfo3.digitaloceanspaces.com
```

## خطوات الإعداد

1. **إنشاء Space في DigitalOcean**
   - اذهب إلى [DigitalOcean Spaces](https://cloud.digitalocean.com/spaces)
   - أنشئ Space جديد واختر Region
   - احفظ اسم الـ Bucket

2. **إنشاء API Keys**
   - اذهب إلى API → Spaces Keys
   - أنشئ مفتاح جديد
   - احفظ Access Key و Secret Key

3. **تحديث ملف .env**
   - أضف جميع المتغيرات المذكورة أعلاه
   - تأكد من صحة القيم

4. **تنظيف الـ Cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

## الملفات التي يتم تخزينها في Spaces

- **assignments/**: ملفات الواجبات التي يرفعها المعلمون
- **submissions/**: حلول الواجبات التي يرفعها الطلاب
- **corrections/**: ملفات التصحيح التي يرفعها المعلمون

## التحقق من الإعداد

بعد إضافة المتغيرات، يمكنك التحقق من أن التكوين يعمل بشكل صحيح:

```bash
php artisan tinker
```

ثم في Tinker:

```php
Storage::disk('spaces')->exists('test.txt'); // يجب أن يعمل بدون أخطاء
```

## الصلاحيات المطلوبة

تأكد من أن الـ Bucket في DigitalOcean Spaces لديه الصلاحيات التالية:

- **Public Read**: إذا كنت تريد أن تكون الملفات متاحة للعموم
- **Private**: إذا كنت تريد التحكم الكامل في الوصول (موصى به)

في حالة استخدام Private، التطبيق سيقوم بإنشاء روابط موقعة مؤقتة عند الحاجة.

## استكشاف الأخطاء

### خطأ: "Unable to locate credentials"
- تحقق من أن DO_SPACES_KEY و DO_SPACES_SECRET صحيحان
- تأكد من عدم وجود مسافات في بداية أو نهاية القيم

### خطأ: "Error executing GetObject"
- تحقق من أن DO_SPACES_BUCKET و DO_SPACES_REGION صحيحان
- تأكد من أن الـ endpoint يطابق الـ region

### خطأ: "403 Forbidden"
- تحقق من صلاحيات الـ API Key
- تأكد من أن الـ Bucket ليس محذوفاً أو معلقاً

## الملفات التي تم تعديلها

- `config/filesystems.php`: إضافة disk جديد لـ Spaces
- `app/Http/Controllers/AssignmentController.php`: تحديث لاستخدام Spaces
- `app/Http/Controllers/AssignmentSubmissionController.php`: تحديث لاستخدام Spaces
- `app/Models/Assignment.php`: تحديث methods للتحقق من الملفات
- `app/Models/AssignmentSubmission.php`: تحديث methods للتحقق من الملفات

## الحزم المثبتة

تم تثبيت الحزمة التالية:

```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

