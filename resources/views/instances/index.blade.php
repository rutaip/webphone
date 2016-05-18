@extends('template')

@section('page')
    Partners
@stop

@section('content')

    {!! Form::open(array('url' => 'projects')) !!}

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <caption>Instance Details</caption>
                <thead>
                <tr class="active">
                    <th>Name</th>
                    <th>Server</th>
                    <th>ICE Servers</th>
                    <th>RTCWeb Breaker</th>
                    <th>Video</th>
                </tr> </thead>
                <tbody>
                @foreach($instances as $instance)
                    <tr>
                        <th scope=row>{{ $instance->name }}</th>
                        <td>{{ $instance->websocket_proxy_url }}</td>
                        <td>{{ $instance->ice_servers }}</td>
                        <td>{{ $instance->rtcweb_breaker }}</td>
                        <td>{{ $instance->video_enable }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <br>


@stop