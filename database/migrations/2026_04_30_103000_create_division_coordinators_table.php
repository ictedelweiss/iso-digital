<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('division_coordinators')) {
            Schema::create('division_coordinators', function (Blueprint $table) {
                $table->id();
                $table->string('department')->unique();
                $table->foreignId('user_id')->nullable()->constrained('admins')->nullOnDelete();
                $table->string('coordinator_name');
                $table->string('coordinator_email');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('division_coordinators');
    }
};
