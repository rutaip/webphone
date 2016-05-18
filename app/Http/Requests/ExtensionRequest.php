<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ExtensionRequest extends Request
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
            'extension' => 'required|min:3',
            'password' => 'required|min:3',
            'pbx_url' => 'required|min:3',
            'status' => 'required|min:3',
            'instance_id' => 'required',
            'user_id' => 'required',
        ];
    }
}
