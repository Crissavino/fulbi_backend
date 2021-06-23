<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('token')->nullable();
            $table->string('uuid')->nullable();
            $table->string('language')->nullable();
            $table->softDeletes();
            $table->timestampsTz();
        });

        Schema::create('device_message', function (Blueprint $table) {
            $table->id();
            $table->integer('device_id')->unsigned();
            $table->integer('message_id')->unsigned();
            $table->boolean('read')->default(false);
            $table->timestamps();
        });

        Schema::table('message_player', function (Blueprint $table) {
            $table->boolean('read')->default(false);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->integer('type')->default(1);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->boolean('have_notifications')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
        Schema::dropIfExists('device_message');
        Schema::table('message_player', function (Blueprint $table) {
            $table->dropColumn('read');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('have_notifications');
        });
    }
}
