<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationComment extends Model
{
    use HasFactory;
    protected $fillable = [
        'paper_id',
        'user_id_for_paper',
        'user',
        'supervisor',
        'first_member',
        'second_member',
        'third_member',
        'forth_member',
        'fifth_member',
    ];
}
