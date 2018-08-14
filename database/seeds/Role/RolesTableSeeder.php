<?php

use App\Models\Role\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'admin'
        ]);

        Role::create([
            'name' => 'moderator'
        ]);

        Role::create([
            'name' => 'user'
        ]);
    }
}
