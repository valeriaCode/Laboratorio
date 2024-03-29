<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PlanSeeder::class);
        $this->call(LaboratorySeeder::class);
        $this->call(LaboratoryPlanSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(PeriodSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(SpecialitySeeder::class);
    }
}
