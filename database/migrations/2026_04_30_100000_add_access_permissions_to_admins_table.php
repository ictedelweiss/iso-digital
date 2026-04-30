<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admins') && ! Schema::hasColumn('admins', 'access_permissions')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->json('access_permissions')->nullable()->after('role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'access_permissions')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('access_permissions');
            });
        }
    }
};
