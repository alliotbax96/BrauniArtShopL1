<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use Illuminate\Database\Seeder;

class UpdateApiKeysSeeder extends Seeder
{
    public function run(): void
    {
        // Обновляем существующий ключ для ALBAX Radio Hosting
        $apiKey = ApiKey::where('name', 'ALBAX Radio Hosting (Child System)')->first();

        if ($apiKey) {
            $apiKey->update([
                'service_name' => 'ALBAX Radio Hosting',
                'service_logo' => 'https://brauniart.shop/assets/img/logo/logo.png',
                'primary_color' => '#90e9d1',
                'secondary_color' => '#5cd4b8',
                'text_color' => '#0a1a1a',
                'bg_color' => '#ffffff',
                'company_name' => 'ООО "БРАУНИ АРТ МАГАЗИН"',
                'inn' => '4705128695',
                'return_url' => 'https://radio-hosting.local/dashboard',
            ]);

            $this->command->info('✅ API key updated with branding');
        } else {
            // Создаём новый с брендингом
            ApiKey::create([
                'name' => 'ALBAX Radio Hosting (Child System)',
                'key' => 'albax_' . \Str::random(40),
                'is_active' => true,
                'expires_at' => null,
                'service_name' => 'ALBAX Radio Hosting',
                'service_logo' => 'https://brauniart.shop/assets/img/logo/logo.png',
                'primary_color' => '#90e9d1',
                'secondary_color' => '#5cd4b8',
                'text_color' => '#0a1a1a',
                'bg_color' => '#ffffff',
                'company_name' => 'ООО "БРАУНИ АРТ МАГАЗИН"',
                'inn' => '4705128695',
                'return_url' => 'https://radio-hosting.local/dashboard',
            ]);

            $this->command->info('✅ New API key created with branding');
        }
    }
}
