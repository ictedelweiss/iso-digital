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
        if (!Schema::hasColumn('meetings', 'type')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->enum('type', ['internal', 'external'])->default('internal')->after('title');
            });
        }

        if (!Schema::hasColumn('attendees', 'user_id')) {
            Schema::table('attendees', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('meeting_id');
                $table->index('user_id');
            });
        }

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
        if (Schema::hasColumn('meetings', 'type')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasColumn('attendees', 'user_id')) {
            Schema::table('attendees', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'signature_path')) {
                $table->dropColumn('signature_path');
            }
        });
    }
};
