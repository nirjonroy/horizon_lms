<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class studentInformation extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name',
        'first_name',
        'middle_name',
        'has_middle_name',
        'surname',
        'email',
        'country_code',
        'phone',
        'date_of_birth',
        'gender',
        'city',
        'nationality',
        'country_of_residence',
        'subject_of_interest',
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
        'course_and_degree',
        'course_name',
        'preferred_session',
        'referral_code',
        'comments',
        'disclaimer_accepted',
        'selected_university_id',
        'selected_university_name',
        'selected_program_name',
        'selected_program_slug',
        'last_education',
    ];
}
