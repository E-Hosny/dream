<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedInteger('prepaid_sessions')->default(0)->after('price');
        });

        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->boolean('is_prepaid')->default(false)->after('is_paid');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('prepaid_sessions');
        });

        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->dropColumn('is_prepaid');
        });
    }
};
