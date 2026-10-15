<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('service_categories')->onDelete('cascade');
            $table->string('business_name')->nullable();
            $table->text('bio')->nullable();
            $table->string('experience_years')->default(0);
            $table->string('service_area')->nullable();
            $table->decimal('hourly_rate', 8, 2)->default(0);
            $table->decimal('skill_score', 5, 2)->default(0);
            $table->integer('total_jobs')->default(0);
            $table->integer('completed_jobs')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->enum('verification_status', ['pending','verified','rejected'])->default('pending');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('id_proof')->nullable();
            $table->string('certificate')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('providers'); }
};
