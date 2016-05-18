<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    protected $fillable = [
        'extension',
        'password',
        'pbx_url',
        'status',
        'instance_id',
        'user_id',
    ];

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
