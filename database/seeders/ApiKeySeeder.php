<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApiKeySeeder extends Seeder
{
    public function run(): void
    {
        // Генерируем ключ для дочерней системы
        ApiKey::create([
            'name' => 'ALBAX Radio Hosting (Child System)',
            'key' => 'albax_' . Str::random(40),
            'is_active' => true,
            'expires_at' => null,
        ]);

        $this->command->info('✅ API Key generated successfully!');

        $key = ApiKey::where('name', 'ALBAX Radio Hosting (Child System)')->first();
        $this->command->info('🔑 API Key: ' . $key->key);
        $this->command->info('📝 Copy this key to your child system .env file as PAYMENT_API_KEY');
    }
}
