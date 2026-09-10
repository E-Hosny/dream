<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['assignment_id', 'sort_order']);
        });

        $assignments = DB::table('assignments')
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->get(['id', 'file_path', 'file_name', 'file_type', 'file_size', 'created_at', 'updated_at']);

        foreach ($assignments as $assignment) {
            DB::table('assignment_files')->insert([
                'assignment_id' => $assignment->id,
                'file_path' => $assignment->file_path,
                'file_name' => $assignment->file_name,
                'file_type' => $assignment->file_type,
                'file_size' => $assignment->file_size ?? 0,
                'sort_order' => 0,
                'created_at' => $assignment->created_at,
                'updated_at' => $assignment->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_files');
    }
};
