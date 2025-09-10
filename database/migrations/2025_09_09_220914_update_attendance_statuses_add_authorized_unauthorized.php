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
        Schema::table('attendance_records', function (Blueprint $table) {
            // Update the status enum to include new statuses
            $table->enum('status', [
                'present', 
                'late', 
                'absent_unauthorized', 
                'absent_authorized',
                'excused', 
                'on_leave', 
                'sick',
                'half_day',
                'pending_approval'
            ])->default('present')->change();

            // Add new fields for proof upload and authorization
            $table->string('proof_document_path')->nullable()
                ->comment('Path to uploaded proof document for unauthorized absence');
            $table->string('proof_document_type')->nullable()
                ->comment('Type of proof document (medical_certificate, emergency_document, etc.)');
            $table->text('proof_notes')->nullable()
                ->comment('Notes about the proof document');
            $table->timestamp('proof_uploaded_at')->nullable()
                ->comment('When proof was uploaded');
            $table->foreignId('proof_approved_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('User who approved the proof document');
            $table->timestamp('proof_approved_at')->nullable()
                ->comment('When proof was approved');
            $table->text('proof_approval_notes')->nullable()
                ->comment('Notes from proof approval');
            $table->enum('proof_status', [
                'pending',
                'approved',
                'rejected'
            ])->nullable()
                ->comment('Status of proof document review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            // Revert to original status enum
            $table->enum('status', [
                'present', 
                'late', 
                'absent', 
                'excused', 
                'on_leave', 
                'sick', 
                'half_day',
                'pending_approval'
            ])->default('present')->change();

            // Drop the new fields
            $table->dropColumn([
                'proof_document_path',
                'proof_document_type',
                'proof_notes',
                'proof_uploaded_at',
                'proof_approved_by',
                'proof_approved_at',
                'proof_approval_notes',
                'proof_status'
            ]);
        });
    }
};