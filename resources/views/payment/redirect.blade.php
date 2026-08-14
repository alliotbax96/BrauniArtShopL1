<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Перенаправление на оплату — {{ $serviceName }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #0a1a1a 0%, #1a2e2a 50%, #0a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        body::before,
        body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 20s ease-in-out infinite;
        }
        body::before {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, {{ $primaryColor }}, transparent 70%);
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }
        body::after {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, {{ $secondaryColor }}, transparent 70%);
            bottom: -80px;
            left: -80px;
            animation-delay: -10s;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            max-width: 560px;
            width: 100%;
            padding: 32px 28px;
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
            text-align: center;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(144, 233, 209, 0.15);
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Логотип родителя */
        .parent-logo {
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }
        .parent-logo img {
            max-width: 200px;
            height: 47px;
            object-fit: contain;
        }
        .parent-logo .divider {
            display: block;
            width: 40px;
            height: 2px;
            background: {{ $primaryColor }};
            margin: 12px auto 0;
            border-radius: 2px;
        }

        /* Логотип дочерней системы */
        .child-logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 4px;
        }
        .child-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
            color: {{ $textColor }};
            flex-shrink: 0;
        }
        .child-logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
        .child-logo {
            font-size: 24px;
            font-weight: 800;
            color: {{ $textColor }};
        }
        .child-logo span {
            color: {{ $primaryColor }};
        }
        .subtitle {
            color: #6b7280;
            font-size: 13px;
            margin-top: 2px;
        }

        .icon-wrapper {
            width: 72px;
            height: 72px;
            margin: 16px auto 12px;
            background: linear-gradient(135deg, {{ $primaryColor }}20 0%, {{ $secondaryColor }}20 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            border: 2px solid {{ $primaryColor }}40;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        h1 {
            font-size: 20px;
            font-weight: 700;
            color: {{ $textColor }};
            margin-bottom: 2px;
        }
        .payment-amount {
            font-size: 30px;
            font-weight: 800;
            color: {{ $primaryColor }};
            margin: 2px 0 6px;
            letter-spacing: -0.5px;
        }
        .payment-amount .currency {
            font-size: 18px;
            font-weight: 600;
            color: #6b7280;
        }

        .description {
            color: #4b5563;
            font-size: 14px;
            line-height: 1.5;
            margin: 4px 0 16px;
        }

        .company-info {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px;
            padding: 12px 16px;
            margin: 12px 0 16px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        .company-info .label {
            font-size: 10px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .company-info .label .icon {
            font-size: 12px;
        }
        .company-info .value {
            font-size: 13px;
            color: {{ $textColor }};
            font-weight: 600;
            margin-top: 2px;
            line-height: 1.3;
        }
        .company-info .value .inn {
            font-weight: 400;
            color: #6b7280;
            font-size: 12px;
            margin-left: 6px;
        }

        .timer-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin: 16px 0 12px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        .timer-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            color: {{ $textColor }};
            position: relative;
            flex-shrink: 0;
        }
        .timer-circle::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});
            opacity: 0.3;
            z-index: -1;
            animation: pulseRing 1.5s ease-in-out infinite;
        }
        @keyframes pulseRing {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.15); opacity: 0.1; }
        }
        .timer-text {
            font-size: 13px;
            color: #4b5563;
        }
        .timer-text strong {
            color: {{ $textColor }};
        }

        .progress-bar {
            width: 100%;
            height: 3px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin: 0 0 16px;
        }
        .progress-bar .fill {
            height: 100%;
            background: linear-gradient(90deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
            width: 100%;
            animation: progress 15s linear forwards;
            border-radius: 4px;
        }
        @keyframes progress {
            0% { width: 100%; }
            100% { width: 0%; }
        }

        .btn-primary {
            display: inline-block;
            padding: 12px 28px;
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
            color: {{ $textColor }};
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px {{ $primaryColor }}66;
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            display: inline-block;
            padding: 10px 28px;
            background: transparent;
            color: #6b7280;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 6px;
        }
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #d1d5db;
            color: {{ $textColor }};
        }

        .footer {
            margin-top: 16px;
            font-size: 11px;
            color: #9ca3af;
        }
        .footer a {
            color: {{ $primaryColor }};
            text-decoration: none;
            font-weight: 500;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .footer .secure {
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        @media (max-width: 480px) {
            .card {
                padding: 24px 16px;
                border-radius: 16px;
            }
            .payment-amount {
                font-size: 24px;
            }
            .timer-wrapper {
                flex-direction: column;
                gap: 6px;
                padding: 14px;
            }
            .child-logo-icon {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }
            .child-logo {
                font-size: 20px;
            }
            .icon-wrapper {
                width: 56px;
                height: 56px;
                font-size: 24px;
                margin: 12px auto 8px;
            }
            h1 {
                font-size: 18px;
            }
            .company-info .value {
                font-size: 12px;
            }
            body::before,
            body::after {
                display: none;
            }
            .parent-logo img {
                max-width: 150px;
                height: 35px;
            }
        }

        @media (max-width: 380px) {
            .card { padding: 16px 12px; }
            h1 { font-size: 16px; }
            .payment-amount { font-size: 20px; }
            .company-info { padding: 10px 12px; }
            .parent-logo img {
                max-width: 120px;
                height: 28px;
            }
        }
    </style>
</head>
<body>
<div class="card">
    <!-- Логотип родителя (BRAUNIART) -->
    <div class="parent-logo">
        <img src="{{ $serviceLogo }}" alt="BRAUNIART" id="parent-logo">
        <span class="divider"></span>
    </div>

    <!-- Логотип дочерней системы -->
    <div class="child-logo-wrapper">
        @if($serviceLogo && $serviceName !== 'BRAUNIART' && $serviceTextLogo !== '')
            <div class="child-logo-icon">{{$serviceTextLogo}}</div>
        @else
            <div class="child-logo-icon">BA</div>
        @endif
        <div class="child-logo">{{ $serviceName }}</div>
    </div>
    <div class="subtitle">Безопасная платёжная система</div>

    <div class="icon-wrapper">🔄</div>
    <h1>Перенаправление на оплату</h1>

    <div class="payment-amount">
        {{ number_format($payment->amount, 2) }} <span class="currency">₽</span>
    </div>

    <div class="description">
        Сейчас вы будете перенаправлены на защищённую страницу оплаты
    </div>

    <div class="company-info">
        <div class="label">
            <span class="icon">🏛️</span> Получатель платежа
        </div>
        <div class="value">
            {{ $companyName }}
            <span class="inn">· ИНН {{ $inn }}</span>
        </div>
    </div>

    <div class="timer-wrapper">
        <div class="timer-circle" id="timer">15</div>
        <div class="timer-text">
            Автоматический переход через <strong id="timer-seconds">15</strong> секунд
        </div>
    </div>

    <div class="progress-bar">
        <div class="fill"></div>
    </div>

    <a href="{{ $paymentUrl }}" class="btn-primary" id="manual-redirect">
        🚀 Перейти к оплате сейчас
    </a>

    <a href="{{ $returnUrl }}" class="btn-secondary">
        ← Вернуться в кабинет
    </a>

    <div class="footer">
        <span class="secure">🔒 Защищённое соединение</span>
        ·
        Оплата через
        <a href="https://www.tinkoff.ru" target="_blank" rel="noopener">Тинькофф Банк</a>
    </div>
</div>

<script>
    // Обработчик ошибок загрузки логотипа
    document.getElementById('parent-logo')?.addEventListener('error', function() {
        this.style.display = 'none';
        this.parentElement.querySelector('.divider')?.remove();
    });

    let seconds = 15;
    const timerElement = document.getElementById('timer');
    const secondsElement = document.getElementById('timer-seconds');
    const redirectUrl = '{{ $paymentUrl }}';
    let redirected = false;

    function startTimer() {
        const interval = setInterval(() => {
            seconds--;
            timerElement.textContent = seconds;
            secondsElement.textContent = seconds;

            if (seconds <= 0 && !redirected) {
                clearInterval(interval);
                redirected = true;
                window.location.href = redirectUrl;
            }
        }, 1000);
    }

    document.getElementById('manual-redirect').addEventListener('click', function(e) {
        e.preventDefault();
        if (!redirected) {
            redirected = true;
            window.location.href = redirectUrl;
        }
    });

    // Проверяем, не загружена ли страница из кеша
    if (performance.navigation.type === 2) {
        window.location.href = '{{ $returnUrl }}';
    }

    // startTimer();

    // Предотвращаем случайный выход с страницы
    window.addEventListener('beforeunload', function(e) {
        if (!redirected && seconds > 0) {
            e.preventDefault();
            e.returnValue = 'Вы уверены, что хотите покинуть страницу оплаты?';
            return e.returnValue;
        }
    });
</script>
</body>
</html>
