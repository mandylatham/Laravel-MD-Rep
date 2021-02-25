<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('parent_id')->default('0');
            $table->string('text')->nullable();
            $table->unsignedInteger('like_count')->default('0');
            $table->unsignedInteger('comment_count')->default('0');
            $table->unsignedInteger('share_count')->default('0');
            $table->unsignedInteger('type');
            $table->unsignedInteger('active')->default('1');
            $table->timestamp('blocked_at')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_posts');
    }
}
