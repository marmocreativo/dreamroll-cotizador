<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name'     => 'Manuel',
            'email'    => 'marmocreativo@gmail.com',
            'password' => Hash::make('Angeles1#'),
        ]);

        User::factory()->create([
            'name'     => 'Elsa',
            'email'    => 'agendatucita@geneticlab.mx',
            'password' => Hash::make('135Agenda#'),
        ]);

        User::factory()->create([
            'name'     => 'Leopoldo',
            'email'    => 'lmaciel@geneticlab.mx',
            'password' => Hash::make('135Lmaciel#'),
        ]);
        $this->call(EstudiosSeeder::class);
        $this->call(CentrosAgendaSeeder::class);
    }
}
