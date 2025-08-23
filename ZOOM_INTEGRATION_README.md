# 🔗 Zoom API Integration - Laravel + Vue

## 📋 نظرة عامة

تم إنشاء نظام متكامل لربط مشروع Laravel + Vue مع Zoom API باستخدام Server-to-Server OAuth. النظام يدعم إنشاء وإدارة اجتماعات Zoom مع حفظ البيانات في قاعدة البيانات.

## 🚀 الميزات

- ✅ **Server-to-Server OAuth** - مصادقة آمنة مع Zoom
- ✅ **إنشاء اجتماعات** - إنشاء اجتماعات جديدة مع إعدادات مخصصة
- ✅ **إدارة الاجتماعات** - تحديث، حذف، بدء، إنهاء الاجتماعات
- ✅ **تتبع المشاركين** - تسجيل دخول وخروج المشاركين
- ✅ **ربط مع الكورسات** - ربط الاجتماعات مع الكورسات والمواعيد
- ✅ **واجهة مستخدم** - Vue components لإدارة الاجتماعات
- ✅ **Cache Management** - إدارة Access Token بكفاءة

## 🛠️ المتطلبات

### 1. Zoom App Credentials
يجب إنشاء Zoom App في [Zoom Marketplace](https://marketplace.zoom.us/) والحصول على:

```env
ZOOM_ACCOUNT_ID=your_account_id
ZOOM_CLIENT_ID=your_client_id
ZOOM_CLIENT_SECRET=your_client_secret
```

### 2. Laravel Requirements
- Laravel 11+
- PHP 8.2+
- MySQL/PostgreSQL

## 📁 هيكل الملفات

```
app/
├── Services/
│   └── ZoomService.php              # خدمة Zoom الرئيسية
├── Models/
│   ├── ZoomMeeting.php              # نموذج الاجتماعات
│   └── ZoomMeetingParticipant.php   # نموذج المشاركين
├── Http/Controllers/
│   └── ZoomMeetingController.php    # تحكم الاجتماعات
└── Console/Commands/
    └── TestZoomAPI.php              # أمر اختبار API

resources/js/Pages/ZoomMeetings/
├── Create.vue                       # إنشاء اجتماع جديد
├── Edit.vue                         # تعديل اجتماع
├── Index.vue                        # قائمة الاجتماعات
└── Show.vue                         # عرض اجتماع

database/migrations/
├── create_zoom_meetings_table.php
└── create_zoom_meeting_participants_table.php
```

## 🔧 التثبيت والإعداد

### 1. إضافة المتغيرات البيئية
أضف المفاتيح التالية إلى ملف `.env`:

```env
ZOOM_ACCOUNT_ID=m8VMK4ZyRkeAN0btuHP_mA
ZOOM_CLIENT_ID=A_YMIa68Rky5zPRCGHyxOw
ZOOM_CLIENT_SECRET=bUKVISRcjhcxMuViOj39hqzi5lt5z44n6
```

### 2. تشغيل Migrations
```bash
php artisan migrate
```

### 3. اختبار الاتصال
```bash
php artisan zoom:test
```

## 📖 كيفية الاستخدام

### 1. إنشاء اجتماع جديد

```php
use App\Services\ZoomService;

$zoomService = new ZoomService();

$meetingData = [
    'topic' => 'درس الرياضيات - الفصل الأول',
    'start_time' => '2025-08-25 08:00:00',
    'duration' => 60, // بالدقائق
    'timezone' => 'Asia/Riyadh'
];

$meeting = $zoomService->createMeeting($meetingData);

// حفظ في قاعدة البيانات
ZoomMeeting::create([
    'course_id' => 1,
    'zoom_meeting_id' => $meeting['zoom_meeting_id'],
    'topic' => $meetingData['topic'],
    'start_time' => $meetingData['start_time'],
    'duration' => $meetingData['duration'],
    'join_url' => $meeting['join_url'],
    'start_url' => $meeting['start_url'],
    'password' => $meeting['password'],
    'status' => 'scheduled',
    'host_email' => $meeting['host_email']
]);
```

### 2. إدارة الاجتماعات

```php
// بدء اجتماع
$meeting->update(['status' => 'started']);

// إنهاء اجتماع
$meeting->update(['status' => 'ended']);

// حذف اجتماع
$zoomService->deleteMeeting($meeting->zoom_meeting_id);
$meeting->delete();
```

### 3. الوصول للاجتماعات

```php
// اجتماعات قادمة
$upcomingMeetings = ZoomMeeting::upcoming(7)->get();

// اجتماعات نشطة
$activeMeetings = ZoomMeeting::active()->get();

// اجتماعات حسب الكورس
$courseMeetings = ZoomMeeting::byCourse($courseId)->get();
```

## 🌐 الواجهات

### 1. قائمة الاجتماعات
```
GET /admin/zoom-meetings
```

### 2. إنشاء اجتماع جديد
```
GET /admin/zoom-meetings/create
POST /admin/zoom-meetings
```

### 3. تعديل اجتماع
```
GET /admin/zoom-meetings/{id}/edit
PUT /admin/zoom-meetings/{id}
```

### 4. إدارة الاجتماع
```
POST /admin/zoom-meetings/{id}/start
POST /admin/zoom-meetings/{id}/end
DELETE /admin/zoom-meetings/{id}
```

## 🔐 الأمان

- **Access Token Caching** - تخزين مؤقت للـ token لمدة 50 دقيقة
- **Error Handling** - معالجة شاملة للأخطاء
- **Logging** - تسجيل جميع العمليات
- **Transaction Management** - إدارة المعاملات لضمان سلامة البيانات

## 📊 قاعدة البيانات

### جدول `zoom_meetings`
- `id` - المعرف الفريد
- `course_id` - معرف الكورس
- `zoom_meeting_id` - معرف الاجتماع في Zoom
- `topic` - عنوان الاجتماع
- `start_time` - وقت البدء
- `duration` - المدة بالدقائق
- `join_url` - رابط الانضمام
- `start_url` - رابط البدء (للمضيف)
- `password` - كلمة المرور
- `status` - حالة الاجتماع
- `host_email` - بريد المضيف

### جدول `zoom_meeting_participants`
- `id` - المعرف الفريد
- `zoom_meeting_id` - معرف الاجتماع
- `user_id` - معرف المستخدم
- `join_time` - وقت الانضمام
- `leave_time` - وقت المغادرة
- `duration` - مدة الحضور
- `status` - حالة المشاركة
- `role` - دور المشارك

## 🧪 الاختبار

### اختبار API
```bash
php artisan zoom:test
```

### اختبار الواجهات
1. إنشاء اجتماع جديد
2. تعديل اجتماع موجود
3. بدء وإنهاء اجتماع
4. حذف اجتماع

## 🚨 استكشاف الأخطاء

### مشاكل شائعة

1. **خطأ في Access Token**
   - تأكد من صحة المفاتيح في `.env`
   - تحقق من صلاحيات Zoom App

2. **خطأ في إنشاء الاجتماع**
   - تأكد من صحة البيانات
   - تحقق من اتصال الإنترنت

3. **خطأ في قاعدة البيانات**
   - تأكد من تشغيل migrations
   - تحقق من صحة العلاقات

### سجلات الأخطاء
```bash
tail -f storage/logs/laravel.log
```

## 🔄 التطوير المستقبلي

- [ ] **Webhook Integration** - استقبال تحديثات من Zoom
- [ ] **Recording Management** - إدارة التسجيلات
- [ ] **Analytics Dashboard** - لوحة تحكم إحصائية
- [ ] **Mobile App Support** - دعم التطبيق المحمول
- [ ] **Multi-language Support** - دعم لغات إضافية

## 📞 الدعم

للمساعدة أو الاستفسارات:
- 📧 البريد الإلكتروني: support@example.com
- 📱 الهاتف: +966-XX-XXX-XXXX
- 💬 الدردشة: [رابط الدردشة]

---

**تم التطوير بواسطة فريق Edu-Dream** 🎓
**آخر تحديث**: 23 أغسطس 2025
