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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users');
            
            // ملف الحل المرفوع من الطالب
            $table->string('submission_file_path')->nullable();
            $table->string('submission_file_name')->nullable();
            $table->string('submission_file_type')->nullable();
            $table->bigInteger('submission_file_size')->nullable();
            $table->timestamp('submitted_at')->nullable();
            
            // ملف التصحيح المرفوع من المعلم
            $table->string('correction_file_path')->nullable();
            $table->string('correction_file_name')->nullable();
            $table->string('correction_file_type')->nullable();
            $table->bigInteger('correction_file_size')->nullable();
            $table->timestamp('corrected_at')->nullable();
            
            // التقييم (1-5 نجوم)
            $table->tinyInteger('rating')->nullable()->comment('Rating from 1 to 5 stars');
            $table->text('teacher_notes')->nullable(); // ملاحظات المعلم
            
            $table->timestamps();
            
            $table->index(['assignment_id', 'student_id']);
            $table->index(['student_id', 'submitted_at']);
            $table->unique(['assignment_id', 'student_id']); // طالب واحد لكل واجب
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
