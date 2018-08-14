<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('role')->create('users_permissions', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->index();
            $table->integer('permission_id')->unsigned()->index();

            $table->primary(['user_id', 'permission_id']);

            $table->foreign('user_id')->references('id')->on('timothy_core.users')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('role')->dropIfExists('users_permissions');
    }
}
