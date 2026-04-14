<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run()
    {
        Group::create([
            'name' => 'Покупатели',
            'type' => 'buyer'
        ]);

        Group::create([
            'name' => 'Продавцы',
            'type' => 'seller'
        ]);

        Group::create([
            'name' => 'Администраторы',
            'type' => 'admin'
        ]);
    }
}
