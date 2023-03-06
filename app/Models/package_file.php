<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class package_file extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $fillable = [
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function table_one_post()
    {
        return $this->hasMany('App\Models\table_one_post','user_id','user_id');
    }

    public function table_two_post()
    {
        return $this->hasMany('App\Models\table_two_posts','user_id','user_id');
    }

    public function StepSobriety()
    {
        return $this->hasOne('App\Models\StepSobriety','user_id','user_id');
    }
}
