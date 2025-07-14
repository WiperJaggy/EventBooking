<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      if (\App\Models\User::count() === 0) {
        $this->call(UsersTableSeeder::class);
    }

    \App\Models\Event::factory(10)->create();
    }
}
