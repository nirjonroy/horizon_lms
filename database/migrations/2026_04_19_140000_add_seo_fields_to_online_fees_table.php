<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_fees', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('long_description');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('keywords')->nullable()->after('meta_description');
            $table->string('canonical_url', 2048)->nullable()->after('keywords');
            $table->string('author')->nullable()->after('canonical_url');
            $table->string('publisher')->nullable()->after('author');
        });
    }

    public function down(): void
    {
        Schema::table('online_fees', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'keywords',
                'canonical_url',
                'author',
                'publisher',
            ]);
        });
    }
};
