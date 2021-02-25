<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('club_user');
            $table->unsignedInteger('event_type');
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('detail');
            $table->text('location')->default('');
            $table->text('organisation')->default('');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->tinyInteger('active')->default(0);
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('club_user')->references('id')->on('admin_users');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('event_type')->references('id')->on('event_types');
        });

        DB::statement("ALTER TABLE events ADD FULLTEXT full(name)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_user']);
            $table->dropForeign(['event_type']);
        });
        Schema::dropIfExists('events');
    }
}
