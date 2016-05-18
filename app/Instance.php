<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    protected $fillable = [
        'name',
        'websocket_proxy_url',
        'outbound_proxy',
        'ice_servers',
        'rtcweb_breaker',
        'bandwidth',
        'video_enable',
        'video_size',
    ];
}
