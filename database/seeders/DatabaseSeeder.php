<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@cctv.test',
        ]);
        $admin->assignRole('admin');

        $sales = User::factory()->create([
            'name' => 'Agent Vanzari',
            'email' => 'vanzari@cctv.test',
        ]);
        $sales->assignRole('vanzari');

        $tech = User::factory()->create([
            'name' => 'Tehnician',
            'email' => 'tehnic@cctv.test',
        ]);
        $tech->assignRole('tehnic');

        $support = User::factory()->create([
            'name' => 'Suport',
            'email' => 'suport@cctv.test',
        ]);
        $support->assignRole('suport');

        $this->call([
            EquipmentSeeder::class,
            PostSeeder::class,
        ]);
    }
}
