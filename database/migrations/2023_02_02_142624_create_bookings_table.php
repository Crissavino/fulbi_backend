<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('field_id')->constrained();
            $table->foreignId('match_id')->nullable()->constrained();
            $table->foreignId('type_id')->nullable()->constrained();
            $table->dateTime('when');
            $table->text('message');
            $table->enum('status', ['pending', 'accepted', 'rejected']);
            $table->boolean('have_notifications')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
