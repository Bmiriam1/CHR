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
        Schema::table('hosts', function (Blueprint $table) {
            // Add program_id column after company_id
            $table->foreignId('program_id')
                ->nullable()
                ->after('company_id')
                ->constrained('programs')
                ->nullOnDelete();
                
            // Add index for better performance
            $table->index(['program_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hosts', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropIndex(['program_id']);
            $table->dropColumn('program_id');
        });
    }
};
