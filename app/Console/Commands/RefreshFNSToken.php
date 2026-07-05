<?php

namespace App\Console\Commands;

use App\Http\Integrations\FNS\AuthConnector;
use App\Http\Integrations\FNS\Requests\GetAccessTokenRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshFNSToken extends Command
{
    protected $signature = 'fns:refresh-token';
    protected $description = 'Refresh FNS access token and store it in cache';

    public function handle(): int
    {
        try {
            $connector = new AuthConnector();

            $deviceInfo = [
                'sourceDeviceId' => config('services.fns.source_device_id'),
                'sourceType' => 'WEB',
                'appVersion' => '1.0.0',
                'metaDetails' => [
                    'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
                ],
            ];

            $refreshToken = config('services.fns.refresh_token');
            $request = new GetAccessTokenRequest($deviceInfo, $refreshToken);
            $response = $connector->send($request);

            if ($response->status() === 200) {
                $data = $response->json();

                if (isset($data['token'])) {
                    // Сохраняем токен в кеш на 60 минут
                    Cache::put('fns_access_token', $data['token'], now()->addMinutes(60));

                    // Также сохраняем refresh token если он обновился
                    if (isset($data['refreshToken'])) {
                        Cache::put('fns_refresh_token', $data['refreshToken'], now()->addDays(30));
                    }

                    $this->info('FNS access token успешно обновлен и сохранен в кеш.');

                    return self::SUCCESS;
                }
            }

            $this->error('Не удалось получить токен. Статус: ' . $response->status());
            $this->error('Ответ: ' . $response->body());

            return self::FAILURE;

        } catch (\Exception $e) {
            $this->error('Ошибка при обновлении токена: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
