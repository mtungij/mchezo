<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::table('groups', function (Blueprint $table) {
    $table->decimal('contribution_amount', 12, 2)->nullable();
    $table->date('start_date')->nullable();
    $table->enum('frequency_type', ['day', 'week', 'month'])->default('day');
    $table->integer('interval')->default(1);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
             Schema::dropIfExists('contribution_amount');
        Schema::dropIfExists('start_date');
        Schema::dropIfExists('frequency_type');
         Schema::dropIfExists('interval');
        });
    }
};
