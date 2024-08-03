<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('reports', static function(Blueprint $table) {
            $table->unsignedBigInteger('admin_notification_id')->nullable()->after('reporter_id');
        });
    }

    public function down(): void {
        Schema::table('reports', static function(Blueprint $table) {
            $table->dropColumn('admin_notification_id');
        });
    }
};