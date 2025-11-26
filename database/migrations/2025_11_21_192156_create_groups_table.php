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
    Schema::create('groups', function (Blueprint $table) {
        $table->id();
        $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
        $table->string('name');
        $table->string('invite_code')->unique();
        
        // settings
        $table->integer('contribution_per_day')->nullable();
        $table->integer('payout_period_days')->nullable();
 

        $table->integer('total_members')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('groups');
}

};
