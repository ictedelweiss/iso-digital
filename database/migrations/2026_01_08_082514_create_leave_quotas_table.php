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
        Schema::create('leave_quotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('employee_name');
            $table->string('department');
            $table->year('quota_year'); // e.g., 2026
            $table->decimal('previous_year_quota', 5, 2)->default(0)->comment('Hak cuti tahun sebelumnya');
            $table->decimal('current_year_quota', 5, 2)->default(12)->comment('Hak cuti tahun berjalan');
            $table->decimal('quota_used', 5, 2)->default(0)->comment('Cuti yang sudah diambil');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'quota_year']); // One quota per user per year

            $table->index('quota_year');
            $table->index('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_quotas');
    }
};
