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
        Schema::table('referees', function (Blueprint $table) {
            if (! Schema::hasColumn('referees', 'license_type')) {
                $table->enum('license_type', ['A', 'B', 'C', 'D'])->nullable()->after('name');
            }
            if (! Schema::hasColumn('referees', 'ktp_path')) {
                $table->string('ktp_path')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('referees', 'license_certificate_path')) {
                $table->string('license_certificate_path')->nullable()->after('ktp_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referees', function (Blueprint $table) {
            if (Schema::hasColumn('referees', 'license_type')) {
                $table->dropColumn('license_type');
            }
            if (Schema::hasColumn('referees', 'ktp_path')) {
                $table->dropColumn('ktp_path');
            }
            if (Schema::hasColumn('referees', 'license_certificate_path')) {
                $table->dropColumn('license_certificate_path');
            }
        });
    }
};
