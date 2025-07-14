<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $users = \App\Models\User::all();
    $events = \App\Models\Event::all();

    foreach ($events as $event) {
        // Book 1-3 random users per event
        $event->bookings()->createMany(
            $users->random(rand(1, 3))
                ->map(fn($user) => ['user_id' => $user->id])
                ->toArray()
        );
    }
    }
}
