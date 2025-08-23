<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->foreignId('zoom_account_id')->nullable()->constrained('zoom_accounts')->onDelete('set null')->after('course_schedule_id');
            $table->index('zoom_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->dropForeign(['zoom_account_id']);
            $table->dropColumn('zoom_account_id');
        });
    }
};
