@extends('template')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <caption>Extensions Details</caption>
                <thead>
                <tr class="active">
                    <th>User Extension</th>
                    <th>Extension</th>
                    <th>SIP URL</th>
                    <th>Password</th>
                    <th>PBX IP</th>
                    <th>Instance</th>
                </tr> </thead>
                <tbody>
                @foreach($extensions as $extension)
                    <tr>
                        <th scope=row>{{ $extension->user->name }} {{ $extension->user->lastname }}</th>
                        <td>{{ $extension->extension }}</td>
                        <td>sip:{{$extension->extension}}{!! '@' !!}{{$extension->pbx_url}}</td>
                        <td>{{ $extension->password }}</td>
                        <td>{{$extension->pbx_url}}</td>
                        <td>{{ $extension->instance->name }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop