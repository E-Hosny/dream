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
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_schedule_id')->nullable()->constrained()->onDelete('set null');
            $table->string('zoom_meeting_id')->unique();
            $table->string('topic');
            $table->datetime('start_time');
            $table->integer('duration'); // بالدقائق
            $table->text('join_url');
            $table->text('start_url');
            $table->string('password', 10)->nullable();
            $table->enum('status', ['scheduled', 'started', 'ended', 'cancelled', 'created'])->default('created');
            $table->string('host_email');
            $table->string('meeting_type')->default('scheduled');
            $table->string('timezone')->default('Asia/Riyadh');
            $table->json('settings')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['course_id', 'start_time']);
            $table->index(['status', 'start_time']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_meetings');
    }
};
