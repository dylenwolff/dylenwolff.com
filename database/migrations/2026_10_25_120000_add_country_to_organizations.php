<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->char('country_code', 2)->nullable()->after('industry')->index();
            $table->string('city', 120)->nullable()->after('country_code');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex(['country_code']);
            $table->dropColumn(['country_code', 'city']);
        });
    }
};
