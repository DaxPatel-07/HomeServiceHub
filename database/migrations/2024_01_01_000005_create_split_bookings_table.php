<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('split_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('split_code')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->date('booking_date');
            $table->text('address');
            $table->string('city');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('payment_method', ['cash','card','wallet'])->default('cash');
            $table->enum('status', ['pending','in_progress','completed','cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::create('split_booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('split_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('service_categories');
            $table->time('scheduled_time');
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('status', ['pending','accepted','completed','cancelled'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('split_booking_items');
        Schema::dropIfExists('split_bookings');
    }
};
