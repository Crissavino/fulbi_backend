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
            $table->integer('team_id');
            $table->integer('location_id');
            $table->softDeletes();
            $table->timestampsTz();
        });

        Schema::create('position_player', function (Blueprint $table) {
            $table->id();
            $table->integer('position_id');
            $table->integer('player_id');
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
        Schema::dropIfExists('position_player');
        Schema::dropIfExists('message_player');
    }
}
