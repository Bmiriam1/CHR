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
        Schema::table('users', function (Blueprint $table) {
            // Document storage paths
            $table->string('qualification_document')->nullable();
            $table->string('cv_document')->nullable();
            $table->string('banking_statement')->nullable();
            $table->string('id_document')->nullable();
            $table->string('proof_of_residence')->nullable();
            
            // Verification status for documents
            $table->boolean('qualification_verified')->default(false);
            $table->boolean('cv_verified')->default(false);
            $table->boolean('banking_statement_verified')->default(false);
            $table->boolean('id_verified')->default(false);
            $table->boolean('proof_of_residence_verified')->default(false);
            
            // Banking details for verification
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->enum('account_type', ['savings', 'current', 'cheque'])->nullable();
            $table->boolean('banking_verified')->default(false);
            
            // Additional learner fields
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('physical_address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->enum('education_level', ['matric', 'diploma', 'degree', 'postgraduate', 'other'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'qualification_document',
                'cv_document', 
                'banking_statement',
                'id_document',
                'proof_of_residence',
                'qualification_verified',
                'cv_verified',
                'banking_statement_verified',
                'id_verified',
                'proof_of_residence_verified',
                'bank_name',
                'account_number',
                'account_holder_name',
                'branch_code',
                'account_type',
                'banking_verified',
                'date_of_birth',
                'phone_number',
                'physical_address',
                'emergency_contact_name',
                'emergency_contact_phone',
                'education_level',
            ]);
        });
    }
};