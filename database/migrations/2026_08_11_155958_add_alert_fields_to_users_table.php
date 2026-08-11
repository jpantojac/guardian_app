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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('alert_threshold')->nullable()->comment('Custom incident threshold for alerts');
            $table->integer('alert_timeframe_hours')->nullable()->comment('Custom timeframe in hours');
            $table->integer('alert_cooldown_hours')->nullable()->comment('Custom cooldown period in hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['alert_threshold', 'alert_timeframe_hours', 'alert_cooldown_hours']);
        });
    }
};
