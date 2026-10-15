<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('service_categories');
            $table->date('booking_date');
            $table->time('booking_time');
            $table->text('address');
            $table->string('city');
            $table->text('description')->nullable();
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('payment_method', ['cash','card','wallet'])->default('cash');
            $table->enum('payment_status', ['pending','paid','refunded'])->default('pending');
            $table->enum('status', ['pending','accepted','in_progress','completed','cancelled'])->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->boolean('is_split')->default(false);
            $table->unsignedBigInteger('split_booking_id')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};
