<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_information', function (Blueprint $table) {
            $table->string('full_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('has_middle_name')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('city')->nullable();
            $table->string('country_code')->nullable();
            $table->string('country_of_residence')->nullable();
            $table->string('has_bachelors_degree')->nullable();
            $table->string('graduation_degree')->nullable();
            $table->string('graduation_college')->nullable();
            $table->string('graduation_month')->nullable();
            $table->string('graduation_year')->nullable();
            $table->string('graduation_marks')->nullable();
            $table->string('has_masters_degree')->nullable();
            $table->unsignedSmallInteger('work_experience_years')->nullable();
            $table->string('company_name')->nullable();
            $table->string('industry')->nullable();
            $table->string('job_role')->nullable();
            $table->string('referral_code')->nullable();
            $table->boolean('disclaimer_accepted')->default(false);
            $table->unsignedBigInteger('selected_university_id')->nullable();
            $table->string('selected_university_name')->nullable();
            $table->string('selected_program_name')->nullable();
            $table->string('selected_program_slug')->nullable();
        });
    }

    public function down()
    {
        Schema::table('student_information', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'middle_name',
                'has_middle_name',
                'date_of_birth',
                'gender',
                'city',
                'country_code',
                'country_of_residence',
                'has_bachelors_degree',
                'graduation_degree',
                'graduation_college',
                'graduation_month',
                'graduation_year',
                'graduation_marks',
                'has_masters_degree',
                'work_experience_years',
                'company_name',
                'industry',
                'job_role',
                'referral_code',
                'disclaimer_accepted',
                'selected_university_id',
                'selected_university_name',
                'selected_program_name',
                'selected_program_slug',
            ]);
        });
    }
};
