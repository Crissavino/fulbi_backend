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

        Schema::table('messages', function (Blueprint $table) {
            $table->integer('type')->default(1);
        });

        Schema::table('match_player', function (Blueprint $table) {
            $table->boolean('have_notifications')->default(false);
            $table->boolean('is_confirmed')->default(false);
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

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('match_player', function (Blueprint $table) {
            $table->dropColumn('have_notifications');
            $table->dropColumn('is_confirmed');
        });
    }
}
