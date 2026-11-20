<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('slug')->unique(); $t->string('category'); $t->text('description'); $t->string('icon')->default('heart'); $t->unsignedInteger('price'); $t->enum('unit',['hour','visit','day'])->default('visit'); $t->unsignedTinyInteger('min_units')->default(1); $t->unsignedSmallInteger('duration_minutes')->default(60); $t->boolean('active')->default(true); $t->timestamps();
        });
        Schema::create('caregivers', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('role'); $t->string('speciality'); $t->unsignedTinyInteger('experience_years')->default(1); $t->string('city')->default('Bandung'); $t->decimal('rating',3,1)->default(4.9); $t->boolean('available')->default(true); $t->string('initials',4)->nullable(); $t->string('accent',20)->default('mint'); $t->text('bio')->nullable(); $t->timestamps();
        });
        Schema::create('customers', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email')->nullable(); $t->string('phone'); $t->text('address')->nullable(); $t->timestamps();
        });
        Schema::create('promotions', function (Blueprint $t) {
            $t->id(); $t->string('code')->unique(); $t->enum('type',['percent','fixed'])->default('percent'); $t->unsignedInteger('value'); $t->unsignedInteger('max_discount')->default(0); $t->boolean('active')->default(true); $t->date('expires_at')->nullable(); $t->timestamps();
        });
        Schema::create('service_areas', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('city'); $t->unsignedTinyInteger('coverage_km')->default(25); $t->boolean('active')->default(true); $t->timestamps();
        });
        Schema::create('bookings', function (Blueprint $t) {
            $t->id(); $t->string('code')->unique(); $t->foreignId('service_id')->constrained()->restrictOnDelete(); $t->foreignId('customer_id')->constrained()->restrictOnDelete(); $t->foreignId('caregiver_id')->nullable()->constrained()->nullOnDelete();
            $t->date('scheduled_date'); $t->time('scheduled_time'); $t->unsignedTinyInteger('units')->default(1); $t->decimal('distance_km',6,1); $t->boolean('urgent')->default(false); $t->json('addons')->nullable(); $t->string('promo_code')->nullable(); $t->text('patient_note')->nullable(); $t->text('address'); $t->enum('status',['pending','confirmed','assigned','on_the_way','in_progress','completed','cancelled'])->default('pending'); $t->json('price_breakdown'); $t->unsignedInteger('total'); $t->timestamps();
        });
        Schema::create('applications', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email'); $t->string('phone'); $t->string('profession'); $t->unsignedTinyInteger('experience_years')->default(0); $t->text('motivation')->nullable(); $t->enum('status',['new','reviewed','interview','accepted','rejected'])->default('new'); $t->timestamps();
        });
        Schema::create('reviews', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->foreignId('service_id')->nullable()->constrained()->nullOnDelete(); $t->unsignedTinyInteger('rating'); $t->text('comment'); $t->boolean('published')->default(false); $t->timestamps();
        });
    }
    public function down(): void
    {
        foreach (['reviews','applications','bookings','service_areas','promotions','customers','caregivers','services'] as $table) Schema::dropIfExists($table);
    }
};
