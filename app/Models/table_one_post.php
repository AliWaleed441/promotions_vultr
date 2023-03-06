<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class table_one_post extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'publisher',
        'search_title',
        'is_impact',
        'is_single',
        'scores',
        'year',
        'attachment',
    ];

    public function package()
    {
        return $this->belongsTo(package_file::class,'user_id','user_id');
    }

}
