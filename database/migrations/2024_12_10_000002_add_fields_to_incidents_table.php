<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->text('location_description')->nullable()->after('location');
            $table->foreignId('localidad_id')->nullable()->after('location_description')->constrained('localidades')->nullOnDelete();
            $table->enum('privacy_level', ['ANONYMOUS', 'IDENTIFIED'])->default('ANONYMOUS')->after('localidad_id');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['localidad_id']);
            $table->dropColumn(['location_description', 'localidad_id', 'privacy_level']);
        });
    }
};
