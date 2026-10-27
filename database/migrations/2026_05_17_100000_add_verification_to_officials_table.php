<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('officials', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('email');
            $table->text('verification_note')->nullable()->after('status');
        });

        DB::table('officials')->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('officials', function (Blueprint $table) {
            $table->dropColumn(['status', 'verification_note']);
        });
    }
};
