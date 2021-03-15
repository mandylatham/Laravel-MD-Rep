<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('tel_code')->nullable();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('code');
            $table->unsignedInteger('user_type');
            $table->text('profile_pic')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('email_verified')->default(0);
            $table->tinyInteger('mobile_verified')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('admin_users', function (Blueprint $table) {
            $table->foreign('user_type')->references('id')->on('admin_user_types');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::table('admin_users', function (Blueprint $table) {
            $table->dropForeign(['user_type']);
        });

        Schema::dropIfExists('admin_users');
    }
}
