<?php

use App\Models\Role\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name' => 'allow'
        ]);

        Permission::create([
            'name' => 'verify'
        ]);

        Permission::create([
            'name' => 'deny'
        ]);

        Permission::create([
            'name' => 'create'
        ]);

        Permission::create([
            'name' => 'edit'
        ]);

        Permission::create([
            'name' => 'update'
        ]);

        Permission::create([
            'name' => 'delete'
        ]);
    }
}
