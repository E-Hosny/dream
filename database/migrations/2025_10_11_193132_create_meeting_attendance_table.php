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
        Schema::create('meeting_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('user_type', ['teacher', 'student']); // نوع المستخدم
            $table->enum('action_type', ['join', 'leave', 'meeting_start', 'meeting_end']); // نوع الإجراء
            $table->timestamp('action_time'); // وقت الإجراء
            $table->string('ip_address')->nullable(); // عنوان IP
            $table->text('user_agent')->nullable(); // معلومات المتصفح
            $table->integer('duration_seconds')->nullable(); // مدة الحضور بالثواني (يتم حسابها عند المغادرة)
            $table->json('additional_data')->nullable(); // بيانات إضافية مثل zoom meeting ID
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('meeting_id')->references('id')->on('zoom_meetings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes للاستعلامات السريعة
            $table->index(['meeting_id', 'user_id']);
            $table->index(['user_type', 'action_type']);
            $table->index('action_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_attendance');
    }
};
