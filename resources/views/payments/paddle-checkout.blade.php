<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إتمام الدفع</title>
    <script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f8fafc;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1f2937;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            width: min(480px, calc(100% - 32px));
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            text-align: center;
        }
        .amount {
            font-size: 28px;
            font-weight: 700;
            color: #b45309;
            margin: 12px 0 24px;
        }
        button {
            background: #d97706;
            color: #fff;
            border: 0;
            border-radius: 10px;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
        }
        .note, .error {
            margin-top: 16px;
            font-size: 14px;
            line-height: 1.7;
            color: #4b5563;
        }
        .error { color: #b91c1c; text-align: right; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>إتمام الدفع</h1>
        <p>{{ $payment->description }}</p>
        <div class="amount">{{ $payment->amount_format }}</div>

        @if($clientToken)
            <button id="pay-button" type="button">ادفع الآن</button>
            <p class="note">ستظهر نافذة الدفع من Paddle بالمبلغ محوّلاً إلى الدولار (1 دولار = 3.75 ر.س).</p>
        @else
            <div class="error">
                لإتمام التجربة محلياً أضف <code>PADDLE_CLIENT_TOKEN</code> في ملف <code>.env</code>.
                <br>
                من لوحة Paddle Sandbox:
                Developer tools → Authentication → Client-side tokens
                ثم أنشئ توكن يبدأ بـ <code>test_</code>.
            </div>
        @endif
    </div>

    @if($clientToken)
    <script>
        Paddle.Environment.set(@json($environment === 'live' ? 'production' : 'sandbox'));
        Paddle.Initialize({
            token: @json($clientToken),
            eventCallback: function (event) {
                if (event.name === 'checkout.completed') {
                    window.location.href = @json($successUrl);
                }
            }
        });

        function openCheckout() {
            Paddle.Checkout.open({
                transactionId: @json($transactionId),
                customer: {
                    email: @json($customerEmail),
                    address: {
                        countryCode: @json($customerCountry)
                    }
                },
                settings: {
                    displayMode: 'overlay',
                    theme: 'light',
                    locale: 'ar',
                    allowLogout: false,
                    showAddDiscounts: false,
                    showAddTaxId: false,
                    successUrl: @json($successUrl)
                }
            });
        }

        document.getElementById('pay-button').addEventListener('click', openCheckout);
        window.addEventListener('load', openCheckout);
    </script>
    @endif
</body>
</html>
