<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avs_bank_name')->nullable()->after('avs_verified_at');
            $table->string('avs_account_status')->nullable()->after('avs_bank_name');
            $table->string('account_type')->nullable()->after('bank_account_type'); // Add this for form compatibility
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avs_bank_name', 'avs_account_status', 'account_type']);
        });
    }
};