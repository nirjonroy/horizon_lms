<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_fees', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('short_name');
            $table->longText('long_description')->nullable()->after('short_description');
        });
    }

    public function down(): void
    {
        Schema::table('online_fees', function (Blueprint $table) {
            $table->dropColumn(['short_description', 'long_description']);
        });
    }
};
