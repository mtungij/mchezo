<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('group_members', function (Blueprint $table) {
            $table->boolean('can_pay')->default(false)->after('order_position');
            $table->date('can_pay_until')->nullable()->after('can_pay');
        });
    }

    public function down()
    {
        Schema::table('group_members', function (Blueprint $table) {
            $table->dropColumn(['can_pay', 'can_pay_until']);
        });
    }
};
