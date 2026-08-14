<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Платеж выполнен — ALBAX Hosting</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: white;
            max-width: 480px;
            width: 100%;
            padding: 40px 32px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            text-align: center;
        }
        .icon { font-size: 64px; margin-bottom: 16px; }
        h1 { font-size: 24px; font-weight: 700; color: #0a1a1a; margin-bottom: 8px; }
        .success-text { color: #22c55e; }
        p { color: #6b7280; font-size: 15px; line-height: 1.6; margin-bottom: 8px; }
        .amount { font-size: 28px; font-weight: 700; color: #0a1a1a; margin: 8px 0; }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #90e9d1 0%, #5cd4b8 100%);
            color: #0a1a1a;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 16px;
        }
        .btn:hover { transform: scale(1.02); box-shadow: 0 8px 24px rgba(144, 233, 209, 0.4); }
        .btn-secondary {
            background: #e5e7eb;
            color: #4b5563;
        }
        .btn-secondary:hover { background: #d1d5db; transform: none; box-shadow: none; }
        .payment-details {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            margin: 16px 0;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">✅</div>
    <h1 class="success-text">Платеж успешно выполнен!</h1>

    <div class="payment-details">
        <p style="color: #4b5563; font-size: 14px;">Сумма платежа</p>
        <div class="amount">{{ number_format($payment->amount, 2) }} ₽</div>
        <p style="color: #6b7280; font-size: 13px; margin-top: 4px;">
            {{ $payment->child_data['description'] ?? 'Пополнение баланса' }}
        </p>
    </div>

    <p>Средства зачислены на ваш баланс.</p>
    <p style="font-size: 13px; color: #9ca3af;">
        ID платежа: {{ $payment->payment_id }}
    </p>

    <a href="{{ $payment->return_url ?? route('home') }}" class="btn">
        Вернуться в кабинет
    </a>
</div>
</body>
</html>
