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
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_id');
            $table->string('name');
            $table->string('division');
            $table->string('signature_path')->nullable();
            $table->timestamp('created_at')->nullable();

            // Foreign key constraint
            $table->foreign('meeting_id')
                ->references('id')
                ->on('meetings')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
};
