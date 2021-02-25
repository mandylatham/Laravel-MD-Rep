<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('tel_code');
            $table->string('phone')->default("");
            $table->string('password');
            $table->string('token')->nullable();
            $table->string('bio')->default("");
            $table->unsignedInteger('gender')->default(0);
            $table->unsignedInteger('country')->nullable();
            $table->text('profile_pic')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->decimal('lat', 10, 8)->default(0);
            $table->decimal('lng', 11, 8)->default(0);
            $table->tinyInteger('email_verified')->default(0);
            $table->tinyInteger('mobile_verified')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('country')->references('id')->on('countries');
        });
       /* Schema::table('users', function (Blueprint $table) {
            $table->foreign('gender')->references('id')->on('genders');
        });*/
        DB::statement("ALTER TABLE users ADD FULLTEXT full(name)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['country']);
            //$table->dropForeign(['gender']);
        });
        Schema::dropIfExists('users');
    }
}
