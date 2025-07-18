<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RoomSeeder extends Seeder
{
    // Customize how many rooms to generate
    protected int $roomCount = 10;

    public function run(): void
    {
        $faker = Faker::create();

        $roomTypes = ['lecture_hall', 'lab', 'seminar_room', 'auditorium'];
        $buildings = ['Science Block', 'Main Block', 'Library Wing', 'Tech Center'];

        for ($i = 1; $i <= $this->roomCount; $i++) {
            Room::create([
                'room_number' => strtoupper('R-' . $faker->unique()->numberBetween(100, 999)),
                'building' => $faker->randomElement($buildings),
                'capacity' => $faker->numberBetween(20, 120),
                'room_type' => $faker->randomElement($roomTypes),
                'created_by' => 1, // Use a real user ID if available
                'updated_by' => 1,
            ]);
        }
    }
}
