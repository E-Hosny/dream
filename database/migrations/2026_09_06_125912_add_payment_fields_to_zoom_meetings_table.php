<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->decimal('session_price', 10, 2)->nullable()->after('duration');
            $table->boolean('is_paid')->default(false)->after('session_price');
        });
    }

    public function down(): void
    {
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->dropColumn(['session_price', 'is_paid']);
        });
    }
};
