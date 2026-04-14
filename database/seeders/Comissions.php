<?php

namespace Database\Seeders;

use Egulias\EmailValidator\Result\Reason\CommentsInIDRight;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comission;

class Comissions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comission::create([
            'product_group_id' => 2,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 5,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 20,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 23,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 24,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 28,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 29,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 42,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 43,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 44,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 45,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 46,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 47,
            'comission'=>16
        ]);

        Comission::create([
            'product_group_id' => 48,
            'comission'=>30
        ]);

        Comission::create([
            'product_group_id' => 49,
            'comission'=>30
        ]);

        Comission::create([
            'product_group_id' => 51,
            'comission'=>30
        ]);

        Comission::create([
            'product_group_id' => 3,
            'comission'=>10
        ]);

        Comission::create([
            'product_group_id' => 6,
            'comission'=>10
        ]);

        Comission::create([
            'product_group_id' => 21,
            'comission'=>10
        ]);

        Comission::create([
            'product_group_id' => 30,
            'comission'=>10
        ]);

        Comission::create([
            'product_group_id' => 32,
            'comission'=>10
        ]);
    }
}
