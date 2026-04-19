<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('where_to_studies', function (Blueprint $table) {
            $table->longText('long_description')->nullable()->after('short_description');
        });
    }

    public function down(): void
    {
        Schema::table('where_to_studies', function (Blueprint $table) {
            $table->dropColumn('long_description');
        });
    }
};
