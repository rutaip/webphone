@extends('template')

@section('page')
    Extensions
@stop

@section('content')

    {!! Form::open(array('url' => 'extensions')) !!}

    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered">
                <caption>Extensions Details</caption>
                <thead>
                <tr class="active">
                    <th>Field</th>
                    <th>Content</th>
                </tr> </thead>
                <tbody>
                <tr>
                    <th scope=row>{!! Form::label('extension', 'Extension Number') !!}
                        <a href="#" data-toggle="popover" data-trigger="focus" title="Extension Number" data-content="The extension number">
                            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                        </a>
                    </th>
                    <td>{!! Form::text('extension', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => '6001']) !!}</td>

                </tr>
                <tr>
                    <th scope=row>{!! Form::label('password', 'Extension Password') !!}</th>
                    <td>{!! Form::text('password', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'extensionpassword']) !!}</td>
                </tr>
                <tr>
                    <th scope=row>{!! Form::label('pbx_url', 'IP PBX Address') !!}</th>
                    <td>{!! Form::text('pbx_url', null, ['class' => 'form-control', 'placeholder' => 'pbx.rutaip.net']) !!}</td>
                </tr>
                <tr>
                    <th scope=row>{!! Form::label('status', 'Status') !!}</th>
                    <td>{!! Form::select('status', ['active' => 'Active', 'inactive' => 'Inactive'], old('instance_id'),['class' => 'form-control', 'required' => 'required']) !!}</td>
                </tr>
                <tr>
                    <th scope=row>{!! Form::label('instance_id', 'Instance') !!}</th>
                    <td>{!! Form::select('instance_id', $instances, old('instance_id'),['class' => 'form-control', 'required' => 'required']) !!}</td>
                </tr>
                <tr>
                    <th scope=row>{!! Form::label('user_id', 'User') !!}</th>
                    <td>{!! Form::select('user_id', $user, old('user_id'),['class' => 'form-control', 'required' => 'required']) !!}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">

        </div>
        <div class="col-md-4">

            {!! Form::submit('Submit', ['class' => 'btn btn-primary form-control']) !!}
        </div>


    </div>




    {!! Form::close() !!}

    <br>

    @include('errors.form')


@stop