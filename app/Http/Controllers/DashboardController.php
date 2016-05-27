<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use Auth;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user=User::find(Auth::user()->id);

        /*if  (Gate::denies('integrations', $integrations)) {

            abort(403, 'Sorry, not allowed');
        }*/

        return view('dashboard.index', compact('user'));
    }
}
