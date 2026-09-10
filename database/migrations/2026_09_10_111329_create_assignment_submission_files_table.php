<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('assignment_submissions')->cascadeOnDelete();
            $table->string('kind', 20); // submission | correction
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['submission_id', 'kind', 'sort_order']);
        });

        $submissions = DB::table('assignment_submissions')->get([
            'id',
            'submission_file_path',
            'submission_file_name',
            'submission_file_type',
            'submission_file_size',
            'correction_file_path',
            'correction_file_name',
            'correction_file_type',
            'correction_file_size',
            'created_at',
            'updated_at',
        ]);

        foreach ($submissions as $submission) {
            if (!empty($submission->submission_file_path)) {
                DB::table('assignment_submission_files')->insert([
                    'submission_id' => $submission->id,
                    'kind' => 'submission',
                    'file_path' => $submission->submission_file_path,
                    'file_name' => $submission->submission_file_name,
                    'file_type' => $submission->submission_file_type,
                    'file_size' => $submission->submission_file_size ?? 0,
                    'sort_order' => 0,
                    'created_at' => $submission->created_at,
                    'updated_at' => $submission->updated_at,
                ]);
            }

            if (!empty($submission->correction_file_path)) {
                DB::table('assignment_submission_files')->insert([
                    'submission_id' => $submission->id,
                    'kind' => 'correction',
                    'file_path' => $submission->correction_file_path,
                    'file_name' => $submission->correction_file_name,
                    'file_type' => $submission->correction_file_type,
                    'file_size' => $submission->correction_file_size ?? 0,
                    'sort_order' => 0,
                    'created_at' => $submission->created_at,
                    'updated_at' => $submission->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submission_files');
    }
};
