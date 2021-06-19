<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('team_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->softDeletes();
            $table->timestampsTz();
        });

        Schema::create('player_position', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id');
            $table->integer('position_id');
            $table->timestampsTz();
        });

        Schema::create('message_player', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id');
            $table->integer('message_id');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('players');
        Schema::dropIfExists('player_position');
        Schema::dropIfExists('message_player');
    }
}
