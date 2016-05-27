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
        $instances=Instance::latest()->get();

        /*if  (Gate::denies('integrations', $integrations)) {

            abort(403, 'Sorry, not allowed');
        }*/

        return view('instances.index', compact('instances'));
    }

    public function create()
    {

        return view('instances.create');
    }

    public function store(InstanceRequest $request)
    {
        /*
        if  (Gate::denies('create', $request)) {

            abort(403, 'Sorry, not allowed');
        }*/

        Instance::create($request->all());
        flash()->success('Successfully created');
        return redirect('instances');
    }
    
    public function show($id){
        $instance = Instance::findOrFail($id);
        return view('instances.show', compact('instance'));
    }

    public function edit($id)
    {
        $instance = Instance::findOrFail($id);

        return view('instances.edit', compact('instance'));
    }

    public function update(InstanceRequest $request, $id)
    {
        $instance = Instance::findOrFail($id);
/*
        if  (Gate::denies('edit', $region)) {

            abort(403, 'Sorry, not allowed');
        }
*/

        $instance->update($request->all());
  //      session()->flash('flash_message', 'Record successfully updated!');
        return redirect('instances');
    }

    public function destroy($id)
    {
        $instance = Instance::findOrFail($id);

        $instance->delete();
        //session()->flash('flash_message', 'Record successfully deleted!');
        return redirect('instances');
    }
}
