<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subject ?? 'inskola' }}</title>
    <style>
        /* Reset Styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
        }
        
        /* Main Styles */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .email-wrapper {
            width: 100%;
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            padding: 40px 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        /* Header Styles */
        .email-header {
            background: linear-gradient(135deg, #18b596 0%, #149a7f 100%);
            padding: 30px 20px;
            text-align: center;
        }
        
        .logo-container {
            margin: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-container img {
            max-width: 100%;
            width: auto;
            height: auto;
            max-height: 150px;
            display: block !important;
            margin: 0 auto;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Content Styles */
        .email-content {
            padding: 40px 30px;
            text-align: center;
            direction: rtl;
        }
        
        .greeting {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 25px;
            font-weight: 600;
            line-height: 1.4;
            text-align: center;
        }
        
        .message-content {
            font-size: 16px;
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .message-content p {
            margin: 0 0 15px 0;
            color: #4b5563;
            text-align: center;
        }
        
        .message-content p:last-child {
            margin-bottom: 0;
        }
        
        .message-content strong {
            color: #1f2937;
            font-weight: 600;
        }
        
        .message-content em {
            color: #6b7280;
            font-style: italic;
        }
        
        /* Action Button */
        .action-button {
            text-align: center;
            margin: 35px 0;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #18b596 0%, #149a7f 100%);
            color: #ffffff !important;
            padding: 16px 40px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(24, 181, 150, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: linear-gradient(135deg, #149a7f 0%, #11866f 100%);
            box-shadow: 0 6px 20px rgba(24, 181, 150, 0.4);
            transform: translateY(-2px);
        }
        
        /* Info Box */
        .info-box {
            background: #f0fdfa;
            border-right: 4px solid #18b596;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        
        .info-box p {
            margin: 8px 0;
            color: #065f46;
            font-size: 15px;
        }
        
        .info-box strong {
            color: #047857;
        }
        
        /* Footer Styles */
        .email-footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .salutation {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 15px;
            line-height: 1.6;
            text-align: center;
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            overflow: hidden !important;
        }
        
        .salutation strong {
            color: #1f2937;
            display: none !important;
        }
        
        .footer-text {
            font-size: 14px;
            color: #64748b;
            margin: 8px 0;
            line-height: 1.6;
            text-align: center;
        }
        
        .footer-text strong {
            color: #18b596;
            font-weight: 600;
        }
        
        .copyright {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        
        /* Subcopy (Alternative Link) */
        .subcopy {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.6;
        }
        
        .subcopy a {
            color: #18b596;
            word-break: break-all;
            text-decoration: none;
        }
        
        /* Responsive Styles */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px;
            }
            
            .email-container {
                border-radius: 10px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-header h1 {
                font-size: 24px;
            }
            
            .email-content {
                padding: 30px 20px;
            }
            
            .greeting {
                font-size: 20px;
            }
            
            .message-content {
                font-size: 15px;
            }
            
            .btn {
                padding: 14px 30px;
                font-size: 15px;
            }
            
            .email-footer {
                padding: 25px 20px;
            }
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background: #1f2937;
            }
            
            .email-content {
                background: #1f2937;
            }
            
            .greeting {
                color: #f9fafb;
            }
            
            .message-content {
                color: #d1d5db;
            }
            
            .message-content p {
                color: #d1d5db;
            }
            
            .email-footer {
                background: #111827;
                border-top-color: #374151;
            }
            
            .salutation {
                color: #9ca3af;
                display: none !important;
                visibility: hidden !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="logo-container">
                    <img src="{{ url('white_logo-removebg-preview.png') }}" alt="inskola" style="max-width: 100%; width: auto; height: auto; max-height: 150px; display: block; margin: 0 auto; background: transparent;" />
                </div>
            </div>
            
            <!-- Content -->
            <div class="email-content">
                <!-- Greeting -->
                @if (!empty($greeting))
                    <div class="greeting">
                        {!! $greeting !!}
                    </div>
                @else
                    <div class="greeting">
                        @if ($level === 'error')
                            @lang('Whoops!')
                        @else
                            @lang('Hello!')
                        @endif
                    </div>
                @endif
                
                <!-- Intro Lines -->
                <div class="message-content">
                    @foreach ($introLines as $line)
                        <p>{!! $line !!}</p>
                    @endforeach
                </div>
                
                <!-- Action Button -->
                @isset($actionText)
                    <div class="action-button">
                        <a href="https://app.inskola.net/student/dashboard" class="btn" style="color: #ffffff;">
                            {{ $actionText }}
                        </a>
                    </div>
                @endisset
                
                <!-- Outro Lines -->
                @if (!empty($outroLines))
                    <div class="message-content">
                        @foreach ($outroLines as $line)
                            <p>{!! $line !!}</p>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                @php
                    // تعطيل salutation تماماً
                    $salutation = '';
                @endphp
                <div class="footer-text">
                    <strong>inskola</strong><br>
                    بوابتك إلى المعرفة والنجاح
                </div>
                
                <div class="copyright">
                    © {{ date('Y') }} inskola. جميع الحقوق محفوظة.
                </div>
                
                <!-- Subcopy (Alternative Link) -->
                @isset($actionText)
                    <div class="subcopy">
                        @lang(
                            "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
                            'into your web browser:',
                            [
                                'actionText' => $actionText,
                            ]
                        )
                        <br><br>
                        <a href="https://app.inskola.net/student/dashboard">https://app.inskola.net/student/dashboard</a>
                    </div>
                @endisset
            </div>
        </div>
    </div>
</body>
</html>
