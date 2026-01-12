<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'text_1',
        'text_2',
        'status',
        'background_color',
        'button_one_text',
        'button_one_link',
        'button_two_text',
        'button_two_link',
    ];
}
