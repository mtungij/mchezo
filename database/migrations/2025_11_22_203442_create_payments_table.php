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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('group_id');
        $table->unsignedBigInteger('member_id'); // group_member id
        $table->unsignedBigInteger('payer_id'); // member anayelipa
        $table->decimal('amount', 15, 2);
        $table->timestamps();

        $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        $table->foreign('member_id')->references('id')->on('group_members')->onDelete('cascade');
        $table->foreign('payer_id')->references('id')->on('group_members')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
