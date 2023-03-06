<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comments_sobriety extends Model
{
    use HasFactory;
    protected $fillable = [
        'sender',
        'name_sender',
        'comment_content',
    ];
}
