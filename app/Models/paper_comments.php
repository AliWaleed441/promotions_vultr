<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paper_comments extends Model
{
    use HasFactory;
    protected $fillable = [
        'paper_id',
        'sender',
        'comment_content',
    ];
}
