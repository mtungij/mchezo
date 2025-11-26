<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('payout_schedules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        $table->date('start_date');
        $table->date('end_date');
        $table->integer('expected_amount');

        $table->enum('status', ['pending', 'paid'])->default('pending');

        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('payout_schedules');
}

};
