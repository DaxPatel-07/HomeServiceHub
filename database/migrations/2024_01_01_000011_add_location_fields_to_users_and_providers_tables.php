<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('locality', 100)->nullable()->after('longitude');
            $table->string('building', 150)->nullable()->after('locality');
            $table->string('postal_code', 20)->nullable()->after('building');
        });

        Schema::table('providers', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('service_area');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('locality', 100)->nullable()->after('longitude');
            $table->string('building', 150)->nullable()->after('locality');
            $table->string('postal_code', 20)->nullable()->after('building');
            $table->decimal('service_radius_km', 5, 2)->default(5.00)->after('postal_code');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'locality', 'building', 'postal_code']);
        });

        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'locality', 'building', 'postal_code', 'service_radius_km']);
        });
    }
};
