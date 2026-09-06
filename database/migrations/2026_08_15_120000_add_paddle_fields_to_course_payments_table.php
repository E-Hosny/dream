<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_payments', function (Blueprint $table) {
            $table->string('paddle_transaction_id')->nullable()->unique()->after('description');
            $table->text('paddle_checkout_url')->nullable()->after('paddle_transaction_id');
        });

        Schema::table('course_payments', function (Blueprint $table) {
            $table->string('moyasar_invoice_id')->nullable()->change();
            $table->text('moyasar_invoice_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('course_payments', function (Blueprint $table) {
            $table->dropUnique(['paddle_transaction_id']);
            $table->dropColumn(['paddle_transaction_id', 'paddle_checkout_url']);
        });
    }
};
