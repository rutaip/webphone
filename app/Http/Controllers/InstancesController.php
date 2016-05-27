<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Instance;
use App\Http\Requests;
use App\Http\Requests\InstanceRequest;

class InstancesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $this->authorize('admin');
        $instances=Instance::latest()->get();

        return view('instances.index', compact('instances'));
    }

    public function create()
    {
        $this->authorize('admin');
        return view('instances.create');
    }

    public function store(InstanceRequest $request)
    {
        $this->authorize('admin');

        Instance::create($request->all());
        flash()->success('Successfully created');
        return redirect('instances');
    }
    
    public function show($id){
        $this->authorize('admin');
        $instance = Instance::findOrFail($id);
        return view('instances.show', compact('instance'));
    }

    public function edit($id)
    {
        $this->authorize('admin');
        $instance = Instance::findOrFail($id);

        return view('instances.edit', compact('instance'));
    }

    public function update(InstanceRequest $request, $id)
    {
        $this->authorize('admin');
        $instance = Instance::findOrFail($id);

        $instance->update($request->all());
        flash()->success('Successfully updated');
        return redirect('instances');
    }

    public function destroy($id)
    {
        $this->authorize('admin');
        $instance = Instance::findOrFail($id);

        $instance->delete();
        flash()->success('Successfully deleted');
        return redirect('instances');
    }
}
