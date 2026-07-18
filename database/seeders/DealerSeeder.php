<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DealerSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating 100 dealers in users table. This may take a few minutes...');
        User::factory()->count(100)->dealer()->create();
    }
}
