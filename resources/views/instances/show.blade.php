@extends('template')

@section('page')
    Partners
@stop

@section('content')


    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered">
                <caption>Instance Details</caption>
                <thead>
                <tr class="active">
                    <th>Field</th>
                    <th>Content</th>
                </tr> </thead>
                <tbody>
                <tr>
                    <th scope=row>Name</th>
                    <td>{{ $instance->name }}</td>

                </tr>
                <tr>
                    <th scope=row>Websocket proxy URL</th>
                    <td>{{ $instance->websocket_proxy_url }}</td>
                </tr>
                <tr>
                    <th scope=row>Outbound Proxy</th>
                    <td>{{ $instance->outbound_proxy }}</td>
                </tr>
                <tr>
                    <th scope=row>Ice Servers</th>
                    <td>{{ $instance->ice_servers }}</td>
                </tr>
                <tr>
                    <th scope=row>RTCWeb Breaker</th>
                    <td>{{ $instance->rtcweb_breaker }}</td>
                </tr>
                <tr>
                    <th scope=row>Bandwidth</th>
                    <td>{{ $instance->bandwidth }}</td>
                </tr>
                <tr>
                    <th>Enable Video</th>
                    <td>{{ $instance->video_enable }}</td>
                </tr>
                <tr>
                    <th>Video Size</th>
                    <td>{{ $instance->video_size }}</td>
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
            {!! Html::link('instances/'.$instance->id.'/edit', 'Edit', ['class' => 'btn btn-primary form-control']) !!}
            {{ Form::open(array('route' => array('instances.destroy', $instance->id), 'method' => 'delete')) }}
            {!! Form::submit('Delete', ['class' => 'btn btn-primary form-control']) !!}
            {{ Form::close() }}
        </div>


    </div>
    <br>

    @include('errors.form')


@stop