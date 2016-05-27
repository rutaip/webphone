<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Webphone;
use App\Http\Requests;
use App\Http\Requests\WebphoneExtensionRequest;
use App\Extension;
use Auth;

class WebphoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $user=Auth::user();

        return view('webphone/index', compact('user'));
    }
    
    

    public function extension(WebphoneExtensionRequest $request)
    {
        $user=Auth::user();
        $extension = Extension::findorFail($request->extension_id);

        return view('webphone/index', compact('user', 'extension'));
    }
}
