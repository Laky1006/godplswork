<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('lessons', function (Blueprint $table) {
        $table->string('phone')->nullable();
        $table->string('banner')->nullable(); // Path to banner image
    });
}

public function down(): void
{
    Schema::table('lessons', function (Blueprint $table) {
        $table->dropColumn(['phone', 'banner', 'available_slots']);
    });
}

};
