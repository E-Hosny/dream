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
        Schema::create('zoom_meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zoom_meeting_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->datetime('join_time')->nullable();
            $table->datetime('leave_time')->nullable();
            $table->integer('duration')->nullable(); // بالدقائق
            $table->enum('status', ['joined', 'left', 'present', 'absent'])->default('absent');
            $table->enum('role', ['host', 'co-host', 'participant'])->default('participant');
            $table->string('device_type')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['zoom_meeting_id', 'user_id']);
            $table->index(['zoom_meeting_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_meeting_participants');
    }
};
