@extends('template')

@section('page')
    Partners
@stop

@section('content')


    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered">
                <caption>User Details</caption>
                <thead>
                <tr class="active">
                    <th>Field</th>
                    <th>Content</th>
                </tr> </thead>
                <tbody>
                <tr>
                    <th scope=row>Name</th>
                    <td>{{ $user->name }} {{ $user->lastname }}</td>
                </tr>
                <tr>
                    <th scope=row>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
                <table class="table table-bordered">
                    <caption>User extensions</caption>
                    <thead>
                    <tr class="active">
                        <th>Extension</th>
                        <th>Status</th>
                        <th>PBX URL</th>
                        <th>Action</th>
                    </tr> </thead>
                    <tbody>
                    @foreach($user->extensions as $extension)
                    <tr>
                        <th scope=row>{{ $extension->extension }}</th>
                        <td>{{ $extension->status }}</td>
                        <td>{{ $extension->pbx_url }}</td>
                        <td>
                            {!! Form::open(array('url' => 'webphone/extension')) !!}

                            {{ Form::hidden('extension_id', $extension->id) }}

                            {!! Form::submit('Use extension', ['class' => 'btn btn-primary form-control']) !!}
                            {!! Form::close() !!}

                        </td>
                    </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">

        </div>
        <div class="col-md-4">

        </div>


    </div>




    {!! Form::close() !!}

    <br>

    @include('errors.form')


@stop