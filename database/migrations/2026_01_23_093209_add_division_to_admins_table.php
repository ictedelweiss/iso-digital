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
        if (Schema::hasTable('admins') && ! Schema::hasColumn('admins', 'division')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->string('division')->nullable()->after('display_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'division')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('division');
            });
        }
    }
};
