<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_type');
            $table->unsignedInteger('permission');
            $table->tinyInteger('active')->default(0);
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('admin_permissions', function (Blueprint $table) {
            $table->foreign('user_type')->references('id')->on('admin_user_types')->onDelete('cascade');
            $table->foreign('permission')->references('id')->on('permissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_permissions', function (Blueprint $table) {
            $table->dropForeign(['user_type']);
            $table->dropForeign(['permission']);
        });
        Schema::dropIfExists('admin_permissions');
    }
}
