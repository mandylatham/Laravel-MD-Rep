<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("chat_id");
            $table->unsignedInteger("from_user");
            $table->unsignedInteger("to_user");
            $table->text("message");
            $table->unsignedInteger("type");
            $table->tinyInteger("is_read")->default(0);
            $table->tinyInteger("status")->default(1);
            $table->timestamp('sender_deleted_at')->nullable();
            $table->timestamp('receiver_deleted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreign('chat_id')->references('id')->on('chat_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['chat_id']);
        });
        Schema::dropIfExists('chat_messages');
    }
}
