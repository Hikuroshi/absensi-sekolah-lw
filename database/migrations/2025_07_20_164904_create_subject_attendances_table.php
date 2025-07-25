<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subject_attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('summary_id')->constrained('subject_attendance_summaries')->onDelete('cascade');
            $table->foreignUuid('class_member_id')->constrained('class_member')->onDelete('cascade');
            $table->string('status'); // hadir, izin, sakit, alpa, pkl
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_attendances');
    }
};
