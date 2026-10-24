<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Users table indexes for role filtering
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'users_role_idx');
            $table->index(['role', 'is_active'], 'users_role_is_active_idx');
        });

        // 2. Providers table 1:1 user constraint and search indexes
        Schema::table('providers', function (Blueprint $table) {
            $table->unique('user_id', 'providers_user_id_unique');
            $table->index(['verification_status', 'is_active'], 'providers_verification_status_is_active_idx');
            $table->index('skill_score', 'providers_skill_score_idx');
        });

        // 3. Bookings table foreign key & performance composite indexes
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('split_booking_id', 'bookings_split_booking_id_foreign')
                  ->references('id')->on('split_bookings')
                  ->onDelete('set null');
            $table->index(['customer_id', 'status'], 'bookings_customer_id_status_idx');
            $table->index(['provider_id', 'status'], 'bookings_provider_id_status_idx');
            $table->index('status', 'bookings_status_idx');
            $table->index('booking_date', 'bookings_booking_date_idx');
        });

        // 4. Reviews table 1:1 booking uniqueness & provider visibility index
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique('booking_id', 'reviews_booking_id_unique');
            $table->index(['provider_id', 'is_visible'], 'reviews_provider_id_is_visible_idx');
        });

        // 5. Provider Badges pivot table composite unique constraint
        Schema::table('provider_badges', function (Blueprint $table) {
            $table->unique(['provider_id', 'badge_id'], 'provider_badges_provider_id_badge_id_unique');
        });

        // 6. Complaints table 1:1 booking uniqueness & status index
        Schema::table('complaints', function (Blueprint $table) {
            $table->unique('booking_id', 'complaints_booking_id_unique');
            $table->index('status', 'complaints_status_idx');
        });

        // 7. Maintenance Reminders composite index for customer dashboard
        Schema::table('maintenance_reminders', function (Blueprint $table) {
            $table->index(['customer_id', 'is_active', 'next_due_date'], 'reminders_customer_active_due_idx');
        });
    }

    public function down(): void {
        Schema::table('maintenance_reminders', function (Blueprint $table) {
            $table->dropIndex('reminders_customer_active_due_idx');
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropIndex('complaints_status_idx');
            $table->dropUnique('complaints_booking_id_unique');
        });

        Schema::table('provider_badges', function (Blueprint $table) {
            $table->dropUnique('provider_badges_provider_id_badge_id_unique');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_provider_id_is_visible_idx');
            $table->dropUnique('reviews_booking_id_unique');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign('bookings_split_booking_id_foreign');
            $table->dropIndex('bookings_customer_id_status_idx');
            $table->dropIndex('bookings_provider_id_status_idx');
            $table->dropIndex('bookings_status_idx');
            $table->dropIndex('bookings_booking_date_idx');
        });

        Schema::table('providers', function (Blueprint $table) {
            $table->dropIndex('providers_skill_score_idx');
            $table->dropIndex('providers_verification_status_is_active_idx');
            $table->dropUnique('providers_user_id_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_is_active_idx');
            $table->dropIndex('users_role_idx');
        });
    }
};
