<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ошибка платежа — ALBAX Hosting</title>
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
        h1 { font-size: 24px; font-weight: 700; color: #dc2626; margin-bottom: 8px; }
        p { color: #6b7280; font-size: 15px; line-height: 1.6; }
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
    </style>
</head>
<body>
<div class="card">
    <div class="icon">❌</div>
    <h1>Ошибка платежа</h1>
    <p>При создании платежа произошла ошибка. Пожалуйста, попробуйте позже или обратитесь в поддержку.</p>
    <a href="{{ route('home') }}" class="btn">Вернуться на главную</a>
    <a href="javascript:history.back()" class="btn btn-secondary" style="margin-top: 8px;">← Попробовать снова</a>
</div>
</body>
</html>
