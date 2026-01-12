<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class onlineFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'degree_id',
        'program',
        'slug',
        'total_fee',
        'yearly',
        'duration',
        'status',
        'type',
        'short_name',
        'short_description',
        'long_description',
        'university_id',
        'link',
        'syllabus_pdf',
    ];

    public function feesCategory()
    {
        return $this->belongsTo(feesCategory::class, 'degree_id'); // Assuming 'degree_id' is the foreign key in 'online_fees' table
    }

    public function university()
    {
        return $this->belongsTo(whereToStudy::class, 'university_id');
    }
}
