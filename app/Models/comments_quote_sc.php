<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comments_quote_sc extends Model
{
    use HasFactory;
    protected $fillable = [
        'paper_id',
        'sender',
        'name_sender',
        'receiver',
        'name_receiver',
        'comment_content',
    ];
}
