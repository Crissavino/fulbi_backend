<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->integer('location_id');
            $table->timestampTz('when_play');
            $table->integer('genre_id');
            $table->integer('type_id');
            $table->integer('num_players');
            $table->float('cost');
            $table->integer('chat_id');
            $table->integer('owner_id');
            $table->timestampsTz();
        });

        Schema::create('match_player', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id');
            $table->integer('match_id');
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
        Schema::dropIfExists('matches');
        Schema::dropIfExists('match_player');
    }
}
