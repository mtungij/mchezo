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
    Schema::create('group_members', function (Blueprint $table) {
        $table->id();
        $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        // order in which member will receive payout
        $table->integer('order_position')->nullable();

        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('group_members');
}

};
