<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Recipient
            $table->string('type'); // e.g., 'lesson_booked', 'review_left', 'upcoming_reminder'

            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('set null');
            $table->string('lesson_title')->nullable(); // cache

            $table->foreignId('student_id')->nullable()->constrained()->onDelete('set null');
            $table->string('student_name')->nullable(); // cache

            $table->date('date')->nullable();
            $table->time('time')->nullable();

            $table->boolean('read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
