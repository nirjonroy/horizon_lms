<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_fees', function (Blueprint $table) {
            $table->string('syllabus_pdf')->nullable()->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('online_fees', function (Blueprint $table) {
            $table->dropColumn('syllabus_pdf');
        });
    }
};
