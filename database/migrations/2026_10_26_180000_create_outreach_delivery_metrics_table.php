<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outreach_delivery_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('total_sent')->default(0);
            $table->unsignedInteger('accepted')->default(0);
            $table->unsignedInteger('bounced')->default(0);
            $table->unsignedInteger('delayed')->default(0);
            $table->unsignedInteger('reputation_blocked')->default(0);
            $table->unsignedInteger('invalid_recipient')->default(0);
            $table->unsignedInteger('server_rejected')->default(0);
            $table->timestamp('measured_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outreach_delivery_metrics');
    }
};
