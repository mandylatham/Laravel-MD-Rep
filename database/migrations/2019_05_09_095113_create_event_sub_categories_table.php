<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_sub_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_type');
            $table->string('name');
            $table->tinyInteger('active')->default(1);
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('event_sub_categories', function (Blueprint $table) {
            $table->foreign('event_type')->references('id')->on('event_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_sub_categories', function (Blueprint $table) {
            $table->dropForeign(['event_type_id']);
        });
        Schema::dropIfExists('event_sub_categories');
    }
}
