<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::table('online_fees', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('program');
        });

        // Backfill existing rows with a unique slug based on the program
        $existing = DB::table('online_fees')->select('id', 'program', 'slug')->get();
        $used = [];

        foreach ($existing as $row) {
            if (!empty($row->slug)) {
                $used[$row->slug] = true;
                continue;
            }

            $base = Str::slug($row->program ?? '') ?: 'program';
            $slug = $base;
            $suffix = 1;

            while (isset($used[$slug]) || DB::table('online_fees')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $suffix;
                $suffix++;
            }

            DB::table('online_fees')->where('id', $row->id)->update(['slug' => $slug]);
            $used[$slug] = true;
        }
    }

    public function down()
    {
        Schema::table('online_fees', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
