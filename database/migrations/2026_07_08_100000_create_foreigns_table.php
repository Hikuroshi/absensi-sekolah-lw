<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'class_id')) {
                $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            }
            if (Schema::hasColumn('attendances', 'subject_id')) {
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            }
            if (Schema::hasColumn('attendances', 'user_id')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['user_id']);
        });
    }
}; 