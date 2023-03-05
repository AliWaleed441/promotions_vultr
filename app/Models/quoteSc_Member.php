<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quoteSc_Member extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'MainQuoteSc',
        'current_promotion1',
        'name1',
        'SecondQuoteSc',
        'current_promotion2',
        'name2',
        'thirdQuoteSc',
        'current_promotion3',
        'name3',
    ];
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
