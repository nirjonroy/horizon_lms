<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->string('background_color')->nullable()->after('text_2');
            $table->string('button_one_text')->nullable()->after('background_color');
            $table->string('button_one_link')->nullable()->after('button_one_text');
            $table->string('button_two_text')->nullable()->after('button_one_link');
            $table->string('button_two_link')->nullable()->after('button_two_text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn([
                'background_color',
                'button_one_text',
                'button_one_link',
                'button_two_text',
                'button_two_link',
            ]);
        });
    }
};
