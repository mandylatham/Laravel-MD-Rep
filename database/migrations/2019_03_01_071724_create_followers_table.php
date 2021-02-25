<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('follower_id');
            $table->unsignedInteger('followed_id');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->softDeletes();
            $table->useCurrent();
        });
        Schema::table('followers', function (Blueprint $table) {
            $table->foreign('follower_id')->references('id')->on('users');
        });
        Schema::table('followers', function (Blueprint $table) {
            $table->foreign('followed_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('followers', function (Blueprint $table) {
            $table->dropForeign(['follower_id']);
        });
         Schema::table('followers', function (Blueprint $table) {
            $table->dropForeign(['followed_id']);
        });
        Schema::dropIfExists('followers');
    }
}
