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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 50)->unique()->comment('Format: PREFIX-LOC-YYYY-NNNNNN');
            $table->string('name', 200);
            $table->foreignId('category_id')->constrained('asset_categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('location_id')->constrained('asset_locations')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('serial_number', 100)->nullable()->unique();
            $table->string('model', 100)->nullable();
            $table->string('manufacturer', 100)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->enum('status', ['Active', 'Maintenance', 'Retired'])->default('Active');
            $table->enum('condition', ['Excellent', 'Good', 'Fair', 'Poor'])->default('Good');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->text('qr_code')->nullable()->comment('Base64 encoded QR code image');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performance
            $table->index('asset_code');
            $table->index('category_id');
            $table->index('location_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
