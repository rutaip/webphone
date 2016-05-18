<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class InstanceRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3',
            'websocket_proxy_url' => 'required|min:3',
            'ice_servers' => 'required|min:3',
            'rtcweb_breaker' => 'required|min:3',
            'video_enable' => 'required|min:3',
        ];
    }
}
