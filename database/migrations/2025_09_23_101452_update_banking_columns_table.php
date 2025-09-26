<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Document storage columns
            if (!Schema::hasColumn('users', 'banking_statement')) {
                $table->string('banking_statement')->nullable();
            }
            if (!Schema::hasColumn('users', 'banking_statement_verified')) {
                $table->boolean('banking_statement_verified')->default(false);
            }
            if (!Schema::hasColumn('users', 'id_document')) {
                $table->string('id_document')->nullable();
            }
            if (!Schema::hasColumn('users', 'id_verified')) {
                $table->boolean('id_verified')->default(false);
            }
            if (!Schema::hasColumn('users', 'proof_of_residence')) {
                $table->string('proof_of_residence')->nullable();
            }
            if (!Schema::hasColumn('users', 'proof_of_residence_verified')) {
                $table->boolean('proof_of_residence_verified')->default(false);
            }

            // API Banking verification columns (UPDATED)
            if (!Schema::hasColumn('users', 'banking_verification_status')) {
                $table->enum('banking_verification_status', ['pending', 'verified', 'failed'])->nullable();
            }
            if (!Schema::hasColumn('users', 'banking_verification_reference')) {
                $table->string('banking_verification_reference')->nullable();
            }
            if (!Schema::hasColumn('users', 'banking_verified_at')) {
                $table->timestamp('banking_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'account_holder_name')) {
                $table->string('account_holder_name')->nullable(); // API will populate this
            }
            
            // Additional learner info
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'physical_address')) {
                $table->text('physical_address')->nullable();
            }
            if (!Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'education_level')) {
                $table->enum('education_level', ['matric', 'diploma', 'degree', 'postgraduate', 'other'])->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'banking_statement', 'banking_statement_verified', 'id_document',
                'id_verified', 'proof_of_residence', 'proof_of_residence_verified',
                'banking_verification_status', 'banking_verification_reference', 
                'banking_verified_at', 'account_holder_name', 'date_of_birth',
                'phone_number', 'physical_address', 'emergency_contact_name',
                'emergency_contact_phone', 'education_level'
            ]);
        });
    }
};