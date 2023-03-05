<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class table_two_posts extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_title',
        'scores',
        'notes',
        'is_single',
        'year',
        'is_search',
        'attachment',
    ];

    public function package()
    {
        return $this->belongsTo(package_file::class,'user_id','user_id');
    }
}
