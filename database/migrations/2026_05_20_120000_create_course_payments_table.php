<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('amount');
            $table->string('amount_format')->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->string('description');
            $table->string('moyasar_invoice_id')->unique();
            $table->text('moyasar_invoice_url');
            $table->enum('status', ['initiated', 'paid', 'failed', 'expired', 'canceled'])->default('initiated');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'course_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_payments');
    }
};
