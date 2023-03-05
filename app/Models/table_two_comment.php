<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class table_two_comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'paper_id',
        'sender',
        'comment_content',
    ];
}
