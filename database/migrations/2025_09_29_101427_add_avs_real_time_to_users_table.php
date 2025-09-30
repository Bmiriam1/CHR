<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avs_status')->nullable();
            $table->json('avs_response')->nullable(); // store full JSON if needed
            $table->timestamp('avs_verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avs_status', 'avs_response', 'avs_verified_at']);
        });
    }
};
