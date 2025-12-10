<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيميل تجريبي - منصة إيدو دريم</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 40px 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
        }
        .header .subtitle {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            font-size: 18px;
            color: #4b5563;
            margin: 30px 0;
            line-height: 1.8;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
        }
        .info-box {
            background: #f0f9ff;
            border: 2px solid #0ea5e9;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        .info-box h3 {
            color: #0c4a6e;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .info-box p {
            color: #075985;
            margin: 5px 0;
            font-size: 15px;
        }
        .footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
        }
        .footer p {
            margin: 5px 0;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>إيدو دريم</h1>
            <div class="subtitle">inskola</div>
        </div>
        
        <div class="content">
            <div class="success-icon">
                ✓
            </div>
            
            <div class="greeting">
                مرحباً {{ $userName }}!
            </div>
            
            <div class="message">
                {{ $testMessage }}
                <br><br>
                تم إرسال هذا الإيميل بنجاح من خلال خدمة Mailgun للتأكد من أن النظام يعمل بشكل صحيح.
            </div>
            
            <div class="info-box">
                <h3>معلومات الإرسال</h3>
                <p><strong>التاريخ:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                <p><strong>الخدمة:</strong> Mailgun API</p>
                <p><strong>حالة الإرسال:</strong> تم بنجاح ✅</p>
                <p><strong>نوع الإيميل:</strong> إيميل تجريبي</p>
            </div>
            
            <a href="http://127.0.0.1:8000" class="btn">زيارة المنصة</a>
        </div>
        
        <div class="footer">
            <p><strong>منصة إيدو دريم التعليمية</strong></p>
            <p>بوابتك إلى المعرفة والنجاح</p>
            <p>© 2024 inskola. جميع الحقوق محفوظة.</p>
            <p style="margin-top: 15px; font-size: 12px;">
                هذا إيميل تجريبي لاختبار نظام البريد الإلكتروني
            </p>
        </div>
    </div>
</body>
</html>
