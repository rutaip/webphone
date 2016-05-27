<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Extension;
use App\Instance;
use App\User;
use App\Http\Requests;
use App\Http\Requests\ExtensionRequest;

class ExtensionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $this->authorize('admin');

        $extensions=Extension::latest()->get();

        /*if  (Gate::denies('integrations', $integrations)) {

            abort(403, 'Sorry, not allowed');
        }*/

        return view('extensions.index', compact('extensions'));
    }

    public function create()
    {
        $this->authorize('admin');
        $instances=Instance::orderBy('id', 'asc')->lists('name','id');
        $user=User::orderBy('id', 'asc')->lists('name', 'id');

        return view('extensions.create', compact('instances', 'user'));
    }

    public function store(ExtensionRequest $request)
    {
        $this->authorize('admin');
        Extension::create($request->all());

        flash()->success('Successfully created');
        return redirect('extensions');
    }

    public function show($id){
        $this->authorize('admin');
        $extension = Extension::findOrFail($id);
        return view('extensions.show', compact('extension'));
    }

    public function edit($id)
    {
        $this->authorize('admin');
        $extension = Extension::findOrFail($id);
        $instances=Instance::orderBy('id', 'asc')->lists('name','id');
        $user=User::orderBy('id', 'asc')->lists('name', 'id');

        return view('extensions.edit', compact('extension', 'instances', 'user'));
    }

    public function update(ExtensionRequest $request, $id)
    {
        $this->authorize('admin');
        $extension = Extension::findOrFail($id);


        $extension->update($request->all());
        flash()->success('Successfully updated');
        return redirect('extensions');
    }

    public function destroy($id)
    {
        $this->authorize('admin');
        $extension = Extension::findOrFail($id);

        $extension->delete();
        flash()->success('Successfully deleted');
        return redirect('extensions');
    }
}
