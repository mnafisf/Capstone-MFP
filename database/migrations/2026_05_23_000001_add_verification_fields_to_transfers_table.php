<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            if (! Schema::hasColumn('transfers', 'verification_status')) {
                $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->after('document_path');
            }
            if (! Schema::hasColumn('transfers', 'verification_note')) {
                $table->text('verification_note')->nullable()->after('verification_status');
            }
            if (! Schema::hasColumn('transfers', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_note');
            }
        });

        DB::table('transfers')->whereNull('verification_status')->update(['verification_status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            if (Schema::hasColumn('transfers', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
            if (Schema::hasColumn('transfers', 'verification_note')) {
                $table->dropColumn('verification_note');
            }
            if (Schema::hasColumn('transfers', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
        });
    }
};
