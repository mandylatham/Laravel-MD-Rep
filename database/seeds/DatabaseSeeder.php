<?php

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
        $this->call(AdminUserTypeSeeder::class);
        $this->call(GenderSeeder::class);
        $this->call(DeviceTypeSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(PermissionSeeder::class);
    }
}
