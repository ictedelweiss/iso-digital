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
        Schema::table('meetings', function (Blueprint $table) {
            $table->enum('type', ['internal', 'external'])->default('internal')->after('title');
        });

        Schema::table('attendees', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('meeting_id');
            // Assuming 'admins' is the table for users (based on User model), but we need to check if we can reference it directly or just store ID
            // Since User model uses 'admins' table and has no autoincrement ID in default Laravel User but custom here might differ.
            // Let's check User model again. It says `protected $table = 'admins';` and `$timestamps = false;`
            // And it extends Authenticatable.
            // Usually 'id' is key. Let's add simple index for performance.
            $table->index('user_id');
        });

        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'signature_path')) {
                $table->string('signature_path')->nullable()->after('display_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('attendees', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'signature_path')) {
                $table->dropColumn('signature_path');
            }
        });
    }
};
